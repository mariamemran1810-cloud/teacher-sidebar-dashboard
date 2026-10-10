/* فاحص بنك الأسئلة — يُشغَّل من سطر الأوامر:
   node validate.node.js            (يفحص كل ملفات js/data/*.js)
   node validate.node.js js/data/g2.js
   يطبع جدداً بعدد الأسئلة لكل وحدة ونوع، ويُخرج برمز 1 عند وجود أخطاء. */
'use strict';
const fs = require('fs');
const path = require('path');
const h = require('./js/helpers.js');

const dir = path.join(__dirname, 'js', 'data');
let files = process.argv.slice(2);
if (!files.length) {
  files = fs.existsSync(dir)
    ? fs.readdirSync(dir).filter(f => f.endsWith('.js')).map(f => path.join(dir, f))
    : [];
}

const windowObj = {};
const grades = [];

for (const f of files) {
  const code = fs.readFileSync(path.isAbsolute(f) ? f : path.join(__dirname, f), 'utf8');
  const fn = new Function(
    'window', 'R', 'M', 'mc', 'tf', 'num', 'pairs', 'order', 'many', 'normAns', 'module',
    code
  );
  try {
    fn(windowObj, h.R, h.M, h.mc, h.tf, h.num, h.pairs, h.order, h.many, h.normAns, undefined);
  } catch (e) {
    console.error('✗ تعذّر تحميل ' + f + ': ' + e.message);
    process.exitCode = 1;
  }
}

const list = windowObj.LIBYA_MATH || [];
let totalErr = 0, totalItems = 0;
const REQ = { mc: 12, tf: 10, num: 10, pairs: 3, order: 4 };

for (const g of list) {
  const res = h.validateGrade(g);
  console.log('\n=== ' + g.name + ' (' + g.id + ') — ' + (g.units ? g.units.length : 0) + ' وحدات ===');
  const header = ['الوحدة', 'الإجمالي', 'mc', 'tf', 'num', 'pairs', 'order'];
  const rows = res.stats.map(s => [s.name, s.total, s.mc, s.tf, s.num, s.pairs, s.order]);
  const sum = res.stats.reduce((a, s) => ({
    total: a.total + s.total, mc: a.mc + s.mc, tf: a.tf + s.tf, num: a.num + s.num,
    pairs: a.pairs + s.pairs, order: a.order + s.order
  }), { total: 0, mc: 0, tf: 0, num: 0, pairs: 0, order: 0 });
  rows.push(['الإجمالي', sum.total, sum.mc, sum.tf, sum.num, sum.pairs, sum.order]);
  totalItems += sum.total;

  const widths = header.map((hh, i) => Math.max(String(hh).length, ...rows.map(r => String(r[i]).length)));
  const line = r => '  ' + r.map((c, i) => String(c).padStart(widths[i], ' ')).join('  ');
  console.log(line(header));
  rows.forEach(r => console.log(line(r)));

  // تنبيهات نقص الأسئلة
  for (const s of res.stats) {
    for (const t of Object.keys(REQ)) {
      if (s[t] < REQ[t]) console.log('  ⚠ ' + s.name + ': ' + t + ' = ' + s[t] + ' (المطلوب ' + REQ[t] + ')');
    }
  }
  if (res.errors.length) {
    totalErr += res.errors.length;
    console.log('  ✗ أخطاء (' + res.errors.length + '):');
    res.errors.slice(0, 60).forEach(e => console.log('    - ' + e));
    if (res.errors.length > 60) console.log('    ... و' + (res.errors.length - 60) + ' أخرى');
  } else {
    console.log('  ✓ لا أخطاء');
  }
}

if (!list.length) console.log('لم يُعثر على أي صف (تحقق من مسارات الملفات).');
console.log('\nعدد الأسئلة الكلي: ' + totalItems + ' | الأخطاء: ' + totalErr);
process.exitCode = process.exitCode || (totalErr ? 1 : 0);
