/* ================= منصة المعلم الليبي — لوحة التحكم ================= */
/* كل البيانات تُحفظ في localStorage داخل متصفح المستخدم */

const $ = (id) => document.getElementById(id);
const store = {
  get(k, d) { try { return JSON.parse(localStorage.getItem('ltp_' + k)) ?? d; } catch { return d; } },
  set(k, v) { localStorage.setItem('ltp_' + k, JSON.stringify(v)); }
};

const SCHEMA = 2;
function freshDb() {
  return {
    v: SCHEMA, users: [], students: [], teachers: [], subjects: [], preps: [], exams: [], grades: [],
    attendance: [], payments: [], followups: [], messages: [], library: [],
    training: [], courses: [], community: [], schedule: [], todos: [], notifications: [],
    activity: [], announcements: [],
    settings: { school: '', city: '', phone: '', email: '' },
    profile: { name: '', email: '', phone: '', spec: '' }
  };
}
let db = store.get('db');
if (!db || db.v !== SCHEMA) { db = freshDb(); store.set('db', db); }
else {
  db.settings = db.settings || { school: '', city: '', phone: '', email: '' };
  db.courses = db.courses || [];
  db.activity = db.activity || [];
  db.announcements = db.announcements || [];
}
let session = store.get('session', null);

function save() { store.set('db', db); }
function uid() { return Date.now() + Math.floor(Math.random() * 1000); }
function hashPw(s) { let h = 5381; for (let i = 0; i < s.length; i++) h = ((h << 5) + h + s.charCodeAt(i)) | 0; return 'h' + Math.abs(h); }
function me() { return db.users.find(u => u.id === session) || null; }
function isOwner() { const u = me(); return u && u.role === 'owner'; }
function stars(n) { const v = Math.round(n || 0); return '⭐'.repeat(v) + '☆'.repeat(5 - v); }

/* ================= عزل البيانات: كل معلم يرى بياناته فقط ================= */
function isOwnerUserId(id) { const u = db.users.find(x => x.id === id); return !!u && u.role === 'owner'; }
function visible(list) { return isOwner() ? list : list.filter(x => x.by === session || isOwnerUserId(x.by)); }
function canTouch(x) { return isOwner() || x.by === session; }

/* ================= سجل النشاط ================= */
function logAct(action, detail, whoOverride) {
  db.activity = db.activity || [];
  db.activity.unshift({
    id: uid(), who: whoOverride || me()?.name || 'زائر',
    role: isOwner() ? '👑 مالك' : (me() ? '👨‍🏫 معلم' : '—'),
    action, detail: detail || '', time: new Date().toLocaleString('ar-LY')
  });
  if (db.activity.length > 300) db.activity.length = 300;
  save();
}

/* ================= الصلاحيات ================= */
const ownerViews = ['teachers', 'payments', 'reports', 'settings', 'activity', 'announce'];
function applyRole() {
  const owner = isOwner();
  document.querySelectorAll('[data-owner]').forEach(el => el.classList.toggle('owner-hide', !owner));
}
function paintAvatar() {
  const u = me(); if (!u) return;
  const html = u.avatar ? `<img src="${u.avatar}" alt="" style="width:100%;height:100%;object-fit:cover">` : esc((u.name || 'م')[0]);
  $('uAv').innerHTML = html;
  const p = $('pfAvatarImg'); if (p) p.innerHTML = html;
}

/* ================= الحسابات: إعداد المالك / دخول / خروج ================= */
function showAuthPart(part) {
  $('authScreen').style.display = 'flex';
  $('appRoot').style.display = 'none';
  ['setup', 'login', 'register'].forEach(p => $('auth-' + p)?.classList.toggle('hidden', p !== part));
  $('authTabs')?.classList.toggle('hidden', part === 'setup');
  $('authSubtitle').textContent = part === 'setup' ? 'أنشئ حساب المالك للبدء' : part === 'register' ? 'حساب معلم جديد' : 'سجّل الدخول إلى حسابك';
  ['suMsg', 'lgMsg', 'rgMsg'].forEach(id => { const el = $(id); if (el) { el.textContent = ''; el.style.color = ''; } });
}
function enterApp() {
  $('authScreen').style.display = 'none';
  $('appRoot').style.display = '';
  const u = me();
  applyRole();
  paintAvatar();
  $('uName').textContent = u.name;
  $('uRole').textContent = isOwner() ? '👑 مالك المنصة' : '👨‍🏫 معلم';
  $('schoolLine').textContent = db.settings?.school || 'لوحة التحكم';
  if (!db.profile.name) {
    db.profile = { ...db.profile, name: u.name, email: u.email, spec: u.spec || '' };
    save();
  }
  go('dashboard');
}
function bootAuth() {
  const ownerExists = db.users.some(u => u.role === 'owner');
  if (!ownerExists) showAuthPart('setup');
  else if (session && me()) enterApp();
  else { session = null; showAuthPart('login'); }
}

document.querySelectorAll('[data-auth]').forEach(b => b.addEventListener('click', () => {
  document.querySelectorAll('[data-auth]').forEach(x => { x.classList.remove('btn-primary'); x.classList.add('btn-ghost'); });
  b.classList.add('btn-primary'); b.classList.remove('btn-ghost');
  showAuthPart(b.dataset.auth);
}));

$('suBtn')?.addEventListener('click', () => {
  const name = $('suName').value.trim(), email = $('suEmail').value.trim().toLowerCase(), pass = $('suPass').value;
  if (!name || !email || !pass) return $('suMsg').textContent = 'أكمل جميع الحقول';
  if (pass.length < 6) return $('suMsg').textContent = 'كلمة المرور 6 أحرف على الأقل';
  if (!/^\S+@\S+\.\S+$/.test(email)) return $('suMsg').textContent = 'بريد إلكتروني غير صحيح';
  if (db.users.some(u => u.email === email)) return $('suMsg').textContent = 'البريد مستخدم مسبقاً';
  const u = { id: uid(), name, email, pass: hashPw(pass), role: 'owner' };
  db.users.push(u);
  session = u.id; store.set('session', session);
  db.notifications.push({ id: uid(), title: '🎉 أهلاً بك يا ' + name, text: 'أنشأت حساب المالك. يمكنك الآن إضافة المعلمين من قسم «المعلمون».', read: false });
  save(); logAct('إنشاء حساب مالك', email); enterApp(); toast('🎉 تم إنشاء حساب المالك');
});

$('lgBtn')?.addEventListener('click', () => {
  const email = $('lgEmail').value.trim().toLowerCase(), pass = $('lgPass').value;
  const u = db.users.find(x => x.email === email);
  if (!u || u.pass !== hashPw(pass)) return $('lgMsg').textContent = 'البريد أو كلمة المرور غير صحيحة';
  if (u.status === 'pending') return $('lgMsg').textContent = '⏳ حسابك بانتظار موافقة المالك قبل الدخول';
  if (u.status === 'suspended') return $('lgMsg').textContent = '⛔ حسابك معلّق — تواصل مع مالك المنصة';
  session = u.id; store.set('session', session);
  $('lgPass').value = '';
  logAct('تسجيل دخول', u.name + ' — ' + email);
  enterApp(); toast('👋 أهلاً ' + u.name);
});

$('rgBtn')?.addEventListener('click', () => {
  const name = $('rgName').value.trim(), email = $('rgEmail').value.trim().toLowerCase(), pass = $('rgPass').value, spec = $('rgSpec').value.trim();
  if (!name || !email || !pass) return $('rgMsg').textContent = 'أكمل جميع الحقول';
  if (pass.length < 6) return $('rgMsg').textContent = 'كلمة المرور 6 أحرف على الأقل';
  if (!/^\S+@\S+\.\S+$/.test(email)) return $('rgMsg').textContent = 'بريد إلكتروني غير صحيح';
  if (db.users.some(u => u.email === email)) return $('rgMsg').textContent = 'البريد مستخدم مسبقاً';
  const u = { id: uid(), name, email, pass: hashPw(pass), role: 'teacher', spec, status: 'pending', joined: new Date().toLocaleDateString('ar-LY') };
  db.users.push(u);
  const owner = db.users.find(x => x.role === 'owner');
  if (owner) db.notifications.push({ id: uid(), uid: owner.id, title: '📝 طلب انضمام جديد', text: name + ' (' + (spec || 'معلم') + ') ينتظر موافقتك — من قسم «المعلمون».', read: false });
  save();
  logAct('طلب انضمام', name + ' — ' + email, name);
  $('rgMsg').textContent = '✅ تم إنشاء الحساب — بانتظار موافقة المالك قبل تسجيل الدخول';
  $('rgMsg').style.color = '#0d9488';
  toast('⏳ تم إرسال الطلب للمالك');
});

$('logoutBtn')?.addEventListener('click', () => {
  logAct('تسجيل خروج', me()?.name || '');
  session = null; store.set('session', null);
  ['lgEmail', 'lgPass'].forEach(id => { const el = $(id); if (el) el.value = ''; });
  showAuthPart('login'); toast('👋 تم تسجيل الخروج');
});

function toast(text, ok = true) {
  const t = $('toast');
  t.textContent = text;
  t.style.background = ok ? '#0f766e' : '#e11d48';
  t.style.display = 'block';
  clearTimeout(t._t);
  t._t = setTimeout(() => t.style.display = 'none', 2600);
}

/* ================= التنقل بين الأقسام ================= */
const titles = {
  dashboard: 'لوحة التحكم', students: 'إدارة الطلاب', teachers: 'إدارة المعلمين', subjects: 'المواد',
  prep: 'التحضير الذكي', ai: 'مساعد AI', exams: 'الاختبارات', grades: 'سجل الدرجات',
  attendance: 'الحضور والغياب', payments: 'المدفوعات', followups: 'المتابعة',
  messages: 'الرسائل', library: 'المكتبة', training: 'التدريب', community: 'المجتمع',
  schedule: 'الجدول', notifications: 'الإشعارات', profile: 'الملف الشخصي',
  reports: 'التقارير', settings: 'الإعدادات',
  activity: 'سجل النشاط', announce: 'الإعلانات'
};

function go(view) {
  if (ownerViews.includes(view) && !isOwner()) {
    toast('⛔ هذا القسم متاح لمالك المنصة فقط', false);
    view = 'dashboard';
  }
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  $('view-' + view)?.classList.add('active');
  document.querySelector(`.nav-item[data-view="${view}"]`)?.classList.add('active');
  $('pageTitle').textContent = titles[view] || 'لوحة التحكم';
  if (window.innerWidth < 768) { $('sidebar').classList.add('translate-x-full'); }
  render(view);
}

document.querySelectorAll('.nav-item').forEach(b => b.addEventListener('click', () => go(b.dataset.view)));
document.querySelectorAll('[data-goto]').forEach(b => b.addEventListener('click', () => go(b.dataset.goto)));

$('menuBtn')?.addEventListener('click', () => $('sidebar').classList.toggle('translate-x-full'));

/* ================= الوضع الليلي ================= */
const themeBtn = $('themeBtn');
function applyTheme(t) {
  document.documentElement.classList.toggle('dark', t === 'dark');
  if (themeBtn) themeBtn.textContent = t === 'dark' ? '☀️' : '🌙';
}
applyTheme(localStorage.getItem('ltp_theme') || 'light');
themeBtn?.addEventListener('click', () => {
  const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
  localStorage.setItem('ltp_theme', next);
  applyTheme(next);
});

/* ================= التوجيهات ================= */
function renderAI() { /* قسم ثابت — لا يحتاج عرضاً ديناميكياً */ }
function render(view) {
  const r = {
    dashboard: renderDashboard, students: renderStudents, teachers: renderTeachers,
    subjects: renderSubjects, prep: renderPreps, ai: renderAI, exams: renderExams,
    grades: renderGrades, attendance: renderAttendance, payments: renderPayments,
    followups: renderFollowups, messages: renderMessages, library: renderLibrary,
    training: renderTraining, community: renderCommunity, schedule: renderSchedule,
    notifications: renderNotifications, profile: renderProfile,
    reports: renderReports, settings: renderSettings,
    activity: renderActivity, announce: renderAnnounce
  }[view];
  if (r) r();
}

/* ================= لوحة التحكم ================= */
function renderDashboard() {
  $('dashName').textContent = (me()?.name) || db.profile.name || 'معلمنا';
  $('kpiStudents').textContent = visible(db.students).length;
  $('kpiPreps').textContent = visible(db.preps).length;
  $('kpiExams').textContent = visible(db.exams).length;
  $('kpiAvg').textContent = (avgGrade()) + '%';
  $('kpiAtt').textContent = overallAtt() + '%';
  $('kpiRev').textContent = visible(db.preps).reduce((a, p) => a + (p.reviews || []).length, 0);

  const box = $('dashTodos');
  box.innerHTML = visible(db.todos).length ? '' : '<div class="empty" style="padding:1rem">لا توجد مهام بعد — أضف مهمة!</div>';
  db.todos.forEach((t, i) => {
    if (!canTouch(t)) return;
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 p-2 rounded-lg';
    div.style.background = '#f9fafb';
    div.innerHTML = `<input type="checkbox" ${t.done ? 'checked' : ''} class="accent-teal-600">
      <span style="flex:1;${t.done ? 'text-decoration:line-through;opacity:.5' : ''}">${esc(t.text)}</span>
      <button class="text-red-400 hover:text-red-600" data-del="${i}">✕</button>`;
    div.querySelector('input').addEventListener('change', e => { t.done = e.target.checked; save(); renderDashboard(); });
    div.querySelector('[data-del]').addEventListener('click', () => { db.todos.splice(i, 1); save(); renderDashboard(); });
    box.appendChild(div);
  });
}
$('todoAdd')?.addEventListener('click', () => {
  const v = $('todoInput').value.trim();
  if (!v) return toast('اكتب المهمة أولاً', false);
  db.todos.push({ text: v, done: false, by: session }); $('todoInput').value = ''; save(); renderDashboard(); toast('✅ تمت إضافة المهمة');
});

function avgGrade() {
  const gs = visible(db.grades);
  if (!gs.length) return 0;
  const sum = gs.reduce((a, g) => a + (Number(g.work) + Number(g.quiz) + Number(g.exam)), 0);
  return Math.round(sum / gs.length);
}
function overallAtt() {
  const recs = visible(db.attendance);
  if (!recs.length) return 100;
  const present = recs.filter(r => r.status === 'present' || r.status === 'late').length;
  return Math.round(present / recs.length * 100);
}
function esc(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }
function gradeLabel(total) {
  if (total >= 85) return ['ممتاز', '#0d9488'];
  if (total >= 75) return ['جيد جداً', '#0ea5e9'];
  if (total >= 65) return ['جيد', '#f59e0b'];
  if (total >= 50) return ['مقبول', '#d97706'];
  return ['يحتاج دعماً', '#e11d48'];
}

/* ================= الطلاب ================= */
function renderStudents() {
  const q = ($('stSearch')?.value || '').trim();
  const body = $('stBody');
  const list = visible(db.students).filter(s => !q || s.name.includes(q));
  body.innerHTML = list.length ? '' : '<tr><td colspan="7" class="text-center" style="padding:2rem">لا يوجد طلاب — اضغط «إضافة طالب»</td></tr>';
  let n = 0;
  list.forEach(s => {
    n++;
    const att = attOf(s.name);
    const gr = gradeOf(s.name);
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${n}</td><td class="font-bold">${esc(s.name)}</td><td>${esc(s.grade)}</td>
      <td>${esc(s.parent)}</td><td>${att}%</td><td>${gr.total}% <span class="pill" style="color:${gr.color}">${gr.label}</span></td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
      if (!canTouch(s)) return toast('هذا الطالب ليس من بياناتك', false);
      if (confirm('حذف الطالب؟')) { db.students = db.students.filter(x => x.id !== s.id); save(); renderStudents(); }
    });
    body.appendChild(tr);
  });
}
$('stSearch')?.addEventListener('input', renderStudents);
$('addStudent')?.addEventListener('click', () => { $('stForm').style.display = $('stForm').style.display === 'none' ? 'block' : 'none'; });
$('stCancel')?.addEventListener('click', () => $('stForm').style.display = 'none');
$('stSave')?.addEventListener('click', () => {
  const name = $('st_name').value.trim();
  if (!name) return toast('اكتب اسم الطالب', false);
  db.students.push({ id: uid(), name, grade: $('st_grade').value, parent: $('st_parent').value.trim(), by: session });
  $('st_name').value = ''; $('st_parent').value = '';
  save(); $('stForm').style.display = 'none'; renderStudents(); toast('✅ تمت إضافة الطالب');
});

function attOf(name) {
  const recs = visible(db.attendance).filter(a => a.student === name);
  if (!recs.length) return 100;
  const present = recs.filter(r => r.status === 'present' || r.status === 'late').length;
  return Math.round(present / recs.length * 100);
}
function gradeOf(name) {
  const gs = visible(db.grades).filter(g => g.student === name);
  if (!gs.length) return { total: 0, label: '—', color: '#6b7280' };
  const total = Math.round(gs.reduce((a, g) => a + Number(g.work) + Number(g.quiz) + Number(g.exam), 0) / gs.length);
  const [label, color] = gradeLabel(total);
  return { total, label, color };
}

/* ================= إدارة حسابات المعلمين (للمالك) ================= */
function statusPill(st) {
  st = st || 'active';
  if (st === 'pending') return '<span class="pill" style="color:#d97706">⏳ بانتظار الموافقة</span>';
  if (st === 'suspended') return '<span class="pill" style="color:#e11d48">⏸ معلّق</span>';
  return '<span class="pill" style="color:#0d9488">✔ نشط</span>';
}
function renderTeachers() {
  if (!isOwner()) return;
  const list = db.users.filter(u => u.role === 'teacher');
  const c = {
    active: list.filter(u => (u.status || 'active') === 'active').length,
    pending: list.filter(u => u.status === 'pending').length,
    suspended: list.filter(u => u.status === 'suspended').length
  };
  $('tcStats').innerHTML = `
    <span class="pill" style="color:#0d9488">✔ نشطون: ${c.active}</span>
    <span class="pill" style="color:#d97706">⏳ بانتظار الموافقة: ${c.pending}</span>
    <span class="pill" style="color:#e11d48">⏸ معلّقون: ${c.suspended}</span>
    <span class="pill">الكل: ${list.length}</span>`;
  const body = $('tcBody');
  body.innerHTML = list.length ? '' : '<tr><td colspan="6" class="text-center" style="padding:2rem">لا توجد حسابات معلمين — اضغط «إضافة حساب معلم»</td></tr>';
  list.forEach((u, i) => {
    const st = u.status || 'active';
    let actions = '';
    if (st === 'pending') actions += `<button class="btn btn-primary btn-sm" data-approve="${i}">✔ موافقة</button> `;
    if (st === 'active') actions += `<button class="btn btn-ghost btn-sm" data-suspend="${i}">⏸ تعليق</button> `;
    if (st === 'suspended') actions += `<button class="btn btn-primary btn-sm" data-approve="${i}">▶️ تفعيل</button> `;
    actions += `<button class="btn btn-ghost btn-sm" data-reset="${i}">🔑 كلمة مرور</button>
      <button class="btn btn-ghost btn-sm" data-del="${i}" style="color:#e11d48">🗑️</button>`;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(u.name)}</td><td>${esc(u.email)}</td>
      <td>${esc(u.spec || '—')}</td><td>${statusPill(st)}</td><td><div class="flex gap-1 flex-wrap">${actions}</div></td>`;
    tr.querySelector('[data-approve]')?.addEventListener('click', () => {
      u.status = 'active';
      db.notifications.push({ id: uid(), uid: u.id, title: '✅ تمت الموافقة على حسابك', text: 'مرحباً ' + u.name + '، يمكنك الآن تسجيل الدخول واستخدام المنصة.', read: false });
      save(); logAct('موافقة على حساب', u.name + ' — ' + u.email); renderTeachers(); toast('✔ تمت الموافقة على حساب ' + u.name);
    });
    tr.querySelector('[data-suspend]')?.addEventListener('click', () => {
      if (!confirm('تعليق حساب ' + u.name + '؟ لن يتمكن من الدخول.')) return;
      u.status = 'suspended'; save(); logAct('تعليق حساب', u.name + ' — ' + u.email); renderTeachers(); toast('⏸ تم تعليق الحساب');
    });
    tr.querySelector('[data-reset]')?.addEventListener('click', () => {
      const np = prompt('كلمة المرور الجديدة لـ ' + u.name + ' (6 أحرف على الأقل):');
      if (!np) return;
      if (np.length < 6) return toast('كلمة المرور 6 أحرف على الأقل', false);
      u.pass = hashPw(np); save(); logAct('إعادة تعيين كلمة مرور', u.name + ' — ' + u.email); toast('🔑 تم إعادة تعيين كلمة المرور');
    });
    tr.querySelector('[data-del]')?.addEventListener('click', () => {
      if (u.id === session) return toast('لا يمكنك حذف حسابك الحالي', false);
      if (!confirm('حذف حساب ' + u.name + ' نهائياً؟')) return;
      logAct('حذف حساب', u.name + ' — ' + u.email);
      db.users = db.users.filter(x => x.id !== u.id);
      save(); renderTeachers(); toast('🗑️ تم حذف الحساب');
    });
    body.appendChild(tr);
  });
}
$('addTeacher')?.addEventListener('click', () => { $('tcForm').style.display = $('tcForm').style.display === 'none' ? 'block' : 'none'; });
$('tcCancel')?.addEventListener('click', () => $('tcForm').style.display = 'none');
$('tcSave')?.addEventListener('click', () => {
  if (!isOwner()) return toast('⛔ الإضافة متاحة للمالك فقط', false);
  const name = $('tc_name').value.trim(), email = $('tc_email').value.trim().toLowerCase(), pass = $('tc_pass').value;
  if (!name || !email || !pass) return toast('أكمل جميع الحقول', false);
  if (pass.length < 6) return toast('كلمة المرور 6 أحرف على الأقل', false);
  if (!/^\S+@\S+\.\S+$/.test(email)) return toast('بريد إلكتروني غير صحيح', false);
  if (db.users.some(u => u.email === email)) return toast('البريد مستخدم مسبقاً', false);
  db.users.push({
    id: uid(), name, email, pass: hashPw(pass), role: 'teacher',
    spec: $('tc_spec').value.trim(), status: 'active', joined: new Date().toLocaleDateString('ar-LY')
  });
  $('tc_name').value = ''; $('tc_email').value = ''; $('tc_pass').value = ''; $('tc_spec').value = '';
  save(); logAct('إنشاء حساب معلم', name + ' — ' + email); $('tcForm').style.display = 'none'; renderTeachers(); toast('✅ تم إنشاء حساب المعلم');
});

/* ================= المواد ================= */
function renderSubjects() {
  const list = visible(db.subjects);
  const box = $('sbBox');
  box.innerHTML = list.length ? '' : '<div class="empty" style="grid-column:1/-1">لا توجد مواد بعد — اضغط «إضافة مادة» لإضافة أول مادة</div>';
  list.forEach((s, i) => {
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `<div class="text-2xl">📚</div>
      <h3 class="font-black mt-2">${esc(s.name)}</h3>
      <p class="text-xs text-gray-500 mt-1">${esc(s.desc || '')}</p>
      <span class="pill">${esc(s.stage)}</span>
      <button class="btn btn-ghost btn-sm mt-2">🗑️</button>`;
    card.querySelector('button')?.addEventListener('click', () => {
      if (!canTouch(s)) return toast('هذه المادة ليست من بياناتك', false);
      db.subjects = db.subjects.filter(x => x.id !== s.id); save(); renderSubjects();
    });
    box.appendChild(card);
  });
}
$('addSubject')?.addEventListener('click', () => { $('sbForm').style.display = $('sbForm').style.display === 'none' ? 'block' : 'none'; });
$('sbCancel')?.addEventListener('click', () => $('sbForm').style.display = 'none');
$('sbSave')?.addEventListener('click', () => {
  const name = $('sb_name').value.trim();
  if (!name) return toast('اكتب اسم المادة', false);
  db.subjects.push({ id: uid(), name, stage: $('sb_stage').value, desc: $('sb_desc').value.trim(), by: session });
  $('sb_name').value = ''; $('sb_desc').value = '';
  save(); $('sbForm').style.display = 'none'; renderSubjects(); toast('✅ تمت إضافة المادة');
});

/* ================= التحضير الذكي ================= */
function buildPrep(title, subject, grade) {
  return `<h4 style="color:#115e59;font-weight:900;margin-bottom:.5rem">📝 تحضير درس: ${title}</h4>
<b>المادة:</b> ${subject} | <b>الصف:</b> ${grade || '—'}

🎯 الأهداف المعرفية:
1. أن يتعرّف الطالب على مفهوم ${title}.
2. أن يشرح الطالب أفكار ${title} بلغته.
3. أن يعدّد الطالب عناصر ${title} الرئيسية.

🛠️ الأهداف المهارية:
1. أن يطبّق الطالب ما تعلمه في تمارين على ${title}.
2. أن يحل الطالب مسائل مشابهة بشكل مستقل.

❤️ الأهداف الوجدانية:
1. أن يُقدّر الطالب أهمية ${title} في حياته.
2. أن يتعاون مع زملائه أثناء الأنشطة.

🎬 التمهيد: يبدأ المعلم بسؤال محفّز مرتبط بحياة الطلاب عن ${title}، ثم يعرض صورة أو موقفاً واقعياً لجذب الانتباه وإثارة الفضول.

📖 العرض:
1. تقديم المفهوم الأساسي لـ ${title} بشرح مبسط.
2. عرض أمثلة توضيحية محلولة خطوة بخطوة.
3. نشاط جماعي: تقسيم الطلاب لمجموعات وتكليفهم بتطبيق عملي.
4. تدعيم بالمثال والتصحيح الفوري.

🧩 الاستراتيجيات: التعلم التعاوني • العصف الذهني • التعلم بالاكتشاف.

🖥️ الوسائل: السبورة، عرض تقديمي، بطاقات عمل، وسائل بصرية.

✅ التقويم: أسئلة شفوية سريعة + بطاقة خروج: «ما أهم شيء تعلمته اليوم؟».

📚 الواجب: حل التمارين المرتبطة بـ ${title} من الكتاب المقرر.`;
}
function prepAvg(p) {
  const rs = p.reviews || [];
  return rs.length ? rs.reduce((a, r) => a + r.score, 0) / rs.length : 0;
}
function renderPreps() {
  const box = $('prepSavedBox');
  box.innerHTML = visible(db.preps).length ? '' : '<div class="empty" style="padding:1rem">لا توجد تحاضير محفوظة بعد</div>';
  db.preps.forEach((p, i) => {
    if (!canTouch(p)) return;
    const avg = prepAvg(p);
    const st = p.status === 'published'
      ? '<span class="pill" style="color:#0d9488">✔ منشور</span>'
      : '<span class="pill" style="color:#d97706">مسودة</span>';
    const div = document.createElement('div');
    div.className = 'p-3 rounded-xl flex items-center gap-2 flex-wrap';
    div.style.background = '#f9fafb';
    div.innerHTML = `<span>📝</span><span style="flex:1;min-width:180px"><b>${esc(p.title)}</b> — ${esc(p.subject)}<br>
        <small class="text-gray-500">${esc(p.date)}</small> ${st}<br>
        <span class="text-xs">${stars(avg)} ${avg ? avg.toFixed(1) + '/5' : 'بدون مراجعة'}</span></span>
      <button class="btn btn-ghost btn-sm" data-open="${i}">👁️ معاينة</button>
      <button class="btn btn-gold btn-sm" data-rev="${i}">⭐ مراجعة</button>
      <button class="btn btn-ghost btn-sm" data-share="${i}">📤 مشاركة</button>
      <button class="btn btn-ghost btn-sm" data-del="${i}">🗑️</button>`;
    div.querySelector('[data-open]').addEventListener('click', () => openPrep(i));
    div.querySelector('[data-rev]').addEventListener('click', () => openReview(i));
    div.querySelector('[data-share]').addEventListener('click', () => sharePrep(i));
    div.querySelector('[data-del]').addEventListener('click', () => {
      if (!canTouch(p)) return toast('هذا التحضير ليس من بياناتك', false);
      if (!confirm('حذف التحضير؟')) return;
      db.preps.splice(i, 1); save(); renderPreps();
    });
    box.appendChild(div);
  });
  renderRevLog();
}
function sharePrep(i) {
  const p = db.preps[i]; if (!p) return;
  db.community.push({
    id: uid(), author: (me()?.name) || 'معلم',
    text: `📝 ${p.title} (${p.subject})\n` + String(p.content || '').replace(/<[^>]+>/g, '').slice(0, 260) + '…',
    likes: 0, comments: 0, date: new Date().toLocaleDateString('ar-LY'), by: session
  });
  save(); toast('📤 تمت مشاركة التحضير في المجتمع');
}
function renderRevLog() {
  const box = $('revBox'); if (!box) return;
  const rows = [];
  db.preps.forEach((p, pi) => {
    if (!canTouch(p)) return;
    (p.reviews || []).forEach((r, ri) => rows.push({ p, r, pi, ri }));
  });
  box.innerHTML = rows.length ? '' : '<div class="empty" style="padding:1rem">لا توجد مراجعات بعد — اضغط ⭐ مراجعة أمام أي درس</div>';
  rows.forEach(({ p, r, pi, ri }) => {
    const div = document.createElement('div');
    div.className = 'p-3 rounded-xl flex items-start gap-2';
    div.style.background = '#f9fafb';
    div.innerHTML = `<span>⭐</span>
      <div style="flex:1"><b>${esc(p.title)}</b> — <span class="text-xs">${stars(r.score)} ${r.score}/5</span><br>
        ${r.comment ? `<span class="text-gray-600">${esc(r.comment)}</span><br>` : ''}
        <small class="text-gray-500">بواسطة: ${esc(r.by || '—')} • ${esc(r.date)}</small></div>
      <button class="btn btn-ghost btn-sm">🗑️</button>`;
    div.querySelector('button').addEventListener('click', () => {
      if (!isOwner() && r.uid !== session) return toast('هذه المراجعة ليست من إنشائك', false);
      db.preps[pi].reviews.splice(ri, 1); save(); renderPreps();
    });
    box.appendChild(div);
  });
}

/* ---- نافذة المعاينة ---- */
let pmIdx = -1, revIdx = -1, revScore = 0;
function paintRevStars() {
  document.querySelectorAll('#revStars [data-star]').forEach(s => {
    s.textContent = Number(s.dataset.star) <= revScore ? '⭐' : '☆';
  });
}
function openPrep(i) {
  const p = db.preps[i]; if (!p) return;
  pmIdx = i;
  $('pmTitle').textContent = '📝 ' + p.title;
  $('pmStatus').innerHTML = p.status === 'published'
    ? '<span class="pill" style="color:#0d9488">✔ منشور</span>'
    : '<span class="pill" style="color:#d97706">مسودة</span>';
  $('pmBody').innerHTML = esc(p.content).replace(/\n/g, '<br>');
  $('pmPublish').textContent = p.status === 'published' ? '↩️ إلغاء النشر' : '✅ اعتماد ونشر';
  $('prepModal').style.display = 'flex';
}
function openReview(i) {
  const p = db.preps[i]; if (!p) return;
  revIdx = i; revScore = 0;
  $('revTitle').textContent = p.title;
  $('revComment').value = '';
  paintRevStars();
  $('revModal').style.display = 'flex';
}
$('pmClose')?.addEventListener('click', () => $('prepModal').style.display = 'none');
$('pmPublish')?.addEventListener('click', () => {
  const p = db.preps[pmIdx]; if (!p) return;
  if (!canTouch(p)) return toast('هذا التحضير ليس من بياناتك', false);
  p.status = p.status === 'published' ? 'draft' : 'published';
  save(); openPrep(pmIdx); renderPreps();
  logAct(p.status === 'published' ? 'نشر درس' : 'إرجاع درس', p.title);
  toast(p.status === 'published' ? '✅ تم اعتماد ونشر الدرس' : '↩️ أُرجع الدرس مسودة');
});
$('pmCopy')?.addEventListener('click', async () => {
  const p = db.preps[pmIdx]; if (!p) return;
  try {
    await navigator.clipboard.writeText(String(p.content || '').replace(/<[^>]+>/g, ''));
    toast('📋 نُسخ محتوى التحضير إلى الحافظة');
  } catch {
    const ta = document.createElement('textarea');
    ta.value = String(p.content || '').replace(/<[^>]+>/g, '');
    document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy'); toast('📋 نُسخ محتوى التحضير'); } catch { toast('تعذّر النسخ', false); }
    ta.remove();
  }
});
$('pmPrint')?.addEventListener('click', () => {
  const w = window.open('', '_blank');
  w.document.write(`<html dir="rtl"><head><meta charset="utf-8"><title>تحضير</title>
  <style>body{font-family:Cairo,sans-serif;padding:2rem;line-height:1.9;white-space:pre-wrap}</style></head>
  <body onload="window.print()">${$('pmBody').innerHTML}</body></html>`);
  w.document.close();
});
$('pmReview')?.addEventListener('click', () => { $('prepModal').style.display = 'none'; openReview(pmIdx); });
$('revClose')?.addEventListener('click', () => $('revModal').style.display = 'none');
document.querySelectorAll('#revStars [data-star]').forEach(s => s.addEventListener('click', () => {
  revScore = Number(s.dataset.star); paintRevStars();
}));
$('revSave')?.addEventListener('click', () => {
  const p = db.preps[revIdx]; if (!p) return;
  if (!canTouch(p)) return toast('هذا التحضير ليس من بياناتك', false);
  if (!revScore) return toast('اختر عدد النجوم أولاً', false);
  p.reviews = p.reviews || [];
  p.reviews.push({
    score: revScore, comment: $('revComment').value.trim(),
    by: (me()?.name) || 'معلم', uid: session, date: new Date().toLocaleDateString('ar-LY')
  });
  save(); $('revModal').style.display = 'none'; renderPreps();
  toast('⭐ تم حفظ المراجعة');
});
$('prepGenerate')?.addEventListener('click', () => {
  const t = $('pp_title').value.trim(), s = $('pp_subject').value.trim();
  if (!t || !s) return toast('اكتب عنوان الدرس والمادة', false);
  $('prepOut').innerHTML = esc(buildPrep(t, s, $('pp_grade').value.trim())).replace(/\n/g, '<br>');
  toast('✨ تم توليد التحضير');
});
$('prepSave')?.addEventListener('click', () => {
  const t = $('pp_title').value.trim(), s = $('pp_subject').value.trim();
  if (!t || !s) return toast('ولّد التحضير أولاً', false);
  db.preps.push({
    id: uid(), title: t, subject: s, grade: $('pp_grade').value,
    content: buildPrep(t, s, $('pp_grade').value), date: new Date().toLocaleDateString('ar-LY'),
    status: 'draft', reviews: [], by: session
  });
  save(); renderPreps(); toast('💾 تم حفظ التحضير — يمكنك مراجعته من «تحاضيري المحفوظة»');
});
$('prepPrint')?.addEventListener('click', () => {
  const w = window.open('', '_blank');
  w.document.write(`<html dir="rtl"><head><meta charset="utf-8"><title>تحضير</title>
  <style>body{font-family:Cairo,sans-serif;padding:2rem;line-height:1.9;white-space:pre-wrap}</style></head>
  <body onload="window.print()">${$('prepOut').innerHTML}</body></html>`);
  w.document.close();
});

/* ================= مساعد AI ================= */
const aiProviders = {
  gemini: { url: k => `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${k}`, key: 'AIza', parse: (d, p) => d?.candidates?.[0]?.content?.parts?.[0]?.text || '' },
  groq: { url: () => 'https://api.groq.com/openai/v1/chat/completions', key: 'gsk_', parse: d => d?.choices?.[0]?.message?.content || '' },
  openrouter: { url: () => 'https://openrouter.ai/api/v1/chat/completions', key: 'sk-or-', parse: d => d?.choices?.[0]?.message?.content || '' },
  openai: { url: () => 'https://api.openai.com/v1/chat/completions', key: 'sk-', parse: d => d?.choices?.[0]?.message?.content || '' }
};
$('aiSaveKey')?.addEventListener('click', () => {
  store.set('aiKey', $('aiKey').value.trim());
  store.set('aiProvider', $('aiProvider').value);
  toast('💾 حُفظ المفتاح في متصفحك فقط');
});
$('aiTest')?.addEventListener('click', async () => {
  const key = $('aiKey').value.trim() || store.get('aiKey', '');
  if (!key) return toast('أدخل المفتاح أولاً', false);
  $('aiConnMsg').textContent = '⏳ جارٍ الاختبار…'; $('aiConnMsg').style.color = '#d97706';
  try {
    await aiGenerate('قل: نجاح', 20);
    $('aiConnMsg').textContent = '✅ الاتصال سليم'; $('aiConnMsg').style.color = '#0d9488';
  } catch (e) {
    $('aiConnMsg').textContent = '❌ فشل: ' + e.message; $('aiConnMsg').style.color = '#e11d48';
  }
});
async function aiGenerate(prompt, timeoutSec = 45) {
  const prov = $('aiProvider')?.value || store.get('aiProvider', 'gemini');
  const key = ($('aiKey')?.value || store.get('aiKey', '')).trim();
  const cfg = aiProviders[prov];
  if (!key) throw new Error('لا يوجد مفتاح API');
  const ctrl = new AbortController();
  const to = setTimeout(() => ctrl.abort(), timeoutSec * 1000);
  try {
    let res;
    if (prov === 'gemini') {
      res = await fetch(cfg.url(key), {
        method: 'POST', signal: ctrl.signal,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }] })
      });
    } else {
      res = await fetch(cfg.url(key), {
        method: 'POST', signal: ctrl.signal,
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + key },
        body: JSON.stringify({
          model: prov === 'groq' ? 'llama-3.1-8b-instant' : 'meta-llama/llama-3.1-8b-instruct:free',
          messages: [{ role: 'user', content: prompt }], max_tokens: 2000
        })
      });
    }
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    const out = cfg.parse(data);
    if (!out) throw new Error('رد فارغ');
    return out;
  } finally { clearTimeout(to); }
}
const taskPrompts = {
  plan: t => `اكتب خطة درس كاملة بالعربية للمعلم الليبي: ${t}. أضف الأهداف الثلاثة (معرفية/مهارية/وجدانية)، التمهيد، العرض، الاستراتيجيات، الوسائل، التقويم، الواجب.`,
  mcq: t => `أنشئ 10 أسئلة اختيار من متعدد بالعربية عن: ${t}. لكل سؤال 4 خيارات وصحيحة محددة، مع الإجابة النموذجية في النهاية.`,
  worksheet: t => `أنشئ ورقة عمل بالعربية عن: ${t} تشمل: أسئلة تطبيقية، مسائل محلولة، ونشاط إبداعي، مع مساحات للإجابة.`,
  activity: t => `اقترح 5 أنشطة صفية تفاعلية بالعربية عن: ${t} مناسبة للمرحلة الأساسية، مع خطوات التنفيذ والزمن المتوقع.`,
  simplify: t => `بسّط شرح الدرس التالي لطلاب الصفوف الدنيا بالعربية مع أمثلة من الحياة اليومية: ${t}.`
};
$('aiRun')?.addEventListener('click', async () => {
  const topic = $('aiTopic').value.trim();
  if (!topic) return toast('اكتب عنوان الدرس', false);
  $('aiOut').textContent = '⏳ جارٍ التوليد… قد يستغرق حتى دقيقة…';
  try {
    const base = taskPrompts[$('aiTask').value](topic);
    const full = `${base}\nالمادة: ${$('aiSubject').value || '—'} — الصف: ${$('aiGrade').value || '—'}`;
    const out = await aiGenerate(full, 60);
    $('aiOut').textContent = out;
    toast('✨ تولّد المحتوى بنجاح');
  } catch (e) {
    $('aiOut').textContent = '❌ خطأ: ' + (e.name === 'AbortError' ? 'انتهت المهلة الزمنية' : e.message) +
      '\n\n💡 تحقق من المفتاح أو جرّب مزوّداً آخر (Groq مجاناً وسريع).';
    toast('فشل التوليد', false);
  }
});

/* ================= الاختبارات ================= */
const typeLabels = { quiz: 'فرض', midterm: 'نصف الفصل', final: 'نهائي' };
function renderExams() {
  const body = $('exBody');
  const list = visible(db.exams);
  body.innerHTML = list.length ? '' : '<tr><td colspan="7" class="text-center" style="padding:2rem">لا توجد اختبارات</td></tr>';
  let n = 0;
  list.forEach(e => {
    n++;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${n}</td><td class="font-bold">${esc(e.title)}</td><td>${esc(e.subject)}</td>
      <td><span class="pill">${typeLabels[e.type] || e.type}</span></td><td>${e.marks}</td><td>${esc(e.date || '—')}</td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
      if (!canTouch(e)) return toast('هذا الاختبار ليس من بياناتك', false);
      if (confirm('حذف الاختبار؟')) { db.exams = db.exams.filter(x => x.id !== e.id); save(); renderExams(); }
    });
    body.appendChild(tr);
  });
}
$('addExam')?.addEventListener('click', () => { $('exForm').style.display = $('exForm').style.display === 'none' ? 'block' : 'none'; });
$('exCancel')?.addEventListener('click', () => $('exForm').style.display = 'none');
$('exSave')?.addEventListener('click', () => {
  const title = $('ex_title').value.trim();
  if (!title) return toast('اكتب عنوان الاختبار', false);
  db.exams.push({ id: uid(), title, subject: $('ex_subject').value.trim(), marks: Number($('ex_marks').value) || 100, date: $('ex_date').value, type: $('ex_type').value, by: session });
  $('ex_title').value = ''; $('ex_subject').value = '';
  save(); $('exForm').style.display = 'none'; renderExams(); toast('✅ تم إنشاء الاختبار');
});

/* ================= الدرجات ================= */
function renderGrades() {
  const body = $('grBody');
  body.innerHTML = visible(db.grades).length ? '' : '<tr><td colspan="8" class="text-center" style="padding:2rem">لا توجد درجات — أضف درجة أول طالب</td></tr>';
  let n = 0;
  db.grades.forEach((g, i) => {
    if (!canTouch(g)) return;
    n++;
    const total = Number(g.work) + Number(g.quiz) + Number(g.exam);
    const [label, color] = gradeLabel(total);
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${n}</td><td class="font-bold">${esc(g.student)}</td><td>${g.work}</td><td>${g.quiz}</td>
      <td>${g.exam}</td><td><b>${total}/100</b></td><td><span class="pill" style="color:${color}">${label}</span></td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
      db.grades.splice(i, 1); save(); renderGrades();
    });
    body.appendChild(tr);
  });
}
$('addGrade')?.addEventListener('click', () => { $('grForm').style.display = $('grForm').style.display === 'none' ? 'block' : 'none'; });
$('grSave')?.addEventListener('click', () => {
  const student = $('gr_student').value.trim();
  if (!student) return toast('اكتب اسم الطالب', false);
  db.grades.push({
    id: uid(), student, by: session,
    work: Math.min(20, Math.max(0, Number($('gr_work').value) || 0)),
    quiz: Math.min(20, Math.max(0, Number($('gr_quiz').value) || 0)),
    exam: Math.min(60, Math.max(0, Number($('gr_exam').value) || 0))
  });
  $('gr_student').value = '';
  save(); renderGrades(); toast('✅ تمت إضافة الدرجة');
});

/* ================= الحضور ================= */
function renderAttendance() {
  if (!$('attDate').value) $('attDate').value = new Date().toISOString().slice(0, 10);
  const body = $('attBody');
  const myStudents = visible(db.students);
  if (!myStudents.length) {
    body.innerHTML = '<tr><td colspan="3" class="text-center" style="padding:2rem">أضف طلاباً أولاً من قسم الطلاب</td></tr>';
    updateAttSummary(); return;
  }
  body.innerHTML = '';
  const date = $('attDate').value;
  let n = 0;
  db.students.forEach((s) => {
    if (!canTouch(s)) return;
    n++;
    const rec = visible(db.attendance).find(a => a.student === s.name && a.date === date);
    const st = rec ? rec.status : 'present';
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${n}</td><td class="font-bold">${esc(s.name)}</td>
      <td>
        <select data-student="${esc(s.name)}" class="input" style="padding:.3rem .6rem;border-radius:.6rem">
          <option value="present" ${st === 'present' ? 'selected' : ''}>حاضر</option>
          <option value="absent" ${st === 'absent' ? 'selected' : ''}>غائب</option>
          <option value="late" ${st === 'late' ? 'selected' : ''}>متأخر</option>
          <option value="excused" ${st === 'excused' ? 'selected' : ''}>بعذر</option>
        </select>
      </td>`;
    tr.querySelector('select').addEventListener('change', e => {
      const name = e.target.dataset.student;
      const existing = visible(db.attendance).find(a => a.student === name && a.date === date);
      if (existing && !canTouch(existing)) {
        e.target.value = existing.status;
        return toast('سجل هذا الطالب ليس من بياناتك', false);
      }
      if (existing) existing.status = e.target.value;
      else db.attendance.push({ id: uid(), student: name, date, status: e.target.value, by: session });
      save(); updateAttSummary();
    });
    body.appendChild(tr);
  });
  updateAttSummary();
}
function updateAttSummary() {
  const date = $('attDate').value;
  const recs = visible(db.attendance).filter(a => a.date === date);
  const p = recs.filter(r => r.status === 'present').length;
  const a = recs.filter(r => r.status === 'absent').length;
  const l = recs.filter(r => r.status === 'late').length;
  $('attSummary').textContent = `حاضر: ${p} | غائب: ${a} | متأخر: ${l}`;
}
$('attDate')?.addEventListener('change', renderAttendance);
$('attAllPresent')?.addEventListener('click', () => {
  const date = $('attDate').value;
  visible(db.students).forEach(s => {
    const existing = visible(db.attendance).find(a => a.student === s.name && a.date === date);
    if (existing && !canTouch(existing)) return;
    if (existing) existing.status = 'present';
    else db.attendance.push({ id: uid(), student: s.name, date, status: 'present', by: session });
  });
  save(); renderAttendance(); toast('✔ تم تحضير الجميع');
});
$('attSave')?.addEventListener('click', () => { save(); toast('💾 تم حفظ الحضور'); });

/* ================= المدفوعات ================= */
const pyTypeLabels = { tuition: 'رسوم دراسية', exam: 'رسوم امتحان', activity: 'نشاط', other: 'أخرى' };
function renderPayments() {
  const pays = visible(db.payments);
  const paid = pays.filter(p => p.status === 'paid');
  const pending = pays.filter(p => p.status !== 'paid');
  const total = paid.reduce((a, p) => a + Number(p.amount), 0);
  $('payTotal').textContent = total.toLocaleString('ar-EG') + ' د.ل';
  $('payPending').textContent = pending.reduce((a, p) => a + Number(p.amount), 0).toLocaleString('ar-EG') + ' د.ل';
  $('payCount').textContent = pays.length;
  $('payRate').textContent = pays.length ? Math.round(paid.length / pays.length * 100) + '%' : '0%';

  const body = $('pyBody');
  body.innerHTML = pays.length ? '' : '<tr><td colspan="7" class="text-center" style="padding:2rem">لا توجد معاملات</td></tr>';
  let n = 0;
  db.payments.forEach((p, i) => {
    if (!canTouch(p)) return;
    n++;
    const st = p.status === 'paid'
      ? '<span class="pill" style="color:#0d9488">مدفوع</span>'
      : '<span class="pill" style="color:#d97706">معلّق</span>';
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${n}</td><td class="font-bold">${esc(p.student)}</td><td>${Number(p.amount).toLocaleString('ar-EG')} د.ل</td>
      <td>${pyTypeLabels[p.type] || p.type}</td><td>${st}</td><td>${esc(p.date || '—')}</td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
      db.payments.splice(i, 1); save(); renderPayments();
    });
    body.appendChild(tr);
  });
}
$('addPayment')?.addEventListener('click', () => { $('pyForm').style.display = $('pyForm').style.display === 'none' ? 'block' : 'none'; });
$('pyCancel')?.addEventListener('click', () => $('pyForm').style.display = 'none');
$('pySave')?.addEventListener('click', () => {
  const student = $('py_student').value.trim();
  const amount = Number($('py_amount').value);
  if (!student || !amount) return toast('أكمل البيانات', false);
  db.payments.push({ id: uid(), student, amount, type: $('py_type').value, status: $('py_status').value, date: new Date().toLocaleDateString('ar-LY'), by: session });
  $('py_student').value = ''; $('py_amount').value = '';
  save(); $('pyForm').style.display = 'none'; renderPayments(); toast('✅ تمت إضافة الدفعة');
});

/* ================= المتابعة ================= */
function renderFollowups() {
  const box = $('fuBox');
  box.innerHTML = visible(db.followups).length ? '' : '<div class="empty" style="padding:1rem">لا توجد متابعات بعد</div>';
  db.followups.forEach((f, i) => {
    if (!canTouch(f)) return;
    const div = document.createElement('div');
    div.className = 'p-3 rounded-xl';
    div.style.background = '#f9fafb';
    div.innerHTML = `<div class="flex justify-between items-center mb-1"><b>📋 ${esc(f.date)}</b>
      <button class="btn btn-ghost btn-sm">🗑️</button></div>
      <p><b>المغطى:</b> ${esc(f.topics)}</p>
      ${f.challenges ? `<p><b>التحديات:</b> ${esc(f.challenges)}</p>` : ''}
      ${f.next ? `<p><b>القادم:</b> ${esc(f.next)}</p>` : ''}`;
    div.querySelector('button').addEventListener('click', () => { db.followups.splice(i, 1); save(); renderFollowups(); });
    box.appendChild(div);
  });
}
$('fuSave')?.addEventListener('click', () => {
  const topics = $('fu_topics').value.trim();
  if (!topics) return toast('اكتب المواد المغطاة', false);
  db.followups.push({
    id: uid(), date: new Date().toLocaleDateString('ar-LY'), topics, by: session,
    activities: $('fu_activities').value.trim(), challenges: $('fu_challenges').value.trim(),
    next: $('fu_next').value.trim()
  });
  $('fu_topics').value = ''; $('fu_activities').value = ''; $('fu_challenges').value = ''; $('fu_next').value = '';
  save(); renderFollowups(); toast('✅ تمت إضافة المتابعة');
});

/* ================= الرسائل ================= */
function renderMessages() {
  const box = $('msBox');
  box.innerHTML = visible(db.messages).length ? '' : '<div class="empty" style="padding:1rem">لا توجد رسائل</div>';
  db.messages.forEach((m, i) => {
    if (!canTouch(m)) return;
    const div = document.createElement('div');
    div.className = 'p-3 rounded-xl flex items-start gap-2';
    div.style.background = '#f9fafb';
    div.innerHTML = `<div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold" style="background:#0f766e">${esc(m.to[0] || '؟')}</div>
      <div style="flex:1"><b>${esc(m.to)}</b><br>${esc(m.text)}<br><small class="text-gray-500">${esc(m.date)}</small></div>
      <button class="btn btn-ghost btn-sm">🗑️</button>`;
    div.querySelector('button').addEventListener('click', () => { db.messages.splice(i, 1); save(); renderMessages(); });
    box.appendChild(div);
  });
}
$('msSend')?.addEventListener('click', () => {
  const to = $('ms_to').value.trim(), text = $('ms_text').value.trim();
  if (!to || !text) return toast('أكمل بيانات الرسالة', false);
  db.messages.push({ id: uid(), to, text, date: new Date().toLocaleDateString('ar-LY'), by: session });
  $('ms_to').value = ''; $('ms_text').value = '';
  save(); renderMessages(); toast('📤 تمت إرسال الرسالة');
});

/* ================= المكتبة ================= */
const lbTypeIcons = { book: '📕', worksheet: '📄', slides: '📊', video: '🎬', exam: '🧪' };
const lbTypeLabels = { book: 'كتاب', worksheet: 'ورقة عمل', slides: 'عرض تقديمي', video: 'فيديو', exam: 'نموذج امتحان' };
function renderLibrary() {
  const box = $('lbBox');
  box.innerHTML = visible(db.library).length ? '' : '<div class="empty" style="grid-column:1/-1">لا توجد موارد — اضغط «إضافة مورد»</div>';
  db.library.forEach((l, i) => {
    if (!canTouch(l)) return;
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `<div class="text-3xl">${lbTypeIcons[l.type] || '📦'}</div>
      <h3 class="font-black mt-2">${esc(l.title)}</h3>
      <span class="pill">${lbTypeLabels[l.type] || l.type}</span>
      <div class="flex gap-2 mt-3">
        ${l.url ? `<a href="${esc(l.url)}" target="_blank" class="btn btn-ghost btn-sm">🔗 فتح</a>` : ''}
        <button class="btn btn-ghost btn-sm">🗑️</button>
      </div>`;
    card.querySelector('button').addEventListener('click', () => { db.library.splice(i, 1); save(); renderLibrary(); });
    box.appendChild(card);
  });
}
$('addLib')?.addEventListener('click', () => { $('lbForm').style.display = $('lbForm').style.display === 'none' ? 'block' : 'none'; });
$('lbCancel')?.addEventListener('click', () => $('lbForm').style.display = 'none');
$('lbSave')?.addEventListener('click', () => {
  const title = $('lb_title').value.trim();
  if (!title) return toast('اكتب عنوان المورد', false);
  db.library.push({ id: uid(), title, type: $('lb_type').value, url: $('lb_url').value.trim(), by: session });
  $('lb_title').value = ''; $('lb_url').value = '';
  save(); $('lbForm').style.display = 'none'; renderLibrary(); toast('✅ تمت إضافة المورد');
});

/* ================= التدريب ================= */
function renderTraining() {
  const box = $('trBox');
  const courses = visible(db.courses || []);
  box.innerHTML = courses.length ? '' : '<div class="empty" style="grid-column:1/-1">لا توجد دورات بعد — يمكن للمالك إضافتها من زر «إضافة دورة»</div>';
  courses.forEach(c => {
    const done = db.training.includes(c.id);
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `<div class="text-3xl">🎓</div>
      <h3 class="font-black mt-2">${esc(c.title)}</h3>
      <p class="text-xs text-gray-500 mt-1">${esc(c.desc || '')}</p>
      <div class="flex gap-2 mt-2"><span class="pill">${esc(c.level || 'عام')}</span><span class="pill">${c.hours || 1} ساعة</span><span class="pill">${esc(c.cat || 'عام')}</span></div>
      <button class="btn ${done ? 'btn-ghost' : 'btn-primary'} btn-sm mt-3 w-full justify-center">
        ${done ? '✅ مكتملة' : '▶️ ابدأ الدورة'}</button>`;
    card.querySelector('button').addEventListener('click', () => {
      if (done) db.training = db.training.filter(id => id !== c.id);
      else db.training.push(c.id);
      save(); renderTraining();
      toast(done ? 'تمت إعادة فتح الدورة' : '🎉 أكملت الدورة');
    });
    box.appendChild(card);
  });
  db.training = db.training.filter(id => courses.some(c => c.id === id));
  $('trDone').textContent = db.training.length;
  const pct = courses.length ? Math.round(db.training.length / courses.length * 100) : 0;
  $('trPct').textContent = pct + '%';
  $('trBar').style.width = pct + '%';
}
$('addCourse')?.addEventListener('click', () => { $('crForm').style.display = $('crForm').style.display === 'none' ? 'block' : 'none'; });
$('crCancel')?.addEventListener('click', () => $('crForm').style.display = 'none');
$('crSave')?.addEventListener('click', () => {
  if (!isOwner()) return toast('إضافة الدورات متاحة للمالك فقط', false);
  const title = $('cr_title').value.trim();
  if (!title) return toast('اكتب عنوان الدورة', false);
  db.courses.push({
    id: uid(), title, desc: $('cr_desc').value.trim(), by: session,
    hours: Number($('cr_hours').value) || 1, level: 'عام',
    cat: $('cr_cat').value.trim() || 'عام'
  });
  $('cr_title').value = ''; $('cr_desc').value = ''; $('cr_cat').value = '';
  save(); $('crForm').style.display = 'none'; renderTraining(); toast('✅ تمت إضافة الدورة');
});

/* ================= المجتمع ================= */
function renderCommunity() {
  const box = $('cmBox');
  box.innerHTML = db.community.length ? '' : '<div class="empty">لا توجد منشورات — كن أول من يشارك!</div>';
  db.community.forEach((p, i) => {
    const div = document.createElement('div');
    div.className = 'card';
    div.innerHTML = `<div class="flex items-center gap-2 mb-2">
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold" style="background:linear-gradient(135deg,#0f766e,#0d9488)">${esc(p.author[0] || 'م')}</div>
        <div><b>${esc(p.author)}</b><br><small class="text-gray-500">${esc(p.date)}</small></div>
        <button class="btn btn-ghost btn-sm mr-auto">🗑️</button>
      </div>
      <p class="text-sm mb-2">${esc(p.text)}</p>
      <div class="flex gap-3 text-sm">
        <button class="btn btn-ghost btn-sm" data-like>❤️ ${p.likes}</button>
        <span class="btn btn-ghost btn-sm" style="cursor:default">💬 ${p.comments}</span>
      </div>`;
    div.querySelector('[data-like]').addEventListener('click', () => { p.likes++; save(); renderCommunity(); });
    div.querySelector('.mr-auto').addEventListener('click', () => {
      if (!canTouch(p)) return toast('هذا المنشور ليس من إنشائك', false);
      db.community.splice(i, 1); save(); renderCommunity();
    });
    box.appendChild(div);
  });
}
$('cmAdd')?.addEventListener('click', () => {
  const text = $('cmText').value.trim();
  if (!text) return toast('اكتب منشورك', false);
  db.community.push({ id: uid(), author: (me()?.name) || db.profile.name || 'معلم', text, likes: 0, comments: 0, date: new Date().toLocaleDateString('ar-LY'), by: session });
  $('cmText').value = '';
  save(); renderCommunity(); toast('📢 تم النشر');
});

/* ================= الجدول ================= */
function renderSchedule() {
  const body = $('scBody');
  body.innerHTML = visible(db.schedule).length ? '' : '<tr><td colspan="5" class="text-center" style="padding:2rem">لا توجد حصص في الجدول</td></tr>';
  db.schedule.forEach((s, i) => {
    if (!canTouch(s)) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td><b>${esc(s.day)}</b></td><td>${esc(s.period)}</td><td>${esc(s.subject)}</td><td>${esc(s.className)}</td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => { db.schedule.splice(i, 1); save(); renderSchedule(); });
    body.appendChild(tr);
  });
}
$('addSch')?.addEventListener('click', () => { $('scForm').style.display = $('scForm').style.display === 'none' ? 'block' : 'none'; });
$('scCancel')?.addEventListener('click', () => $('scForm').style.display = 'none');
$('scSave')?.addEventListener('click', () => {
  const subject = $('sc_subject').value.trim();
  if (!subject) return toast('اكتب المادة', false);
  db.schedule.push({ id: uid(), day: $('sc_day').value, period: $('sc_period').value, subject, className: $('sc_class').value.trim(), by: session });
  $('sc_subject').value = ''; $('sc_class').value = '';
  save(); $('scForm').style.display = 'none'; renderSchedule(); toast('✅ تمت إضافة الحصة');
});

/* ================= الإشعارات ================= */
function renderNotifications() {
  const box = $('ntBox');
  const list = db.notifications.filter(n => isOwner() || !n.uid || n.uid === session);
  box.innerHTML = list.length ? '' : '<div class="empty">لا توجد إشعارات</div>';
  list.forEach((n, i) => {
    const div = document.createElement('div');
    div.className = 'card';
    div.style.opacity = n.read ? '.6' : '1';
    div.innerHTML = `<div class="flex items-center gap-2">
      <span>🔔</span>
      <div style="flex:1"><b>${esc(n.title)}</b><br><span class="text-sm text-gray-500">${esc(n.text)}</span></div>
      ${!n.read ? '<button class="btn btn-ghost btn-sm">✔ قراءة</button>' : '<span class="pill">مقروء</span>'}
    </div>`;
    div.querySelector('button')?.addEventListener('click', () => { n.read = true; save(); renderNotifications(); });
    box.appendChild(div);
  });
}

/* ================= الملف الشخصي ================= */
function renderProfile() {
  const u = me() || {};
  $('pf_name').value = u.name || db.profile.name || '';
  $('pf_email').value = u.email || db.profile.email || '';
  $('pf_phone').value = db.profile.phone || '';
  $('pf_spec').value = u.spec || db.profile.spec || '';
  paintAvatar();
}
$('pfSave')?.addEventListener('click', () => {
  const u = me(); if (!u) return;
  const email = $('pf_email').value.trim().toLowerCase();
  if (email && email !== u.email && db.users.some(x => x.email === email)) return toast('البريد الإلكتروني مستخدم مسبقاً', false);
  db.profile = {
    name: $('pf_name').value.trim(), email,
    phone: $('pf_phone').value.trim(), spec: $('pf_spec').value.trim()
  };
  u.name = $('pf_name').value.trim() || u.name;
  if (email) u.email = email;
  u.spec = $('pf_spec').value.trim();
  save();
  $('uName').textContent = u.name;
  toast('💾 تم حفظ الملف الشخصي');
});
$('pfPass')?.addEventListener('click', () => {
  const u = me(); if (!u) return;
  const o = $('pf_old').value, n = $('pf_new').value, c = $('pf_conf').value;
  if (!o || !n) return toast('أكمل الحقول', false);
  if (u.pass !== hashPw(o)) return toast('كلمة المرور الحالية غير صحيحة', false);
  if (n.length < 6) return toast('كلمة المرور الجديدة 6 أحرف على الأقل', false);
  if (n !== c) return toast('كلمتا المرور غير متطابقتين', false);
  u.pass = hashPw(n); save();
  $('pf_old').value = ''; $('pf_new').value = ''; $('pf_conf').value = '';
  toast('🔑 تم تحديث كلمة المرور');
});

/* ---- الصورة الشخصية ---- */
function resizeImg(file, cb) {
  const r = new FileReader();
  r.onload = e => {
    const img = new Image();
    img.onload = () => {
      const size = 160, c = document.createElement('canvas');
      c.width = c.height = size;
      const ctx = c.getContext('2d');
      const min = Math.min(img.width, img.height);
      ctx.drawImage(img, (img.width - min) / 2, (img.height - min) / 2, min, min, 0, 0, size, size);
      cb(c.toDataURL('image/jpeg', 0.82));
    };
    img.src = e.target.result;
  };
  r.readAsDataURL(file);
}
$('pfAvatar')?.addEventListener('change', e => {
  const f = e.target.files[0]; if (!f) return;
  if (!f.type.startsWith('image/')) return toast('اختر ملف صورة', false);
  if (f.size > 5 * 1024 * 1024) return toast('الصورة كبيرة — الحد 5MB', false);
  resizeImg(f, url => {
    const u = me(); if (!u) return;
    u.avatar = url; save(); paintAvatar(); toast('🖼️ تم تحديث الصورة الشخصية');
  });
  e.target.value = '';
});
$('pfAvatarRemove')?.addEventListener('click', () => {
  const u = me(); if (!u) return;
  delete u.avatar; save(); paintAvatar(); toast('🗑️ تم حذف الصورة');
});

/* ================= النسخ الاحتياطي ================= */
function exportData() {
  const blob = new Blob([JSON.stringify(db, null, 2)], { type: 'application/json' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'teacher-backup-' + new Date().toISOString().slice(0, 10) + '.json';
  a.click();
  setTimeout(() => URL.revokeObjectURL(a.href), 3000);
  logAct('تصدير نسخة احتياطية', isOwner() ? 'كامل البيانات' : 'بياناتي');
  toast('⬇️ تم تصدير النسخة الاحتياطية');
}
function importData(file) {
  const r = new FileReader();
  r.onload = e => {
    try {
      const d = JSON.parse(e.target.result);
      if (!d || !Array.isArray(d.users) || !Array.isArray(d.students)) throw new Error('ملف نسخة غير صالح');
      db = d;
      db.v = SCHEMA;
      db.settings = db.settings || { school: '', city: '', phone: '', email: '' };
      db.courses = db.courses || [];
      db.activity = db.activity || [];
      db.announcements = db.announcements || [];
      save();
      logAct('استيراد نسخة احتياطية', file.name || '');
      toast('⬆️ تم استيراد النسخة بنجاح — جارٍ إعادة التحميل…');
      setTimeout(() => location.reload(), 900);
    } catch (err) {
      toast('❌ الملف غير صالح: ' + err.message, false);
    }
  };
  r.readAsText(file);
}
$('pfExport')?.addEventListener('click', exportData);
$('setExport')?.addEventListener('click', exportData);
$('pfImportBtn')?.addEventListener('click', () => $('pfImport').click());
$('setImportBtn')?.addEventListener('click', () => $('setImport').click());
$('pfImport')?.addEventListener('change', e => { if (e.target.files[0]) importData(e.target.files[0]); e.target.value = ''; });
$('setImport')?.addEventListener('change', e => { if (e.target.files[0]) importData(e.target.files[0]); e.target.value = ''; });

/* ================= الإعدادات (للمالك) ================= */
function dataStatsText() {
  const kb = (JSON.stringify(db).length / 1024).toFixed(1);
  return `الطلاب ${db.students.length} • المعلمون ${db.users.filter(u => u.role === 'teacher').length} • المواد ${db.subjects.length} • التحاضير ${db.preps.length} • الاختبارات ${db.exams.length} • الدرجات ${db.grades.length} • المدفوعات ${db.payments.length} • الدورات ${(db.courses || []).length} • الحجم ${kb} KB`;
}
function renderSettings() {
  if (!isOwner()) return;
  const s = db.settings || (db.settings = { school: '', city: '', phone: '', email: '' });
  $('set_school').value = s.school || '';
  $('set_city').value = s.city || '';
  $('set_phone').value = s.phone || '';
  $('set_email').value = s.email || '';
  $('setDataStats').textContent = dataStatsText();
}
$('setSave')?.addEventListener('click', () => {
  if (!isOwner()) return;
  db.settings = {
    school: $('set_school').value.trim(), city: $('set_city').value.trim(),
    phone: $('set_phone').value.trim(), email: $('set_email').value.trim()
  };
  save();
  $('schoolLine').textContent = db.settings.school || 'لوحة التحكم';
  logAct('حفظ بيانات المدرسة', db.settings.school || '—');
  toast('💾 حُفظت بيانات المدرسة');
});
$('setReset')?.addEventListener('click', () => {
  if (!isOwner()) return toast('⛔ متاح للمالك فقط', false);
  if (!confirm('سيتم حذف جميع البيانات (طلاب، تحاضير، اختبارات…) وإبقاء حساب المالك فقط. هل أنت متأكد؟')) return;
  const users = db.users.filter(u => u.role === 'owner');
  const settings = db.settings;
  db = freshDb();
  db.users = users;
  db.settings = settings;
  session = users.length ? users[0].id : null;
  store.set('session', session);
  save();
  logAct('حذف كل البيانات', 'مسح كامل مع إبقاء حساب المالك');
  toast('🗑️ تم حذف كل البيانات وإبقاء حساب المالك');
  go('dashboard');
});

/* ================= سجل النشاط (للمالك) ================= */
function renderActivity() {
  if (!isOwner()) return;
  const body = $('acBody');
  const list = db.activity || [];
  body.innerHTML = list.length ? '' : '<tr><td colspan="5" class="text-center" style="padding:2rem">لا يوجد نشاط مسجل بعد</td></tr>';
  list.forEach((a, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="text-xs">${esc(a.time)}</td>
      <td class="font-bold">${esc(a.who)}<br><small class="text-gray-500">${esc(a.role || '')}</small></td>
      <td><span class="pill">${esc(a.action)}</span></td><td class="text-sm">${esc(a.detail || '—')}</td>`;
    body.appendChild(tr);
  });
}
$('acClear')?.addEventListener('click', () => {
  if (!isOwner()) return;
  if (!confirm('مسح سجل النشاط بالكامل؟')) return;
  db.activity = []; save(); renderActivity(); toast('🗑️ تم مسح السجل');
});

/* ================= الإعلانات (للمالك) ================= */
function renderAnnounce() {
  if (!isOwner()) return;
  const box = $('anBox');
  const list = db.announcements || [];
  box.innerHTML = list.length ? '' : '<div class="empty" style="padding:1rem">لم تُرسل إعلانات بعد</div>';
  list.forEach((a, i) => {
    const div = document.createElement('div');
    div.className = 'p-3 rounded-xl flex items-start gap-2';
    div.style.background = '#f9fafb';
    div.innerHTML = `<span>📢</span><div style="flex:1"><b>${esc(a.title)}</b><br>
        <span class="text-sm text-gray-600">${esc(a.text)}</span><br>
        <small class="text-gray-500">${esc(a.date)} • أُرسل إلى ${a.to} معلم</small></div>
      <button class="btn btn-ghost btn-sm">🗑️</button>`;
    div.querySelector('button').addEventListener('click', () => { db.announcements.splice(i, 1); save(); renderAnnounce(); });
    box.appendChild(div);
  });
}
$('anSend')?.addEventListener('click', () => {
  if (!isOwner()) return toast('⛔ الإعلانات متاحة لمالك المنصة فقط', false);
  const title = $('an_title').value.trim(), text = $('an_text').value.trim();
  if (!title || !text) return toast('اكتب عنوان ونص الإعلان', false);
  const teachers = db.users.filter(u => u.role === 'teacher' && (u.status || 'active') === 'active');
  teachers.forEach(u => db.notifications.push({ id: uid(), uid: u.id, title: '📢 ' + title, text, read: false }));
  db.announcements = db.announcements || [];
  db.announcements.unshift({ id: uid(), title, text, date: new Date().toLocaleDateString('ar-LY'), to: teachers.length });
  $('an_title').value = ''; $('an_text').value = '';
  save();
  logAct('إرسال إعلان', title + ' — إلى ' + teachers.length + ' معلم');
  renderAnnounce();
  toast('📢 أُرسل الإعلان إلى ' + teachers.length + ' معلم');
});

/* ================= التقارير (للمالك) ================= */
function renderReports() {
  if (!isOwner()) return;
  const t = db.users.filter(u => u.role === 'teacher');
  $('rpTeachers').textContent = t.length;
  $('rpActive').textContent = t.filter(u => (u.status || 'active') === 'active').length;
  $('rpPending').textContent = t.filter(u => u.status === 'pending').length;
  $('rpStudents').textContent = db.students.length;
  $('rpPreps').textContent = db.preps.length;
  $('rpExams').textContent = db.exams.length;
  $('rpAvg').textContent = avgGrade() + '%';
  const paid = db.payments.filter(p => p.status === 'paid').reduce((a, p) => a + Number(p.amount), 0);
  $('rpPaid').textContent = paid.toLocaleString('ar-EG') + ' د.ل';

  const body = $('rpBody');
  body.innerHTML = t.length ? '' : '<tr><td colspan="6" class="text-center" style="padding:2rem">لا توجد حسابات معلمين</td></tr>';
  t.forEach((u, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(u.name)}</td><td>${esc(u.spec || '—')}</td>
      <td>${esc(u.email)}</td><td>${statusPill(u.status)}</td><td>${esc(u.joined || '—')}</td>`;
    body.appendChild(tr);
  });

  const top = [...db.students]
    .map(s => ({ s, g: gradeOf(s.name), a: attOf(s.name) }))
    .sort((x, y) => y.g.total - x.g.total)
    .slice(0, 5);
  const tb = $('rpTopBody');
  tb.innerHTML = top.length ? '' : '<tr><td colspan="5" class="text-center" style="padding:2rem">لا يوجد طلاب بعد</td></tr>';
  top.forEach(({ s, g, a }, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(s.name)}</td><td>${esc(s.grade)}</td>
      <td>${g.total}% <span class="pill" style="color:${g.color}">${g.label}</span></td><td>${a}%</td>`;
    tb.appendChild(tr);
  });
}

/* ================= تهيئة ================= */
bootAuth();
