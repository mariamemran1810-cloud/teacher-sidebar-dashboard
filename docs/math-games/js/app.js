/* =====================================================
   app.js — منصة ألعاب الرياضيات (المنهج الليبي)
   التخزين، التنقل، الشاشات، النقاط، الشارات، الصوت
   ===================================================== */
'use strict';

/* ---------------- أدوات عامة ---------------- */
const $ = (s, r) => (r || document).querySelector(s);
const $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
const esc = s => String(s == null ? '' : s).replace(/[&<>"]/g, c =>
  ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
const pct = n => Math.max(0, Math.min(100, Math.round(n || 0)));

/* ---------------- التخزين ---------------- */
const KEY = 'libya-math-games.v1';
function defData() {
  return {
    v: 1, name: '', xp: 0, sound: true, theme: 'light',
    totals: { plays: 0, right: 0, all: 0, score: 0, streak: 0 },
    units: {}, modes: {}, grades: {}, badges: {}, history: [], board: []
  };
}
const Store = {
  d: null,
  load() {
    let raw = null;
    try { raw = localStorage.getItem(KEY); } catch (e) {}
    this.d = raw ? Object.assign(defData(), JSON.parse(raw)) : defData();
    this.d.totals = Object.assign(defData().totals, this.d.totals);
    return this.d;
  },
  save() { try { localStorage.setItem(KEY, JSON.stringify(this.d)); } catch (e) {} }
};

/* ---------------- الصوت ---------------- */
const Sfx = {
  ac: null,
  ctx() {
    if (!this.ac) {
      const AC = window.AudioContext || window.webkitAudioContext;
      if (!AC) return null;
      try { this.ac = new AC(); } catch (e) { return null; }
    }
    if (this.ac.state === 'suspended') this.ac.resume();
    return this.ac;
  },
  tone(f, dur, type, delay, vol) {
    const ac = this.ctx(); if (!ac) return;
    const t0 = ac.currentTime + (delay || 0);
    const o = ac.createOscillator(), g = ac.createGain();
    o.type = type || 'sine';
    o.frequency.setValueAtTime(f, t0);
    g.gain.setValueAtTime(0.0001, t0);
    g.gain.exponentialRampToValueAtTime(vol || 0.08, t0 + 0.02);
    g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
    o.connect(g); g.connect(ac.destination);
    o.start(t0); o.stop(t0 + dur + 0.06);
  },
  play(name) {
    if (!Store.d.sound) return;
    try {
      if (name === 'tap') this.tone(540, 0.07, 'triangle', 0, 0.04);
      else if (name === 'good') { this.tone(660, 0.12, 'sine', 0, 0.08); this.tone(880, 0.16, 'sine', 0.09, 0.07); }
      else if (name === 'bad') { this.tone(210, 0.2, 'sawtooth', 0, 0.06); this.tone(150, 0.24, 'sawtooth', 0.09, 0.05); }
      else if (name === 'win') [523, 659, 784, 1046].forEach((f, i) => this.tone(f, 0.2, 'triangle', i * 0.1, 0.07));
      else if (name === 'level') [440, 554, 659, 880].forEach((f, i) => this.tone(f, 0.18, 'sine', i * 0.08, 0.07));
      else if (name === 'star') { this.tone(950, 0.09, 'square', 0, 0.04); this.tone(1250, 0.12, 'square', 0.07, 0.035); }
    } catch (e) {}
  }
};

/* ---------------- المؤثرات ---------------- */
function toast(msg, gold) {
  const t = document.createElement('div');
  t.className = 'toast' + (gold ? ' gold' : '');
  t.innerHTML = msg;
  $('#toastRoot').appendChild(t);
  setTimeout(() => t.classList.add('out'), 2500);
  setTimeout(() => t.remove(), 3200);
}
function flyScore(ev, text) {
  const x = ev && ev.clientX ? ev.clientX : window.innerWidth / 2;
  const y = ev && ev.clientY ? ev.clientY : window.innerHeight / 2;
  const s = document.createElement('span');
  s.className = 'fly';
  s.textContent = text;
  s.style.left = (x - 16) + 'px';
  s.style.top = (y - 34) + 'px';
  $('#fxRoot').appendChild(s);
  setTimeout(() => s.remove(), 950);
}
function confetti(n) {
  const chars = ['🎉', '⭐', '✨', '🏅', '🔷', '🎊', '💫', '🟢'];
  const root = $('#fxRoot');
  for (let i = 0; i < (n || 36); i++) {
    const s = document.createElement('span');
    s.className = 'confetti';
    s.textContent = chars[i % chars.length];
    s.style.left = (Math.random() * 100) + 'vw';
    s.style.fontSize = (13 + Math.random() * 17) + 'px';
    s.style.animationDuration = (2.3 + Math.random() * 2.2) + 's';
    s.style.animationDelay = (Math.random() * 0.7) + 's';
    root.appendChild(s);
    setTimeout(() => s.remove(), 5400);
  }
}
function buzz(p) { try { navigator.vibrate && navigator.vibrate(p); } catch (e) {} }

function modal(html, onMount, cls) {
  const back = document.createElement('div');
  back.className = 'modal-back';
  back.innerHTML = '<div class="modal' + (cls ? ' ' + cls : '') + '">' + html + '</div>';
  $('#modalRoot').appendChild(back);
  const close = () => back.remove();
  back.addEventListener('click', e => { if (e.target === back) close(); });
  $$('[data-close]', back).forEach(b => b.addEventListener('click', close));
  const m = back.querySelector('.modal');
  if (onMount) onMount(m, close);
  return close;
}

/* واجهة تستخدمها الألعاب */
window.UI = {
  sfx: n => Sfx.play(n),
  buzz,
  fly: flyScore,
  toast,
  confetti,
  modal
};

/* ---------------- المستويات والشارات ---------------- */
const lvlOf = xp => Math.floor(Math.sqrt(Math.max(0, xp) / 40)) + 1;
const xpAt = lvl => 40 * (lvl - 1) * (lvl - 1);
function lvlInfo(xp) {
  const l = lvlOf(xp), base = xpAt(l), next = xpAt(l + 1);
  return { l, pct: next > base ? Math.round(((xp - base) / (next - base)) * 100) : 100, need: next - xp };
}
function masteredUnits() {
  return Object.keys(Store.d.units).filter(k => (Store.d.units[k].bestAcc || 0) >= 80).length;
}
const BADGES = [
  { id: 'first', icon: '🎮', name: 'الانطلاقة', hint: 'أنهِ أول لعبة', test: s => s.totals.plays >= 1 },
  { id: 'streak5', icon: '🔥', name: 'سلاسل متقدّمة', hint: '٥ إجابات صحيحة متتالية', test: s => s.totals.streak >= 5 },
  { id: 'streak10', icon: '🌋', name: 'انفجار الطاقة', hint: '١٠ إجابات صحيحة متتالية', test: s => s.totals.streak >= 10 },
  { id: 'perfect', icon: '💯', name: 'علامة كاملة', hint: 'دقة ١٠٠٪ في جولة واحدة', test: (s, c) => c && c.acc === 100 },
  { id: 'plays10', icon: '🏅', name: 'لاعب متمرس', hint: 'إنهاء ١٠ ألعاب', test: s => s.totals.plays >= 10 },
  { id: 'plays25', icon: '🎖️', name: 'محترف حقيقي', hint: 'إنهاء ٢٥ لعبة', test: s => s.totals.plays >= 25 },
  { id: 'right100', icon: '🧠', name: 'مئة إجابة صحيحة', hint: '100 إجابة صحيحة', test: s => s.totals.right >= 100 },
  { id: 'right300', icon: '🎓', name: 'ثلاثمئة إجابة', hint: '300 إجابة صحيحة', test: s => s.totals.right >= 300 },
  { id: 'xp500', icon: '⭐', name: '٥٠٠ نقطة خبرة', hint: 'اجمع 500 نقطة', test: s => s.xp >= 500 },
  { id: 'xp1500', icon: '🌟', name: '١٥٠٠ نقطة خبرة', hint: 'اجمع 1500 نقطة', test: s => s.xp >= 1500 },
  { id: 'allModes', icon: '🎯', name: 'جولة الألعاب', hint: 'جرّب الألعاب الستة', test: s => Object.keys(s.modes).length >= 6 },
  { id: 'allGrades', icon: '🌍', name: 'كل الصفوف', hint: 'العب في الصفوف الثلاثة', test: s => Object.keys(s.grades).length >= 3 },
  { id: 'master5', icon: '👑', name: 'خبير الوحدات', hint: 'أتقن ٥ وحدات (٨٠٪+)', test: () => masteredUnits() >= 5 },
  { id: 'masterAll', icon: '🏆', name: 'خبير المنهج', hint: 'أتقن 12 وحدة (٨٠٪+)', test: () => masteredUnits() >= 12 }
];
function checkBadges(ctx) {
  const fresh = [];
  BADGES.forEach(b => {
    if (Store.d.badges[b.id]) return;
    let ok = false;
    try { ok = b.test(Store.d, ctx); } catch (e) {}
    if (ok) { Store.d.badges[b.id] = Date.now(); fresh.push(b); }
  });
  return fresh;
}

/* ---------------- قياس نتيجة الجولة ---------------- */
function measure(s) {
  let right, all;
  if (s.mode === 'match') { all = s.total * 4; right = Math.max(0, all - s.mistakes); }
  else if (s.mode === 'order') { all = s.total + s.mistakes; right = s.total; }
  else { right = s.correct; all = s.total; }
  const acc = all ? Math.round((right / all) * 100) : 0;
  return { right, all, acc };
}

/* =====================================================
   الشاشات
   ===================================================== */
const app = () => $('#app');
let currentGame = null;
let lastResult = null;
let pickerClose = null;

function killGame() {
  if (currentGame) { try { currentGame.destroy(); } catch (e) {} currentGame = null; }
  if (pickerClose) { try { pickerClose(); } catch (e) {} pickerClose = null; }
}

/* ---------------- الرئيسية ---------------- */
function viewHome() {
  const t = Cur.totals();
  const d = Store.d;
  const resume = d.history && d.history.length ? d.history[d.history.length - 1] : null;

  const grades = Cur.grades.map(g => {
    const m = gradeMastery(g.id);
    const c = Cur.gradeCounts(g.id);
    return '<a class="grade-card" style="--gc:' + esc(g.color) + '" href="#/g/' + g.id + '">' +
      '<div class="gc-top"><div class="gc-icon">' + g.icon + '</div>' +
      '<div><div class="gc-title">' + esc(g.name) + '</div>' +
      '<div class="gc-sub">' + g.units.length + ' وحدات · ' + (c.mc + c.tf + c.num) + ' سؤالاً</div></div></div>' +
      '<p>' + esc(g.desc) + '</p>' +
      '<div class="gc-meta"><span class="chip">🎮 ' + GAME_META.length + ' ألعاب</span>' +
      '<span class="chip">🧩 ' + (c.pairs + c.order) + ' تحدي ترتيب ومطابقة</span></div>' +
      '<div class="gc-foot"><div class="progress"><i style="width:' + m + '%"></i></div><span>' +
      (m ? m + '% إتقان' : 'ابدأ الآن') + '</span></div></a>';
  }).join('');

  const gameTiles = GAME_META.map(g =>
    '<a class="game-tile" href="#/help" style="--gc:var(--teal)"><div class="gt-icon">' + g.icon + '</div>' +
    '<div class="gt-name">' + esc(g.name) + '</div><div class="gt-desc">' + esc(g.desc) + '</div>' +
    '<div class="gt-meta"><span class="chip">' + g.len + ' ' + esc(g.unit) + '</span></div></a>').join('');

  app().innerHTML =
    '<section class="hero"><div class="hero-grid">' +
      '<div>' +
        '<span class="eyebrow">🎮 منصة ألعاب تفاعلية مجانية</span>' +
        '<h1>تعلّم الرياضيات <span class="hl">باللعب</span> — منهج ليبيا للمرحلة الإعدادية</h1>' +
        '<p class="lead">بنك أسئلة شامل يغطي الصفوف الثلاثة، وستة ألعاب مختلفة تختبر فهمك بطرق ممتعة: اختبار سريع، صح أم خطأ، المطابقة، الترتيب، إدخال الإجابة، وسباق الوقت — مع شرح بعد كل إجابة، ونقاط، وشارات، ولوحة متصدرين.</p>' +
        '<div class="hero-cta">' +
          '<button class="btn btn-primary" id="startBtn">🚀 ابدأ اللعب الآن</button>' +
          (resume ? '<button class="btn btn-gold" id="resumeBtn">▶ أكمل من حيث توقفت</button>' : '') +
          '<a class="btn btn-ghost" href="#/help">كيف تلعب؟</a>' +
        '</div>' +
        '<div class="hero-badges">' +
          '<div class="b"><i>🎮</i> 6 ألعاب تفاعلية</div>' +
          '<div class="b"><i>📚</i> ' + t.units + ' وحدة دراسية</div>' +
          '<div class="b"><i>💯</i> شرح كل إجابة</div>' +
          '<div class="b"><i>📵</i> تعمل بدون إنترنت</div>' +
        '</div>' +
      '</div>' +
      '<div class="hero-visual">' +
        '<div class="mock">' +
          '<div class="mock-row"><div class="mi">⚡</div><div class="mt"><b>اختبار سريع · الأعداد الناسبية</b><small>10 أسئلة مع شرح بعد كل إجابة</small></div><div class="bar"><i style="width:92%"></i></div></div>' +
          '<div class="mock-row"><div class="mi">🧩</div><div class="mt"><b>المطابقة · المتتاليات</b><small>طابق المصطلح بمقابله</small></div><div class="bar"><i style="width:78%"></i></div></div>' +
          '<div class="mock-row"><div class="mi">⏱️</div><div class="mt"><b>سباق الوقت · الدائرة</b><small>60 ثانية و3 أرواح</small></div><div class="bar"><i style="width:64%"></i></div></div>' +
          '<div class="mock-row"><div class="mi">🏆</div><div class="mt"><b>تقدّمك في المنهج</b><small>' + masteredUnits() + ' وحدات متقنة</small></div><div class="bar"><i style="width:' + pct(t.units ? (masteredUnits() / t.units) * 100 : 0) + '%"></i></div></div>' +
        '</div>' +
        '<span class="float-tag f1">⭐ ' + d.xp + ' نقطة</span>' +
        '<span class="float-tag f2">🏅 ' + Object.keys(d.badges).length + ' شارة</span>' +
      '</div>' +
    '</div></section>' +

    '<section class="section"><div class="stats-strip">' +
      '<div class="stat-box"><b>' + t.questions + '</b><span>سؤال في البنك</span></div>' +
      '<div class="stat-box"><b>' + t.units + '</b><span>وحدة دراسية</span></div>' +
      '<div class="stat-box"><b>' + GAME_META.length + '</b><span>ألعاب تفاعلية</span></div>' +
      '<div class="stat-box"><b>' + Store.d.xp + '</b><span>نقطة خبرة لك</span></div>' +
    '</div></section>' +

    '<section class="section" id="grades"><div class="section-head">' +
      '<span class="eyebrow">اختر صفك</span><h2>3 صفوف · 18 وحدة · 3 فصول دراسية</h2>' +
      '<p>كل وحدة تحوي بنك أسئلة خاصاً بها، وتقدّمك يُحفظ تلقائياً على جهازك.</p></div>' +
      '<div class="grade-grid">' + grades + '</div></section>' +

    '<section class="section" id="games"><div class="section-head">' +
      '<span class="eyebrow">ستة أصناف</span><h2>اختر طريقة اللعب التي تحبها</h2>' +
      '<p>لكل لعبة نقاطها وتحديها، واللعبة نفسها تختلف من وحدة لأخرى لأن الأسئلة تتبدّل كل مرة.</p></div>' +
      '<div class="games-grid">' + gameTiles + '</div></section>';

  const sb = $('#startBtn');
  if (sb) sb.addEventListener('click', () => {
    const el = $('#grades');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    Sfx.play('tap');
  });
  const rb = $('#resumeBtn');
  if (rb && resume) rb.addEventListener('click', () => {
    location.hash = '#/play/' + resume.gid + '/' + resume.uid + '/' + resume.mode;
  });
}

/* ---------------- شاشة الصف ---------------- */
function viewGrade(gid) {
  const g = Cur.grade(gid);
  if (!g) { location.hash = '#/'; return; }
  const c = Cur.gradeCounts(gid);
  const av = Cur.gradeAvailability(gid);

  const units = g.units.map(u => {
    const st = Store.d.units[u.id] || { plays: 0, bestAcc: 0, best: 0 };
    let topics = [];
    try { topics = (u.items() || []).map(x => x.k).filter((v, i, a) => v && a.indexOf(v) === i).slice(0, 4); }
    catch (e) { topics = u.topics ? u.topics.slice(0, 4) : []; }
    if (!topics.length) topics = (u.topics || []).slice(0, 4);
    return '<article class="unit-card">' +
      (st.bestAcc >= 80 ? '<span class="crown" title="وحدة متقنة">👑</span>' : '') +
      '<div class="uc-head"><div class="uc-icon">' + u.icon + '</div>' +
        '<div><div class="uc-title">' + esc(u.name) + '</div>' +
        '<div class="uc-desc">' + esc(u.desc || '') + '</div></div></div>' +
      '<div class="uc-topics">' + topics.map(t => '<span class="chip">' + esc(t) + '</span>').join('') + '</div>' +
      '<div class="uc-stats"><div class="progress' + (st.bestAcc >= 80 ? ' gold' : '') + '"><i style="width:' + pct(st.bestAcc) + '%"></i></div>' +
        '<span>' + (st.plays ? pct(st.bestAcc) + '% · أفضل ' + (st.best || 0) : 'لم تُلعب بعد') + '</span></div>' +
      '<div class="uc-actions"><button class="btn btn-primary" data-pick="' + u.id + '">🎮 اختر لعبتك</button></div>' +
    '</article>';
  }).join('');

  app().innerHTML =
    '<div class="section">' +
    '<div class="crumb"><a href="#/">الرئيسية</a> <span>←</span> <b>' + esc(g.name) + '</b></div>' +
    '<div class="grade-head" style="--gc:' + esc(g.color) + '">' +
      '<div class="gh-icon">' + g.icon + '</div>' +
      '<div><h1>الصف ' + esc(g.name) + '</h1><p>' + esc(g.desc) + ' · ' + g.units.length + ' وحدات · ' +
        (c.mc + c.tf + c.num) + ' سؤالاً و' + (c.pairs + c.order) + ' تحدياً</p></div>' +
      '<div class="gh-actions">' +
        '<a class="btn btn-gold btn-sm" href="#/play/' + g.id + '/_mixed/quiz" title="أسئلة من كل وحدات الصف">⚡ تحدي شامل</a>' +
        '<a class="btn btn-ghost btn-sm" href="#/stats">📊 تقدّمي</a>' +
      '</div>' +
    '</div>' +
    '<div class="unit-grid">' + units + '</div>' +
    '<div class="center-actions"><a class="btn btn-ghost btn-sm" href="#/">← رجوع للرئيسية</a></div>' +
    '</div>';

  $$('[data-pick]').forEach(b => b.addEventListener('click', () => openPicker(g, b.dataset.pick)));
}

/* ---------------- نافذة اختيار اللعبة ---------------- */
function openPicker(g, uid) {
  const u = g.id === null ? null : Cur.unit(g.id, uid);
  const isMixed = uid === '_mixed';
  const av = isMixed ? Cur.gradeAvailability(g.id) : Cur.availability(g.id, uid);
  const name = isMixed ? 'التحدي الشامل' : (u ? u.name : '');
  const desc = isMixed ? 'أسئلة مختلطة من كل وحدات ' + g.name : (u ? u.desc : '');
  const store = Store.d.units[uid] || { modes: {} };

  const tiles = GAME_META.map(m => {
    const a = av[m.id] || { ok: false, have: 0, need: m.min };
    const best = (store.modes && store.modes[m.id]) ? store.modes[m.id].best : 0;
    return '<button type="button" class="game-tile' + (a.ok ? '' : ' locked') + '" data-mode="' + m.id + '" style="--gc:' + esc(g.color) + '">' +
      (best ? '<span class="best">🏆 ' + best + '</span>' : '') +
      '<div class="gt-icon">' + m.icon + '</div>' +
      '<div class="gt-name">' + esc(m.name) + '</div>' +
      '<div class="gt-desc">' + esc(m.desc) + '</div>' +
      '<div class="gt-meta">' +
        (a.ok
          ? '<span class="chip ok">متاحة</span><span class="chip">' + a.have + ' ' + (m.need === 'pairs' || m.need === 'order' ? 'جولة' : 'سؤال') + '</span>'
          : '<span class="chip">تحتاج ' + a.need + ' ' + (m.need === 'pairs' || m.need === 'order' ? 'جولات' : 'أسئلة') + ' (' + a.have + ')</span>') +
      '</div></button>';
  }).join('');

  pickerClose = modal(
    '<h3>🎮 اختر لعبتك — ' + esc(name) + '</h3>' +
    '<p>' + esc(desc || '') + '</p>' +
    '<div class="games-grid" style="margin-top:16px">' + tiles + '</div>' +
    '<div class="row"><button class="btn btn-ghost" data-close>إغلاق</button></div>',
    (m, close) => {
      $$('.game-tile:not(.locked)', m).forEach(b => b.addEventListener('click', () => {
        close(); pickerClose = null;
        location.hash = '#/play/' + g.id + '/' + uid + '/' + b.dataset.mode;
      }));
      $$('.game-tile.locked', m).forEach(b => b.addEventListener('click', () =>
        toast('هذه اللعبة تحتاج أسئلة أكثر في هذه الوحدة')));
    }, 'wide');
}

/* ---------------- شاشة اللعب ---------------- */
function viewPlay(gid, uid, mode) {
  const g = Cur.grade(gid);
  const meta = GAME_META.find(m => m.id === mode);
  if (!g || !meta) { location.hash = '#/'; return; }

  const isMixed = uid === '_mixed';
  if (!isMixed && !Cur.unit(gid, uid)) { location.hash = '#/g/' + gid; return; }

  const av = isMixed ? Cur.gradeAvailability(gid) : Cur.availability(gid, uid);
  if (!av[mode] || !av[mode].ok) {
    toast('هذه اللعبة غير متاحة لهذه الوحدة بعد');
    location.hash = isMixed ? '#/g/' + gid : '#/g/' + gid;
    return;
  }

  const full = isMixed ? Cur.gradePool(gid, meta.need) : Cur.pool(gid, uid, meta.need);
  if (!full.length) { toast('لا توجد أسئلة كافية'); location.hash = '#/g/' + gid; return; }
  const pool = (meta.id === 'arcade') ? full : full.slice(0, meta.len);

  const unit = isMixed ? null : Cur.unit(gid, uid);
  let ended = false;

  document.body.classList.add('in-game');
  app().innerHTML = '';
  currentGame = window.Games.run({    mode, gradeId: gid, unitId: uid, pool,
    title: (isMixed ? '⚡ التحدي الشامل' : (unit ? unit.icon + ' ' + unit.name : '')) ,
    sub: 'الصف ' + g.name + ' · ' + meta.name,
    quit() { location.hash = '#/g/' + gid; },
    done(summary) {
      if (ended) return;
      ended = true;
      const s = Object.assign({}, summary);
      const ms = measure(s);
      handleResult(s, ms, { gid, uid, mode, g, unit, meta });
    }
  });
  if (currentGame && currentGame.root) app().appendChild(currentGame.root);
}

/* ---------------- حفظ النتيجة + الشارات ---------------- */
function handleResult(s, ms, ctx) {
  const d = Store.d;
  const acc = ms.acc;
  let xp = Math.max(5, Math.round(s.score / 4));
  if (acc === 100) xp += 40; else if (acc >= 80) xp += 15;

  d.xp += xp;
  d.totals.plays++;
  d.totals.right += ms.right;
  d.totals.all += ms.all;
  d.totals.score += s.score;
  d.totals.streak = Math.max(d.totals.streak, s.bestStreak || 0);

  d.modes[ctx.mode] = (d.modes[ctx.mode] || 0) + 1;
  d.grades[ctx.gid] = (d.grades[ctx.gid] || 0) + 1;

  if (ctx.uid !== '_mixed') {
    const u = d.units[ctx.uid] || (d.units[ctx.uid] = { plays: 0, best: 0, bestAcc: 0, modes: {} });
    u.plays++;
    u.best = Math.max(u.best || 0, s.score);
    u.bestAcc = Math.max(u.bestAcc || 0, acc);
    const m = u.modes[ctx.mode] || (u.modes[ctx.mode] = { plays: 0, best: 0, acc: 0 });
    m.plays++;
    m.best = Math.max(m.best, s.score);
    m.acc = Math.max(m.acc, acc);
  }

  d.history = (d.history || []).concat([{
    gid: ctx.gid, uid: ctx.uid, mode: ctx.mode, acc, score: s.score, ts: Date.now()
  }]).slice(-40);

  const fresh = checkBadges({ acc });
  upsertBoard();
  Store.save();
  updateXP();

  lastResult = { s, ms, ctx, xp, fresh };
  location.hash = '#/r';
  if (fresh.length) { confetti(30); Sfx.play('level'); }
}

/* ---------------- شاشة النتيجة ---------------- */
function viewResults() {
  document.body.classList.remove('in-game');
  const R = lastResult;
  if (!R) { location.hash = '#/'; return; }
  const { s, ms, ctx, xp, fresh } = R;
  const acc = ms.acc;
  const stars = acc >= 90 ? 3 : acc >= 70 ? 2 : acc >= 50 ? 1 : 0;
  const title = acc === 100 ? 'ممتاز! علامة كاملة 🏆'
    : acc >= 80 ? 'أحسنت! أداء رائع 🌟'
    : acc >= 60 ? 'جيد، واصِل التدريب 💪'
    : acc >= 40 ? 'بداية طيبة — أعد المحاولة 📘'
    : 'لا بأس، كل جولة تجربة 🌱';
  const lvl = lvlInfo(Store.d.xp);

  const wrong = (s.wrong || []).slice(0, 6).map(w =>
    '<div class="review-item"><div class="q">' + esc(w.q) + '</div>' +
    '<div class="a">✔ الصواب: ' + esc(w.a) + '</div>' +
    (w.k ? '<div class="y">✘ إجابتك: ' + esc(w.k) + '</div>' : '') + '</div>').join('');

  const badgeHTML = fresh.map(b =>
    '<div class="badge got"><span class="bi">' + b.icon + '</span><b>' + esc(b.name) + '</b><small>' + esc(b.hint) + '</small></div>').join('');

  app().innerHTML =
    '<section class="play"><div class="result">' +
      '<div class="ring" style="--p:' + acc + '"><div class="in"><b>' + acc + '%</b><span>دقة الإجابات</span></div></div>' +
      '<div class="stars">' +
        '<span class="' + (stars >= 1 ? 'on' : '') + '">⭐</span>' +
        '<span class="' + (stars >= 2 ? 'on' : '') + '">⭐</span>' +
        '<span class="' + (stars >= 3 ? 'on' : '') + '">⭐</span>' +
      '</div>' +
      '<h1>' + title + '</h1>' +
      '<p class="sub">' + esc(ctx.meta.icon + ' ' + ctx.meta.name) + ' · ' +
        esc(ctx.unit ? ctx.unit.name : 'التحدي الشامل') + ' · الصف ' + esc(ctx.g.name) + '</p>' +
      '<div class="gain">⭐ +' + xp + ' نقطة خبرة</div>' +
      '<div class="sum-grid">' +
        '<div class="sum-box"><b>' + s.score + '</b><span>النقاط</span></div>' +
        '<div class="sum-box"><b>' + ms.right + '/' + ms.all + '</b><span>صحيحة</span></div>' +
        '<div class="sum-box"><b>' + (s.bestStreak || 0) + '</b><span>أطول سلسلة 🔥</span></div>' +
        '<div class="sum-box"><b>' + lvl.l + '</b><span>مستواك الحالي</span></div>' +
      '</div>' +
      (badgeHTML ? '<div class="badge-grid" style="max-width:720px;margin:22px auto 0">' + badgeHTML + '</div>' : '') +
      '<div class="result-actions">' +
        '<button class="btn btn-primary" id="again">🔁 العب مجدداً</button>' +
        '<a class="btn btn-gold" href="#/g/' + ctx.gid + '">🎮 لعبة أخرى</a>' +
        '<a class="btn btn-ghost" href="#/stats">📊 تقدّمي</a>' +
        (Store.d.name ? '' : '<button class="btn btn-ghost" id="addName">✏️ أضف اسمك للمتصدرين</button>') +
      '</div>' +
      (wrong ? '<div class="review"><div class="section-head" style="margin-bottom:0"><h2 style="font-size:20px">📝 راجع أخطاءك</h2></div>' + wrong + '</div>' : '') +
      '<div class="center-actions"><a class="btn btn-ghost btn-sm" href="#/">الرئيسية</a></div>' +
    '</div></section>';

  if (stars === 3) confetti(40);
  Sfx.play(acc >= 70 ? 'win' : 'tap');

  const again = $('#again');
  if (again) again.addEventListener('click', () => {
    location.hash = '#/play/' + ctx.gid + '/' + ctx.uid + '/' + ctx.mode;
  });
  const addName = $('#addName');
  if (addName) addName.addEventListener('click', () => askName(true));
  lastResult = null;
}

/* ---------------- تقدّمي ---------------- */
function viewStats() {
  document.body.classList.remove('in-game');
  const d = Store.d;
  const t = d.totals;
  const lvl = lvlInfo(d.xp);
  const accAll = t.all ? Math.round((t.right / t.all) * 100) : 0;

  const bars = Cur.grades.map(g => {
    const m = gradeMastery(g.id);
    return '<div class="bar-row"><div class="lbl"><span>' + g.icon + ' ' + esc(g.name) + '</span><span>' + m + '%</span></div>' +
      '<div class="progress"><i style="width:' + m + '%"></i></div></div>';
  }).join('');

  const unitList = Cur.grades.map(g =>
    '<div style="margin-top:16px"><b style="display:block;margin-bottom:8px">' + g.icon + ' ' + esc(g.name) + '</b>' +
    g.units.map(u => {
      const st = d.units[u.id] || { plays: 0, bestAcc: 0, best: 0 };
      return '<div class="bar-row" style="margin-bottom:9px"><div class="lbl"><span>' + u.icon + ' ' + esc(u.name) + '</span>' +
        '<span>' + (st.plays ? pct(st.bestAcc) + '% · ' + st.plays + ' جولات' : 'لم تُلعب') + '</span></div>' +
        '<div class="progress' + (st.bestAcc >= 80 ? ' gold' : '') + '"><i style="width:' + pct(st.bestAcc) + '%"></i></div></div>';
    }).join('') + '</div>').join('');

  const badges = BADGES.map(b =>
    '<div class="badge' + (d.badges[b.id] ? ' got' : '') + '"><span class="bi">' + b.icon + '</span>' +
    '<b>' + esc(b.name) + '</b><small>' + esc(b.hint) + '</small></div>').join('');

  const modes = GAME_META.map(m => {
    const n = d.modes[m.id] || 0;
    return '<div class="sum-box"><b>' + n + '</b><span>' + m.icon + ' ' + esc(m.name) + '</span></div>';
  }).join('');

  app().innerHTML =
    '<section class="section">' +
      '<div class="crumb"><a href="#/">الرئيسية</a> <span>←</span> <b>تقدّمي</b></div>' +
      '<div class="grade-head" style="--gc:var(--teal)">' +
        '<div class="gh-icon">📊</div>' +
        '<div><h1>' + esc(d.name || 'لاعب مجهول') + ' — المستوى ' + lvl.l + '</h1>' +
        '<p>' + d.xp + ' نقطة خبرة · تبقّى ' + lvl.need + ' نقطة للمستوى ' + (lvl.l + 1) + ' · دقة عامة ' + accAll + '%</p>' +
        '<div class="progress" style="margin-top:9px;max-width:340px"><i style="width:' + lvl.pct + '%"></i></div></div>' +
        '<div class="gh-actions">' +
          '<button class="btn btn-ghost btn-sm" id="editName">✏️ تغيير الاسم</button>' +
          '<a class="btn btn-ghost btn-sm" href="#/board">🏆 المتصدرون</a>' +
        '</div>' +
      '</div>' +

      '<div class="stats-strip">' +
        '<div class="stat-box"><b>' + d.totals.plays + '</b><span>لعبة مكتملة</span></div>' +
        '<div class="stat-box"><b>' + d.totals.right + '</b><span>إجابة صحيحة</span></div>' +
        '<div class="stat-box"><b>' + d.totals.streak + '</b><span>أطول سلسلة 🔥</span></div>' +
        '<div class="stat-box"><b>' + Object.keys(d.badges).length + '/' + BADGES.length + '</b><span>شارات مكتسبة</span></div>' +
      '</div>' +

      '<div class="card" style="margin-top:22px"><h3 style="font-size:19px;font-weight:900;margin-bottom:14px">🎮 جولاتك في كل لعبة</h3>' +
        '<div class="sum-grid" style="margin:0">' + modes + '</div></div>' +

      '<div class="card" style="margin-top:18px"><h3 style="font-size:19px;font-weight:900;margin-bottom:14px">🎯 إتقان الصفوف</h3>' +
        '<div class="bars">' + bars + '</div></div>' +

      '<div class="card" style="margin-top:18px"><h3 style="font-size:19px;font-weight:900;margin-bottom:6px">📚 تقدّمك في الوحدات</h3>' + unitList + '</div>' +

      '<div class="card" style="margin-top:18px"><h3 style="font-size:19px;font-weight:900;margin-bottom:14px">🏅 الشارات</h3>' +
        '<div class="badge-grid">' + badges + '</div></div>' +

      '<div class="center-actions">' +
        '<a class="btn btn-ghost" href="#/">الرئيسية</a>' +
        '<button class="btn btn-ghost" id="resetBtn">🗑 تصفير التقدّم</button>' +
      '</div>' +
    '</section>';

  $('#editName').addEventListener('click', () => askName(true));
  $('#resetBtn').addEventListener('click', () => {
    modal('<h3>تصفير التقدّم؟</h3><p>سيتم حذف النقاط والشارات والإحصاءات نهائياً من هذا الجهاز.</p>' +
      '<div class="row"><button class="btn btn-ghost" data-close>تراجع</button><button class="btn btn-primary" id="yesReset">نعم، احذف</button></div>',
      (m, close) => $('#yesReset', m).addEventListener('click', () => {
        const theme = Store.d.theme, sound = Store.d.sound;
        Store.d = defData(); Store.d.theme = theme; Store.d.sound = sound;
        Store.save(); close(); updateXP(); toast('تم تصفير التقدّم'); viewStats();
      }));
  });
}

/* ---------------- المتصدرون ---------------- */
function viewBoard() {
  document.body.classList.remove('in-game');
  const d = Store.d;
  const rows = (d.board || []).slice().sort((a, b) => b.xp - a.xp).slice(0, 30);
  const me = d.name;
  const table = rows.map((r, i) => {
    const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : (i + 1);
    const acc = r.all ? Math.round((r.right / r.all) * 100) : 0;
    return '<tr class="' + (r.name === me ? 'me' : '') + '"><td class="rank"><span class="medal">' + medal + '</span></td>' +
      '<td>' + esc(r.name) + '</td><td>' + r.xp + '</td><td>' + lvlOf(r.xp) + '</td><td>' + acc + '%</td><td>' + r.plays + '</td></tr>';
  }).join('');

  app().innerHTML =
    '<section class="section">' +
      '<div class="crumb"><a href="#/">الرئيسية</a> <span>←</span> <b>لوحة المتصدرين</b></div>' +
      '<div class="section-head"><span class="eyebrow">🏆 تحدّي أصدقاءك</span>' +
      '<h2>لوحة المتصدرين</h2><p>الترتيب محفوظ على هذا الجهاز — كلما لعبت زادت نقاطك ومركزك.</p></div>' +
      (rows.length
        ? '<div style="overflow-x:auto"><table class="tbl"><thead><tr><th>المركز</th><th>الاسم</th><th>النقاط ⭐</th><th>المستوى</th><th>الدقة</th><th>الألعاب</th></tr></thead><tbody>' + table + '</tbody></table></div>'
        : '<div class="empty"><span class="big">🏆</span>لا توجد نتائج بعد — العب جولة أولًا لتظهر هنا!</div>') +
      '<div class="center-actions">' +
        '<button class="btn btn-primary" id="editName">✏️ ' + (d.name ? 'تغيير الاسم' : 'أضف اسمك') + '</button>' +
        '<a class="btn btn-ghost" href="#/stats">📊 تقدّمي</a>' +
        '<a class="btn btn-ghost" href="#/">الرئيسية</a>' +
      '</div>' +
    '</section>';

  $('#editName').addEventListener('click', () => askName(true));
}

function askName(force) {
  const cur = Store.d.name || '';
  modal(
    '<h3>👋 ما اسمك؟</h3><p>يُستخدم الاسم في لوحة المتصدرين على هذا الجهاز فقط.</p>' +
    '<input class="field" id="nameIn" maxlength="18" placeholder="اكتب اسمك هنا" value="' + esc(cur) + '">' +
    '<div class="name-chips">' + ['أحمد', 'سارة', 'محمد', 'ليان', 'عمر'].map(n => '<button type="button" data-n="' + n + '">' + n + '</button>').join('') + '</div>' +
    '<div class="row"><button class="btn btn-ghost" data-close>لاحقاً</button><button class="btn btn-primary" id="saveName">حفظ</button></div>',
    (m, close) => {
      const inp = $('#nameIn', m);
      setTimeout(() => inp.focus(), 80);
      $$('[data-n]', m).forEach(b => b.addEventListener('click', () => { inp.value = b.dataset.n; }));
      const save = () => {
        const v = inp.value.trim().slice(0, 18);
        if (!v) { toast('اكتب اسماً من فضلك'); return; }
        Store.d.name = v;
        upsertBoard();
        Store.save();
        close();
        toast('أهلًا ' + esc(v) + '! 🎉', true);
        if (location.hash.indexOf('board') > -1 || location.hash.indexOf('stats') > -1) route();
      };
      $('#saveName', m).addEventListener('click', save);
      inp.addEventListener('keydown', e => { if (e.key === 'Enter') save(); });
    });
}

function upsertBoard() {
  const d = Store.d;
  d.board = d.board || [];
  if (!d.name) return;
  const i = d.board.findIndex(x => x.name === d.name);
  const rec = {
    name: d.name, xp: d.xp, plays: d.totals.plays,
    right: d.totals.right, all: d.totals.all, ts: Date.now()
  };
  if (i > -1) d.board[i] = Object.assign(d.board[i], rec);
  else d.board.push(rec);
  if (d.board.length > 50) d.board = d.board.sort((a, b) => b.xp - a.xp).slice(0, 50);
}

/* ---------------- كيف تلعب ---------------- */
function viewHelp() {
  document.body.classList.remove('in-game');
  const games = GAME_META.map(m =>
    '<div class="card"><div style="display:flex;gap:12px;align-items:center">' +
      '<div class="gt-icon" style="margin:0">' + m.icon + '</div>' +
      '<div><div class="gt-name">' + esc(m.name) + '</div>' +
      '<div class="chip" style="margin-top:5px">' + m.len + ' ' + esc(m.unit) + '</div></div></div>' +
      '<p style="color:var(--muted);font-size:14.5px;margin-top:11px">' + esc(m.desc) + '</p></div>').join('');

  app().innerHTML =
    '<section class="section">' +
      '<div class="crumb"><a href="#/">الرئيسية</a> <span>←</span> <b>كيف تلعب</b></div>' +
      '<div class="section-head"><span class="eyebrow">🎓 دليل سريع</span><h2>ستة ألعاب… وطريق واحد للإتقان</h2>' +
      '<p>اختر صففك ← اختر وحدة ← اختر لعبة → العب → اقرأ الشرح → كرّر حتى تتقن الوحدة.</p></div>' +
      '<div class="games-grid">' + games + '</div>' +

      '<div class="card" style="margin-top:24px"><h3 style="font-size:19px;font-weight:900;margin-bottom:12px">⭐ كيف تكسب النقاط؟</h3>' +
        '<div class="uc-topics">' +
          '<span class="chip">إجابة صحيحة = نقاط + مكافأة السرعة</span>' +
          '<span class="chip">سلسلة صحيحة 🔥 تضاعف نقاطك</span>' +
          '<span class="chip">دقة 100٪ = 40 نقطة إضافية</span>' +
          '<span class="chip">كل 4 نقاط = نقطة خبرة XP</span>' +
        '</div>' +
        '<p style="color:var(--muted);margin-top:12px;font-size:14.5px">النقاط ترفع مستواك ومركزك في لوحة المتصدرين، و80٪ فأعلى في الوحدة تمنحك شارة 👑.</p></div>' +

      '<div class="faq" style="margin-top:24px">' +
        '<details open><summary>هل تعمل المنصة بدون إنترنت؟</summary><p>نعم. بعد أول زيارة تُخزَّن الملفات في متصفحك (Service Worker) وتعمل الألعاب كاملة دون اتصال.</p></details>' +
        '<details><summary>هل هي مجانية بالكامل؟</summary><p>نعم، مجانية بالكامل ولا تحتوي إعلانات ولا تسجيل حسابات — تقدّمك محفوظ داخل جهازك فقط.</p></details>' +
        '<details><summary>أنا معلم… هل يمكن إضافة أسئلة؟</summary><p>بالتأكيد. ملفات الأسئلة موجودة في <code>js/data/g1.js</code> و<code>g2.js</code> و<code>g3.js</code> بصيغة بسيطة ومعلّمة بالعربية، ويمكنك إضافة وحدات أو أسئلة ثم إعادة تشغيل <code>node validate.node.js</code> للتحقق.</p></details>' +
        '<details><summary>لماذا تتغيّر الأسئلة في كل مرة؟</summary><p>لأن الأسئلة تُخلط عشوائياً عند كل جولة، وبعضها يُولَّد بمعادلات مختلفة في كل مرة — فلا تحفظ، بل فهم!</p></details>' +
        '<details><summary>كيف ألعب بلوحة المفاتيح؟</summary><p>في الاختبار السريع وصح/خطأ وسباق الوقت استخدم الأرقام 1-4 لاختيار الخيار، وEnter للانتقال للسؤال التالي.</p></details>' +
      '</div>' +

      '<div class="center-actions"><a class="btn btn-primary" href="#/">🚀 ابدأ اللعب</a>' +
      '<a class="btn btn-ghost" href="../index.html">منصة المعلم الليبي</a></div>' +
    '</section>';
}

/* ---------------- أدوات مساعدة للعرض ---------------- */
function unitAcc(uid) { const u = Store.d.units[uid]; return u ? (u.bestAcc || 0) : 0; }
function gradeMastery(gid) {
  const g = Cur.grade(gid);
  if (!g || !g.units.length) return 0;
  const sum = g.units.reduce((a, u) => a + unitAcc(u.id), 0);
  return Math.round(sum / g.units.length);
}
function updateXP() {
  const el = $('#xpVal');
  if (el) el.textContent = Store.d.xp;
}

/* =====================================================
   التنقل
   ===================================================== */
function setActiveNav(key) {
  $$('#mainnav a').forEach(a => a.classList.toggle('active', a.dataset.nav === key));
}

function route() {
  const hash = (location.hash || '#/').replace(/^#/, '');
  const parts = hash.split('/').filter(p => p !== '');
  killGame();
  document.body.classList.remove('in-game');

  const head = parts[0] || '';
  if (!head) { viewHome(); setActiveNav('home'); }
  else if (head === 'g') { viewGrade(parts[1]); setActiveNav(''); }
  else if (head === 'play') { viewPlay(parts[1], parts[2], parts[3]); setActiveNav(''); }
  else if (head === 'r') { viewResults(); setActiveNav(''); }
  else if (head === 'stats') { viewStats(); setActiveNav('stats'); }
  else if (head === 'board') { viewBoard(); setActiveNav('board'); }
  else if (head === 'help') { viewHelp(); setActiveNav('help'); }
  else { viewHome(); setActiveNav('home'); }

  if (head !== 'play') window.scrollTo({ top: 0, behavior: 'auto' });
}

/* =====================================================
   الإقلاع
   ===================================================== */
function boot() {
  Store.load();
  document.body.dataset.theme = Store.d.theme || 'light';
  const tBtn = $('#themeBtn'), sBtn = $('#soundBtn');
  if (tBtn) tBtn.textContent = Store.d.theme === 'dark' ? '☀️' : '🌙';
  if (sBtn) { sBtn.textContent = Store.d.sound ? '🔊' : '🔇'; sBtn.classList.toggle('off', !Store.d.sound); }
  updateXP();

  /* قائمة الجوال */
  const mnav = document.createElement('div');
  mnav.className = 'mobile-nav';
  mnav.innerHTML =
    '<a href="#/">الرئيسية</a><a href="#/stats">تقدّمي</a>' +
    '<a href="#/board">المتصدرون</a><a href="#/help">كيف تلعب</a>';
  document.body.appendChild(mnav);
  const menuBtn = $('#menuBtn');
  if (menuBtn) menuBtn.addEventListener('click', () => mnav.classList.toggle('open'));
  mnav.addEventListener('click', e => { if (e.target.tagName === 'A') mnav.classList.remove('open'); });

  if (tBtn) tBtn.addEventListener('click', () => {
    Store.d.theme = Store.d.theme === 'dark' ? 'light' : 'dark';
    document.body.dataset.theme = Store.d.theme;
    tBtn.textContent = Store.d.theme === 'dark' ? '☀️' : '🌙';
    Store.save();
    Sfx.play('tap');
  });
  if (sBtn) sBtn.addEventListener('click', () => {
    Store.d.sound = !Store.d.sound;
    sBtn.textContent = Store.d.sound ? '🔊' : '🔇';
    sBtn.classList.toggle('off', !Store.d.sound);
    Store.save();
    if (Store.d.sound) Sfx.play('good');
    toast(Store.d.sound ? 'الصوت مفعّل' : 'الصوت مكتوم');
  });

  /* المنهج */
  Cur.init();
  if (!Cur.gradesReady()) {
    app().innerHTML = '<section class="section"><div class="empty"><span class="big">📚</span>' +
      '<b>تعذّر تحميل بنك الأسئلة</b><p>تأكد من وجود ملفات <code>js/data/g1.js</code> و<code>g2.js</code> و<code>g3.js</code>.</p></div></section>';
    return;
  }

  /* الاسم عند أول استخدام للوحة */
  window.addEventListener('hashchange', route);
  route();

  /* خدمة العملاء (تعمل بدون إنترنت) — تُعطَّل محلياً لتسهيل التطوير */
  const host = location.hostname;
  const local = host === 'localhost' || host === '127.0.0.1' || host === '';
  if ('serviceWorker' in navigator && !local && location.protocol.indexOf('http') === 0) {
    navigator.serviceWorker.register('./sw.js').catch(() => {});
  }
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
else boot();
