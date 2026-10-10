/* =====================================================
   منصة ألعاب الرياضيات — أدوات مساعدة (Helpers)
   يُحمَّل هذا الملف قبل ملفات الأسئلة (data/*.js)
   يوفّر: R (عشوائي) + M (تنسيق) + بُناة الأسئلة + التحقق
   ===================================================== */
'use strict';

/* ---------------- R: أدوات عشوائية ---------------- */
const R = {
  /** عدد صحيح من a إلى b (شامل) */
  int(a, b) { return Math.floor(Math.random() * (b - a + 1)) + a; },

  /** عشوائي عشري بدقة d خانات */
  float(a, b, d = 2) {
    const v = a + Math.random() * (b - a);
    return +v.toFixed(d);
  },

  /** عنصر عشوائي من مصفوفة */
  pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; },

  /** نسخة مخلوطة من المصفوفة (لا تعدّل الأصل) */
  shuffle(arr) {
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  },

  /** صحيح باحتمال p (0..1) */
  chance(p) { return Math.random() < p; },

  /** n قيم مختلفة عبر مولّد fn (يمنع التكرار) */
  uniq(n, fn) {
    const seen = new Set(), out = [];
    let guard = 0;
    while (out.length < n && guard++ < n * 60) {
      const v = fn();
      const s = JSON.stringify(v);
      if (!seen.has(s)) { seen.add(s); out.push(v); }
    }
    return out;
  }
};

/* ---------------- M: تنسيق العرض ---------------- */
const M = {
  /** تنسيق رقم: يحذف الصفر العشري الزائد */
  n(x) {
    if (typeof x === 'number') {
      const s = (+x.toFixed(6)).toString();
      return s.replace(/\.0$/, '');
    }
    return String(x);
  },
  /** كسر: frac(3,4) => "3/4" */
  frac(a, b) { return a + '/' + b; },
  /** جذر: root(49) => "√49" */
  root(x) { return '√' + x; },
  /** تربيع: sq(5) => "5²" */
  sq(x) { return x + '²'; },
  /** مكعب */
  cube(x) { return x + '³'; },
  /** نسبة مئوية */
  pct(x) { return x + '%'; },
  /** قيمة مطلقة نصية */
  abs(x) { return '|' + x + '|'; },
  /** درجة */
  deg(x) { return x + '°'; },
  /** جزء من عشرة: 2.5 => 2+5/10 (اختياري) */
  mixed(a, b) { return a + '+' + b + '/' + (a >= 0 ? '' : '-'); }
};

/* ---------------- بُناة الأسئلة ---------------- */

/**
 * اختيار من متعدد
 * @param {{q:string,a:any,o:any[],e?:string,k?:string}} d
 * q: نص السؤال — a: الإجابة الصحيحة — o: بدائل خاطئة (3 عادة) — e: شرح — k: اسم المهارة
 */
function mc(d) {
  const opts = R.shuffle(dedup([d.a].concat(d.o || [])));
  return { t: 'mc', q: String(d.q), o: opts, c: opts.indexOf(String(d.a)), e: d.e || '', k: d.k || '' };
}

function dedup(arr) {
  const seen = new Set(), out = [];
  for (const v of arr) {
    const s = String(v);
    if (!seen.has(s)) { seen.add(s); out.push(s); }
  }
  return out;
}

/**
 * صح / خطأ
 * @param {{q:string,a:boolean,e?:string,k?:string}} d
 */
function tf(d) {
  return { t: 'tf', q: String(d.q), c: !!d.a, e: d.e || '', k: d.k || '' };
}

/**
 * إجابة رقمية يكتبها اللاعب
 * @param {{q:string,a:any,alt?:any[],e?:string,k?:string}} d
 * a: الإجابة (رقم أو نص) — alt: صيغ مقبولة أخرى
 */
function num(d) {
  return { t: 'num', q: String(d.q), a: d.a, alt: d.alt || [], e: d.e || '', k: d.k || '' };
}

/**
 * لعبة المطابقة: أزواج قصيرة (يسار = يمين)
 * @param {{p:any[][],e?:string,k?:string}} d
 * p: مصفوفة أزواج [يسار، يمين] بطول 4 أو 5، اليسار فريد واليمين فريد
 */
function pairs(d) {
  return { t: 'pairs', p: d.p.map(x => [String(x[0]), String(x[1])]), e: d.e || '', k: d.k || '' };
}

/**
 * ترتيب تصاعدي/تنازلي بالنقر
 * @param {{v:any[],dir?:'asc'|'desc',q?:string,e?:string,k?:string}} d
 */
function order(d) {
  return {
    t: 'order',
    v: d.v.map(x => (typeof x === 'number' ? x : String(x))),
    dir: d.dir === 'desc' ? 'desc' : 'asc',
    q: d.q || '',
    e: d.e || '',
    k: d.k || ''
  };
}

/** تكرار مولّد n مرة: many(20, i => num({...})) */
function many(n, fn) {
  const out = [];
  for (let i = 0; i < n; i++) out.push(fn(i));
  return out;
}

/* ---------------- تطبيع الإجابة ---------------- */
function normAns(v) {
  if (typeof v === 'number') return String(+v.toFixed(6)).replace(/\.?0+$/, '');
  let s = String(v == null ? '' : v).trim();
  s = s.replace(/[٠-٩]/g, d => String(d.charCodeAt(0) - 0x0660))
       .replace(/[۰-۹]/g, d => String(d.charCodeAt(0) - 0x06F0))
       .replace(/٫/g, '.')
       .replace(/\s+/g, '')
       .replace(/^\+/, '')
       .replace(/[،,]/g, '');
  s = s.replace(/\.$/, '');
  if (/^-??\d+(\.\d+)?0+$/.test(s) && s.indexOf('.') > -1) s = s.replace(/0+$/, '').replace(/\.$/, '');
  return s;
}

/** هل تُقبل الإجابة المكتوبة مقابل الإجابة الصحيحة؟ */
function acceptAns(given, item) {
  const g = normAns(given);
  if (!g) return false;
  const list = [item.a].concat(item.alt || []);
  for (const cand of list) {
    if (normAns(cand) === g) return true;
    // مقارنة رقمية (متساوية قيمياً)
    const a = Number(normAns(cand)), b = Number(g);
    if (!isNaN(a) && !isNaN(b) && Math.abs(a - b) < 1e-6) return true;
    // كسور مكافئة: 1/2 == 0.5
    const fa = fracVal(cand), fb = fracVal(given);
    if (fa !== null && fb !== null && Math.abs(fa - fb) < 1e-6) return true;
  }
  return false;
}

function fracVal(v) {
  const s = normAns(v);
  const m = /^(-?\d+(?:\.\d+)?)\/(-?\d+(?:\.\d+)?)$/.exec(s);
  if (!m) return null;
  const d = Number(m[2]);
  if (!d) return null;
  return Number(m[1]) / d;
}

/* ---------------- التحقق من صحة بنك الأسئلة ---------------- */
function validateItem(it, where) {
  const errs = [];
  const push = m => errs.push(where + ': ' + m);
  if (!it || typeof it !== 'object') { push('عنصر غير صالح'); return errs; }
  if (!it.q && it.t !== 'pairs' && it.t !== 'order') push('نص السؤال فارغ');
  if (it.e !== undefined && it.e !== null && typeof it.e !== 'string') push('الشرح ليس نصاً');

  if (it.t === 'mc') {
    if (!Array.isArray(it.o) || it.o.length < 2) push('خيارات غير كافية');
    else {
      if (it.c < 0 || it.c >= it.o.length) push('فهرس الإجابة الصحيحة خارج النطاق');
      if (new Set(it.o.map(String)).size !== it.o.length) push('خيارات مكررة');
      if (String(it.o[it.c]) === undefined) push('الإجابة الصحيحة مفقودة');
    }
    if (!it.e) push('بدون شرح');
  } else if (it.t === 'tf') {
    if (typeof it.c !== 'boolean') push('قيمة tf ليست منطقية');
  } else if (it.t === 'num') {
    if (it.a === undefined || it.a === null || it.a === '') push('الإجابة مفقودة');
    const n = Number(normAns(it.a));
    const raw = String(it.a);
    const fracOk = fracVal(raw) !== null;
    if (isNaN(n) && !fracOk) push('الإجابة ليست رقماً صالحاً: ' + raw);
    if (!it.e) push('بدون شرح');
  } else if (it.t === 'pairs') {
    if (!Array.isArray(it.p) || it.p.length < 3) push('أزواج غير كافية');
    else {
      const L = it.p.map(x => x[0]), Rt = it.p.map(x => x[1]);
      if (new Set(L).size !== L.length) push('أزواج اليسار مكررة');
      if (new Set(Rt).size !== Rt.length) push('أزواج اليمين مكررة');
      it.p.forEach((x, i) => {
        if (!x[0] || !x[1]) push('زوج فارغ #' + i);
        if (x[0] === x[1]) push('الزوج متطابق الطرفين #' + i);
      });
    }
  } else if (it.t === 'order') {
    if (!Array.isArray(it.v) || it.v.length < 4) push('قيم الترتيب أقل من 4');
    else if (new Set(it.v.map(String)).size !== it.v.length) push('قيم مكررة في الترتيب');
  } else {
    push('نوع عنصر غير معروف: ' + it.t);
  }
  return errs;
}

function validateGrade(g) {
  const errs = [];
  if (!g || !g.id || !g.name || !Array.isArray(g.units)) {
    return ['بنية الصف غير صالحة'];
  }
  const seenIds = new Set();
  const stats = [];
  for (const u of g.units) {
    if (!u.id || !u.name || typeof u.items !== 'function') {
      errs.push(g.id + ': وحدة ناقصة (id/name/items)');
      continue;
    }
    if (seenIds.has(u.id)) errs.push('معرّف وحدة مكرر: ' + u.id);
    seenIds.add(u.id);
    let items;
    try { items = u.items(); }
    catch (e) { errs.push(u.id + ': خطأ في توليد الأسئلة → ' + e.message); continue; }
    if (!Array.isArray(items) || !items.length) { errs.push(u.id + ': لا توجد أسئلة'); continue; }

    const counts = { mc: 0, tf: 0, num: 0, pairs: 0, order: 0 };
    const uids = new Set();
    items.forEach((it, i) => {
      const w = u.id + ' #' + i + ' [' + (it && it.t) + ']';
      errs.push(...validateItem(it, w));
      if (it && counts[it.t] !== undefined) counts[it.t]++;
      if (it && it.q) {
        const key = it.t + '|' + it.q;
        if (uids.has(key)) errs.push(w + ': سؤال مكرر');
        uids.add(key);
      }
    });
    stats.push({ id: u.id, name: u.name, total: items.length, ...counts });
  }
  return { errors: errs, stats };
}

/* تصدير للاختبار داخل Node (اختياري) */
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { R, M, mc, tf, num, pairs, order, many, normAns, acceptAns, validateGrade, validateItem, fracVal };
}
