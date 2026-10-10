/* =====================================================
   curriculum.js — تجميع المنهج وبنوك الأسئلة
   يعتمد على window.LIBYA_MATH القادم من js/data/*.js
   ===================================================== */
'use strict';

/** تعريف الألعاب الست (المتاحة للوحدات التي تملك نوع الأسئلة الكافي) */
const GAME_META = [
  { id: 'quiz',   name: 'اختبار سريع',      icon: '⚡', desc: '١٠ أسئلة واختيار من متعدد، مع شرح بعد كل إجابة ونقاط سرعة.', need: 'mc',   min: 10, len: 10, unit: 'سؤال' },
  { id: 'arcade', name: 'سباق الوقت',       icon: '⏱️', desc: '٦٠ ثانية فقط! أجب بأسرع ما تستطيع وحافظ على ٣ أرواح.',      need: 'mc',   min: 12, len: 20, unit: 'سؤال', timed: 60 },
  { id: 'tf',     name: 'صح أم خطأ',        icon: '✅', desc: '١٢ جولة سريعة لتقييم العبارات، والسلسلة تضاعف نقاطك.',      need: 'tf',   min: 8,  len: 12, unit: 'عبارة' },
  { id: 'input',  name: 'اكتب الإجابة',     icon: '⌨️', desc: '١٠ أسئلة بلا خيارات، اكتب الحل بنفسك على لوحة الأرقام.',   need: 'num',  min: 8,  len: 10, unit: 'سؤال' },
  { id: 'match',  name: 'المطابقة',         icon: '🧩', desc: 'طابق كل مصطلح بمقابله، ٣ جولات من ٤ أزواج.',              need: 'pairs', min: 3, len: 3,  unit: 'جولة' },
  { id: 'order',  name: 'ترتيب الأرقام',    icon: '🎚️', desc: 'رتّب الأعداد تصاعدياً أو تنازلياً بالنقر بالترتيب الصحيح.', need: 'order', min: 3, len: 4,  unit: 'جولة' }
];

const Cur = {
  grades: [],
  _byId: {},

  /** تجميع الصفوف بعد تحميل الملفات */
  init() {
    const src = (typeof window !== 'undefined' && Array.isArray(window.LIBYA_MATH)) ? window.LIBYA_MATH : [];
    const order = ['g1', 'g2', 'g3'];
    this.grades = src
      .slice()
      .sort((a, b) => order.indexOf(a.id) - order.indexOf(b.id))
      .map(g => {
        const grade = Object.assign({}, g);
        grade.units = (g.units || []).map(u => Object.assign({}, u, { gradeId: g.id }));
        grade.items = this._flatten(grade);
        return grade;
      });
    this._byId = {};
    this.grades.forEach(g => { this._byId[g.id] = g; });
    return this;
  },

  _flatten(grade) {
    const out = {};
    ['mc', 'tf', 'num', 'pairs', 'order'].forEach(t => { out[t] = []; });
    let broken = [];
    grade.units.forEach(u => {
      let list = [];
      try { list = u.items() || []; } catch (e) { broken.push(u.id + ': ' + e.message); list = []; }
      list.forEach(it => {
        if (it && out[it.t]) out[it.t].push(Object.assign({ unitId: u.id }, it));
      });
    });
    if (broken.length && typeof console !== 'undefined') console.warn('تعذّر توليد أسئلة لبعض الوحدات:', broken);
    return out;
  },

  gradesReady() { return this.grades.length > 0; },
  grade(id) { return this._byId[id] || null; },

  unit(gid, uid) {
    const g = this.grade(gid);
    if (!g) return null;
    return g.units.find(u => u.id === uid) || null;
  },

  /** أسئلة وحدة من نوع معيّن (مخلوطة) */
  pool(gid, uid, type) {
    const g = this.grade(gid);
    if (!g) return [];
    const u = g.units.find(x => x.id === uid);
    if (!u) return [];
    const all = [];
    let broken = [];
    try { (u.items() || []).forEach(it => { if (it && it.t === type) all.push(it); }); }
    catch (e) { broken.push(e.message); }
    if (broken.length && typeof console !== 'undefined') console.warn('pool:', broken);
    return R.shuffle(all);
  },

  /** كل أسئلة الصف من نوع معين (للتحدي الشامل) */
  gradePool(gid, type) {
    const g = this.grade(gid);
    if (!g) return [];
    return R.shuffle(g.items[type] || []);
  },

  /** كل بنك الأسئلة من نوع معين */
  allPool(type) {
    const out = [];
    this.grades.forEach(g => (g.items[type] || []).forEach(it => out.push(it)));
    return R.shuffle(out);
  },

  /** عدد العناصر لكل نوع داخل وحدة */
  counts(gid, uid) {
    const c = { mc: 0, tf: 0, num: 0, pairs: 0, order: 0 };
    const g = this.grade(gid);
    const u = g && g.units.find(x => x.id === uid);
    if (!u) return c;
    let list = [];
    try { list = u.items() || []; } catch (e) { return c; }
    list.forEach(it => { if (it && c[it.t] !== undefined) c[it.t]++; });
    return c;
  },

  /** حالة توفر كل لعبة لوحدة */
  availability(gid, uid) {
    const c = this.counts(gid, uid);
    const out = {};
    GAME_META.forEach(m => {
      const enough = (c[m.need] || 0) >= m.min;
      out[m.id] = { ok: enough, have: c[m.need] || 0, need: m.min };
    });
    return out;
  },

  /** حالة التوفر للتحدي الشامل (كل أسئلة الصف) */
  gradeCounts(gid) {
    const g = this.grade(gid);
    const c = { mc: 0, tf: 0, num: 0, pairs: 0, order: 0 };
    if (!g) return c;
    ['mc', 'tf', 'num', 'pairs', 'order'].forEach(t => { c[t] = (g.items[t] || []).length; });
    return c;
  },

  gradeAvailability(gid) {
    const c = this.gradeCounts(gid);
    const out = {};
    GAME_META.forEach(m => { out[m.id] = { ok: (c[m.need] || 0) >= m.min, have: c[m.need] || 0, need: m.min }; });
    return out;
  },

  /** مجموع عدد الأسئلة لكل الصفوف */
  totals() {
    let q = 0, units = 0;
    const perGrade = {};
    this.grades.forEach(g => {
      const c = { mc: 0, tf: 0, num: 0, pairs: 0, order: 0 };
      g.units.forEach(u => {
        units++;
        let list = [];
        try { list = u.items() || []; } catch (e) { list = []; }
        list.forEach(it => { if (it && c[it.t] !== undefined) { c[it.t]++; q++; } });
      });
      perGrade[g.id] = Object.assign({ total: Object.values(c).reduce((a, b) => a + b, 0) }, c);
    });
    // كل جولة مطابقة/ترتيب تساوي 4 عناصر في العرض
    return { questions: q, units, grades: this.grades.length, perGrade };
  }
};

/* اجعلها متاحة عالمياً */
if (typeof window !== 'undefined') { window.Cur = Cur; window.GAME_META = GAME_META; }
