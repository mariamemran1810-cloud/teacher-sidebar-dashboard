/* يلف ملفات البيانات في دالة معزولة لتفادي تعارض الأسماء العامة بين الملفات
   الاستخدام: node wrap-data.js [الملف...] (افتراضي: js/data/*.js) */
'use strict';
const fs = require('fs');
const path = require('path');

const dir = path.join(__dirname, 'js', 'data');
let files = process.argv.slice(2);
if (!files.length) files = fs.readdirSync(dir).filter(f => f.endsWith('.js')).map(f => path.join(dir, f));

for (const f of files) {
  const p = path.isAbsolute(f) ? f : path.join(__dirname, f);
  const src = fs.readFileSync(p, 'utf8');
  if (/\(function\s*\(\)\s*\{[\s\S]*window\.LIBYA_MATH/.test(src.slice(0, 400))) {
    console.log('✓ ملثوف مسبقاً: ' + path.basename(p));
    continue;
  }
  const out = '(function () {\n' + src.replace(/\s*$/, '') + '\n})();\n';
  fs.writeFileSync(p, out, 'utf8');
  console.log('✓ تم التغليف: ' + path.basename(p));
}
