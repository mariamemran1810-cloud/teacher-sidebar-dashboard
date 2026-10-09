/* ================= منصة المعلم الليبي — لوحة التحكم ================= */
/* كل البيانات تُحفظ في localStorage داخل متصفح المستخدم */

const $ = (id) => document.getElementById(id);
const store = {
  get(k, d) { try { return JSON.parse(localStorage.getItem('ltp_' + k)) ?? d; } catch { return d; } },
  set(k, v) { localStorage.setItem('ltp_' + k, JSON.stringify(v)); }
};

let db = store.get('db', {
  students: [], teachers: [], subjects: [], preps: [], exams: [], grades: [],
  attendance: [], payments: [], followups: [], messages: [], library: [],
  training: [], community: [], schedule: [], todos: [], notifications: [
    { id: 1, title: 'مرحباً بك في المنصة', text: 'ابدأ بإضافة طلابك وتحضير دروسك', read: false }
  ],
  profile: { name: 'معلمنا', email: '', phone: '', spec: '' }
});

function save() { store.set('db', db); }
function uid() { return Date.now() + Math.floor(Math.random() * 1000); }

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
  dashboard: 'لوحة التحكم', students: 'إدارة الطلاب', teachers: 'المدرسون', subjects: 'المواد',
  prep: 'التحضير الذكي', ai: 'مساعد AI', exams: 'الاختبارات', grades: 'سجل الدرجات',
  attendance: 'الحضور والغياب', payments: 'المدفوعات', followups: 'المتابعة',
  messages: 'الرسائل', library: 'المكتبة', training: 'التدريب', community: 'المجتمع',
  schedule: 'الجدول', notifications: 'الإشعارات', profile: 'الملف الشخصي'
};

function go(view) {
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
function render(view) {
  const r = {
    dashboard: renderDashboard, students: renderStudents, teachers: renderTeachers,
    subjects: renderSubjects, prep: renderPreps, ai: renderAI, exams: renderExams,
    grades: renderGrades, attendance: renderAttendance, payments: renderPayments,
    followups: renderFollowups, messages: renderMessages, library: renderLibrary,
    training: renderTraining, community: renderCommunity, schedule: renderSchedule,
    notifications: renderNotifications, profile: renderProfile
  }[view];
  if (r) r();
}

/* ================= لوحة التحكم ================= */
function renderDashboard() {
  $('dashName').textContent = db.profile.name || 'معلمنا';
  $('kpiStudents').textContent = db.students.length;
  $('kpiPreps').textContent = db.preps.length;
  $('kpiExams').textContent = db.exams.length;
  $('kpiAvg').textContent = (avgGrade()) + '%';

  const box = $('dashTodos');
  box.innerHTML = db.todos.length ? '' : '<div class="empty" style="padding:1rem">لا توجد مهام بعد — أضف مهمة!</div>';
  db.todos.forEach((t, i) => {
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
  db.todos.push({ text: v, done: false }); $('todoInput').value = ''; save(); renderDashboard(); toast('✅ تمت إضافة المهمة');
});

function avgGrade() {
  if (!db.grades.length) return 0;
  const sum = db.grades.reduce((a, g) => a + (Number(g.work) + Number(g.quiz) + Number(g.exam)), 0);
  return Math.round(sum / db.grades.length);
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
  body.innerHTML = db.students.length ? '' : '<tr><td colspan="7" class="text-center" style="padding:2rem">لا يوجد طلاب — اضغط «إضافة طالب»</td></tr>';
  db.students.filter(s => !q || s.name.includes(q)).forEach((s, i) => {
    const att = attOf(s.name);
    const gr = gradeOf(s.name);
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(s.name)}</td><td>${esc(s.grade)}</td>
      <td>${esc(s.parent)}</td><td>${att}%</td><td>${gr.total}% <span class="pill" style="color:${gr.color}">${gr.label}</span></td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
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
  db.students.push({ id: uid(), name, grade: $('st_grade').value, parent: $('st_parent').value.trim() });
  $('st_name').value = ''; $('st_parent').value = '';
  save(); $('stForm').style.display = 'none'; renderStudents(); toast('✅ تمت إضافة الطالب');
});

function attOf(name) {
  const recs = db.attendance.filter(a => a.student === name);
  if (!recs.length) return 100;
  const present = recs.filter(r => r.status === 'present' || r.status === 'late').length;
  return Math.round(present / recs.length * 100);
}
function gradeOf(name) {
  const gs = db.grades.filter(g => g.student === name);
  if (!gs.length) return { total: 0, label: '—', color: '#6b7280' };
  const total = Math.round(gs.reduce((a, g) => a + Number(g.work) + Number(g.quiz) + Number(g.exam), 0) / gs.length);
  const [label, color] = gradeLabel(total);
  return { total, label, color };
}

/* ================= المدرسون ================= */
function renderTeachers() {
  const body = $('tcBody');
  body.innerHTML = db.teachers.length ? '' : '<tr><td colspan="5" class="text-center" style="padding:2rem">لا يوجد مدرسون</td></tr>';
  db.teachers.forEach((t, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(t.name)}</td><td>${esc(t.spec)}</td><td>${esc(t.phone)}</td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
      if (confirm('حذف المدرس؟')) { db.teachers = db.teachers.filter(x => x.id !== t.id); save(); renderTeachers(); }
    });
    body.appendChild(tr);
  });
}
$('addTeacher')?.addEventListener('click', () => { $('tcForm').style.display = $('tcForm').style.display === 'none' ? 'block' : 'none'; });
$('tcCancel')?.addEventListener('click', () => $('tcForm').style.display = 'none');
$('tcSave')?.addEventListener('click', () => {
  const name = $('tc_name').value.trim();
  if (!name) return toast('اكتب اسم المدرس', false);
  db.teachers.push({ id: uid(), name, spec: $('tc_spec').value.trim(), phone: $('tc_phone').value.trim() });
  $('tc_name').value = ''; $('tc_spec').value = ''; $('tc_phone').value = '';
  save(); $('tcForm').style.display = 'none'; renderTeachers(); toast('✅ تمت إضافة المدرس');
});

/* ================= المواد ================= */
const defaultSubjects = [
  { name: 'اللغة العربية', stage: 'أساسي', desc: 'قراءة، نحو، بلاغة' },
  { name: 'الرياضيات', stage: 'أساسي', desc: 'حساب، جبر، هندسة' },
  { name: 'اللغة الإنجليزية', stage: 'أساسي', desc: 'قواعد، محادثة، كتابة' },
  { name: 'العلوم', stage: 'أساسي', desc: 'أحياء، كيمياء، فيزياء' },
  { name: 'التربية الإسلامية', stage: 'أساسي', desc: 'قرآن، عقيدة، سيرة' },
  { name: 'الدراسات الاجتماعية', stage: 'أساسي', desc: 'تاريخ، جغرافيا' }
];
function renderSubjects() {
  const list = db.subjects.length ? db.subjects : defaultSubjects;
  const box = $('sbBox');
  box.innerHTML = '';
  list.forEach((s, i) => {
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `<div class="text-2xl">📚</div>
      <h3 class="font-black mt-2">${esc(s.name)}</h3>
      <p class="text-xs text-gray-500 mt-1">${esc(s.desc || '')}</p>
      <span class="pill">${esc(s.stage)}</span>
      ${db.subjects.length ? '<button class="btn btn-ghost btn-sm mt-2">🗑️</button>' : ''}`;
    card.querySelector('button')?.addEventListener('click', () => {
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
  db.subjects.push({ id: uid(), name, stage: $('sb_stage').value, desc: $('sb_desc').value.trim() });
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
function renderPreps() {
  const box = $('prepSavedBox');
  box.innerHTML = db.preps.length ? '' : '<div class="empty" style="padding:1rem">لا توجد تحاضير محفوظة بعد</div>';
  db.preps.forEach((p, i) => {
    const div = document.createElement('div');
    div.className = 'p-3 rounded-xl flex items-center gap-2';
    div.style.background = '#f9fafb';
    div.innerHTML = `<span>📝</span><span style="flex:1"><b>${esc(p.title)}</b> — ${esc(p.subject)}<br><small class="text-gray-500">${esc(p.date)}</small></span>
      <button class="btn btn-ghost btn-sm" data-view="${i}">عرض</button>
      <button class="btn btn-ghost btn-sm" data-del="${i}">🗑️</button>`;
    div.querySelector('[data-view]').addEventListener('click', () => {
      $('prepOut').innerHTML = esc(p.content).replace(/\n/g, '<br>');
    });
    div.querySelector('[data-del]').addEventListener('click', () => {
      db.preps.splice(i, 1); save(); renderPreps();
    });
    box.appendChild(div);
  });
}
$('prepGenerate')?.addEventListener('click', () => {
  const t = $('pp_title').value.trim(), s = $('pp_subject').value.trim();
  if (!t || !s) return toast('اكتب عنوان الدرس والمادة', false);
  $('prepOut').innerHTML = esc(buildPrep(t, s, $('pp_grade').value.trim())).replace(/\n/g, '<br>');
  toast('✨ تم توليد التحضير');
});
$('prepSave')?.addEventListener('click', () => {
  const t = $('pp_title').value.trim(), s = $('pp_subject').value.trim();
  if (!t || !s) return toast('ولّد التحضير أولاً', false);
  db.preps.push({ title: t, subject: s, grade: $('pp_grade').value, content: buildPrep(t, s, $('pp_grade').value), date: new Date().toLocaleDateString('ar-LY') });
  save(); renderPreps(); toast('💾 تم حفظ التحضير');
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
  body.innerHTML = db.exams.length ? '' : '<tr><td colspan="7" class="text-center" style="padding:2rem">لا توجد اختبارات</td></tr>';
  db.exams.forEach((e, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(e.title)}</td><td>${esc(e.subject)}</td>
      <td><span class="pill">${typeLabels[e.type] || e.type}</span></td><td>${e.marks}</td><td>${esc(e.date || '—')}</td>
      <td><button class="btn btn-ghost btn-sm">🗑️</button></td>`;
    tr.querySelector('button').addEventListener('click', () => {
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
  db.exams.push({ id: uid(), title, subject: $('ex_subject').value.trim(), marks: Number($('ex_marks').value) || 100, date: $('ex_date').value, type: $('ex_type').value });
  $('ex_title').value = ''; $('ex_subject').value = '';
  save(); $('exForm').style.display = 'none'; renderExams(); toast('✅ تم إنشاء الاختبار');
});

/* ================= الدرجات ================= */
function renderGrades() {
  const body = $('grBody');
  body.innerHTML = db.grades.length ? '' : '<tr><td colspan="8" class="text-center" style="padding:2rem">لا توجد درجات — أضف درجة أول طالب</td></tr>';
  db.grades.forEach((g, i) => {
    const total = Number(g.work) + Number(g.quiz) + Number(g.exam);
    const [label, color] = gradeLabel(total);
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(g.student)}</td><td>${g.work}</td><td>${g.quiz}</td>
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
    id: uid(), student,
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
  if (!db.students.length) {
    body.innerHTML = '<tr><td colspan="3" class="text-center" style="padding:2rem">أضف طلاباً أولاً من قسم الطلاب</td></tr>';
    updateAttSummary(); return;
  }
  body.innerHTML = '';
  const date = $('attDate').value;
  db.students.forEach((s, i) => {
    const rec = db.attendance.find(a => a.student === s.name && a.date === date);
    const st = rec ? rec.status : 'present';
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(s.name)}</td>
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
      const existing = db.attendance.find(a => a.student === name && a.date === date);
      if (existing) existing.status = e.target.value;
      else db.attendance.push({ id: uid(), student: name, date, status: e.target.value });
      save(); updateAttSummary();
    });
    body.appendChild(tr);
  });
  updateAttSummary();
}
function updateAttSummary() {
  const date = $('attDate').value;
  const recs = db.attendance.filter(a => a.date === date);
  const p = recs.filter(r => r.status === 'present').length;
  const a = recs.filter(r => r.status === 'absent').length;
  const l = recs.filter(r => r.status === 'late').length;
  $('attSummary').textContent = `حاضر: ${p} | غائب: ${a} | متأخر: ${l}`;
}
$('attDate')?.addEventListener('change', renderAttendance);
$('attAllPresent')?.addEventListener('click', () => {
  const date = $('attDate').value;
  db.students.forEach(s => {
    const existing = db.attendance.find(a => a.student === s.name && a.date === date);
    if (existing) existing.status = 'present';
    else db.attendance.push({ id: uid(), student: s.name, date, status: 'present' });
  });
  save(); renderAttendance(); toast('✔ تم تحضير الجميع');
});
$('attSave')?.addEventListener('click', () => { save(); toast('💾 تم حفظ الحضور'); });

/* ================= المدفوعات ================= */
const pyTypeLabels = { tuition: 'رسوم دراسية', exam: 'رسوم امتحان', activity: 'نشاط', other: 'أخرى' };
function renderPayments() {
  const paid = db.payments.filter(p => p.status === 'paid');
  const pending = db.payments.filter(p => p.status !== 'paid');
  const total = paid.reduce((a, p) => a + Number(p.amount), 0);
  $('payTotal').textContent = total.toLocaleString('ar-EG') + ' د.ل';
  $('payPending').textContent = pending.reduce((a, p) => a + Number(p.amount), 0).toLocaleString('ar-EG') + ' د.ل';
  $('payCount').textContent = db.payments.length;
  $('payRate').textContent = db.payments.length ? Math.round(paid.length / db.payments.length * 100) + '%' : '0%';

  const body = $('pyBody');
  body.innerHTML = db.payments.length ? '' : '<tr><td colspan="7" class="text-center" style="padding:2rem">لا توجد معاملات</td></tr>';
  db.payments.forEach((p, i) => {
    const st = p.status === 'paid'
      ? '<span class="pill" style="color:#0d9488">مدفوع</span>'
      : '<span class="pill" style="color:#d97706">معلّق</span>';
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i + 1}</td><td class="font-bold">${esc(p.student)}</td><td>${Number(p.amount).toLocaleString('ar-EG')} د.ل</td>
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
  db.payments.push({ id: uid(), student, amount, type: $('py_type').value, status: $('py_status').value, date: new Date().toLocaleDateString('ar-LY') });
  $('py_student').value = ''; $('py_amount').value = '';
  save(); $('pyForm').style.display = 'none'; renderPayments(); toast('✅ تمت إضافة الدفعة');
});

/* ================= المتابعة ================= */
function renderFollowups() {
  const box = $('fuBox');
  box.innerHTML = db.followups.length ? '' : '<div class="empty" style="padding:1rem">لا توجد متابعات بعد</div>';
  db.followups.forEach((f, i) => {
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
    id: uid(), date: new Date().toLocaleDateString('ar-LY'), topics,
    activities: $('fu_activities').value.trim(), challenges: $('fu_challenges').value.trim(),
    next: $('fu_next').value.trim()
  });
  $('fu_topics').value = ''; $('fu_activities').value = ''; $('fu_challenges').value = ''; $('fu_next').value = '';
  save(); renderFollowups(); toast('✅ تمت إضافة المتابعة');
});

/* ================= الرسائل ================= */
function renderMessages() {
  const box = $('msBox');
  box.innerHTML = db.messages.length ? '' : '<div class="empty" style="padding:1rem">لا توجد رسائل</div>';
  db.messages.forEach((m, i) => {
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
  db.messages.push({ id: uid(), to, text, date: new Date().toLocaleDateString('ar-LY') });
  $('ms_to').value = ''; $('ms_text').value = '';
  save(); renderMessages(); toast('📤 تمت إرسال الرسالة');
});

/* ================= المكتبة ================= */
const lbTypeIcons = { book: '📕', worksheet: '📄', slides: '📊', video: '🎬', exam: '🧪' };
const lbTypeLabels = { book: 'كتاب', worksheet: 'ورقة عمل', slides: 'عرض تقديمي', video: 'فيديو', exam: 'نموذج امتحان' };
function renderLibrary() {
  const box = $('lbBox');
  box.innerHTML = db.library.length ? '' : '<div class="empty" style="grid-column:1/-1">لا توجد موارد — اضغط «إضافة مورد»</div>';
  db.library.forEach((l, i) => {
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
  db.library.push({ id: uid(), title, type: $('lb_type').value, url: $('lb_url').value.trim() });
  $('lb_title').value = ''; $('lb_url').value = '';
  save(); $('lbForm').style.display = 'none'; renderLibrary(); toast('✅ تمت إضافة المورد');
});

/* ================= التدريب ================= */
const defaultCourses = [
  { id: 1, title: 'استخدام التكنولوجيا في التعليم', desc: 'تعلم استخدام الأدوات التكنولوجية الحديثة في الفصل', hours: 20, level: 'مبتدئ', cat: 'تكنولوجيا' },
  { id: 2, title: 'تصميم الاختبارات الإلكترونية', desc: 'أساليب تصميم اختبارات فعالة وموثوقة', hours: 15, level: 'متوسط', cat: 'التقويم' },
  { id: 3, title: 'إدارة الصف الدراسي', desc: 'استراتيجيات فعالة لإدارة الصف وتنظيم التعلم', hours: 10, level: 'مبتدئ', cat: 'إدارة' },
  { id: 4, title: 'التعليم التفاعلي', desc: 'تفعيل مشاركة الطلاب في عملية التعلم', hours: 25, level: 'متقدم', cat: 'استراتيجيات' },
  { id: 5, title: 'التقويم من أجل التعلم', desc: 'تقويم تكويني وبدائل التقييم التقليدي', hours: 12, level: 'متوسط', cat: 'التقويم' },
  { id: 6, title: 'الصف المقلوب', desc: 'التعلم القائم على المشروعات والواجبات المنزلية', hours: 18, level: 'متقدم', cat: 'استراتيجيات' }
];
function renderTraining() {
  const box = $('trBox');
  box.innerHTML = '';
  defaultCourses.forEach(c => {
    const done = db.training.includes(c.id);
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `<div class="text-3xl">🎓</div>
      <h3 class="font-black mt-2">${esc(c.title)}</h3>
      <p class="text-xs text-gray-500 mt-1">${esc(c.desc)}</p>
      <div class="flex gap-2 mt-2"><span class="pill">${c.level}</span><span class="pill">${c.hours} ساعة</span><span class="pill">${c.cat}</span></div>
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
  $('trDone').textContent = db.training.length;
  const pct = Math.round(db.training.length / defaultCourses.length * 100);
  $('trPct').textContent = pct + '%';
  $('trBar').style.width = pct + '%';
}

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
    div.querySelector('.mr-auto').addEventListener('click', () => { db.community.splice(i, 1); save(); renderCommunity(); });
    box.appendChild(div);
  });
}
$('cmAdd')?.addEventListener('click', () => {
  const text = $('cmText').value.trim();
  if (!text) return toast('اكتب منشورك', false);
  db.community.push({ id: uid(), author: db.profile.name || 'معلم', text, likes: 0, comments: 0, date: new Date().toLocaleDateString('ar-LY') });
  $('cmText').value = '';
  save(); renderCommunity(); toast('📢 تم النشر');
});

/* ================= الجدول ================= */
function renderSchedule() {
  const body = $('scBody');
  body.innerHTML = db.schedule.length ? '' : '<tr><td colspan="5" class="text-center" style="padding:2rem">لا توجد حصص في الجدول</td></tr>';
  db.schedule.forEach((s, i) => {
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
  db.schedule.push({ id: uid(), day: $('sc_day').value, period: $('sc_period').value, subject, className: $('sc_class').value.trim() });
  $('sc_subject').value = ''; $('sc_class').value = '';
  save(); $('scForm').style.display = 'none'; renderSchedule(); toast('✅ تمت إضافة الحصة');
});

/* ================= الإشعارات ================= */
function renderNotifications() {
  const box = $('ntBox');
  box.innerHTML = db.notifications.length ? '' : '<div class="empty">لا توجد إشعارات</div>';
  db.notifications.forEach((n, i) => {
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
  $('pf_name').value = db.profile.name || '';
  $('pf_email').value = db.profile.email || '';
  $('pf_phone').value = db.profile.phone || '';
  $('pf_spec').value = db.profile.spec || '';
}
$('pfSave')?.addEventListener('click', () => {
  db.profile = { name: $('pf_name').value.trim(), email: $('pf_email').value.trim(), phone: $('pf_phone').value.trim(), spec: $('pf_spec').value.trim() };
  save(); toast('💾 تم حفظ الملف الشخصي');
});
$('pfPass')?.addEventListener('click', () => {
  const o = $('pf_old').value, n = $('pf_new').value, c = $('pf_conf').value;
  if (!o || !n) return toast('أكمل الحقول', false);
  if (n !== c) return toast('كلمتا المرور غير متطابقتين', false);
  $('pf_old').value = ''; $('pf_new').value = ''; $('pf_conf').value = '';
  toast('🔑 تم تحديث كلمة المرور');
});

/* ================= تهيئة ================= */
go('dashboard');
