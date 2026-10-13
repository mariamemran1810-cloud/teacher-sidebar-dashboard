/* فحص برمجي سريع لبنية المشروع وصفحاته — بدون متصفح
   التشغيل: node tests/smoke.js  (أو npm test) */
'use strict';
const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..');
let failures = 0;
let checks = 0;

function ok(cond, msg){
  checks++;
  if(!cond){ failures++; console.log('  ✗ ' + msg); }
}
function read(f){ return fs.readFileSync(path.join(ROOT, f), 'utf8'); }

/* ---------- 1) تحميل السكربتات كما يفعل المتصفح ---------- */
const code = read('js/data.js') + '\n' + read('js/pages.js') + '\n';
let api;
try{
  api = new Function(code + '\nreturn { MTL_DOCS, MTLPages, MTL_DEFAULT_SETTINGS, MTL_ICONS };')();
}catch(e){
  console.log('✗ فشل تنفيذ السكربتات: ' + e.message);
  process.exit(1);
}
const { MTL_DOCS, MTLPages, MTL_DEFAULT_SETTINGS, MTL_ICONS } = api;

console.log('== فحص التعريفات ==');
ok(Array.isArray(MTL_DOCS) && MTL_DOCS.length === 10, 'عدد الملفات يجب أن يكون 10');
ok(Object.keys(MTL_DEFAULT_SETTINGS).length >= 8, 'الإعدادات الافتراضية ناقصة');
ok(Object.keys(MTL_ICONS).length === 10, 'عدد الأيقونات يجب أن يكون 10');

const KNOWN = ['cover','index','divider','table','form','text','certificate'];
let totalPages = 0;

MTL_DOCS.forEach(doc=>{
  console.log('== الملف: ' + doc.title + ' ==');
  ok(doc.pages && doc.pages.length >= 4, doc.id + ': عدد الصفحات أقل من 4');
  ok(doc.pages[0].type === 'cover',  doc.id + ': الصفحة الأولى يجب أن تكون غلافًا');
  ok(doc.pages[1].type === 'index',   doc.id + ': الصفحة الثانية يجب أن تكون فهرسًا');
  ok(doc.pages.some(p=>p.type==='divider'), doc.id + ': لا يوجد فاصل');
  ok(doc.pages.some(p=>p.type==='table'),   doc.id + ': لا يوجد جدول');
  ok(typeof MTL_ICONS[doc.icon] === 'string', doc.id + ': أيقونة غير موجودة');

  const html = MTLPages.render(doc).join('\n');
  totalPages += doc.pages.length;

  ok(MTLPages.render(doc).length === doc.pages.length, doc.id + ': عدد الصفحات المولّدة غير مطابق');
  ok(html.indexOf('undefined') === -1, doc.id + ': ظهر نص undefined في المخرجات');
  ok(html.indexOf('NaN') === -1, doc.id + ': ظهر NaN في المخرجات');
  ok((html.match(/<article/g) || []).length === doc.pages.length, doc.id + ': عدد عناصر article');
  ok((html.match(/<\/article>/g) || []).length === doc.pages.length, doc.id + ': عدد إغلاق article');
  ok(html.indexOf('<html') === -1, doc.id + ': تداخل وسم html كامل');

  /* فحص مفاتيح التحرير */
  const keys = (html.match(/data-k="([^"]+)"/g) || []).map(s=>s.slice(8, -1));
  ok(keys.length > 10, doc.id + ': مفاتيح التحرير قليلة جدًا');
  const counts = {};
  keys.forEach(k => counts[k] = (counts[k] || 0) + 1);
  Object.keys(counts).forEach(k=>{
    const isTitleKey = /#title$/.test(k);
    if(counts[k] > 1 && !isTitleKey){
      ok(false, doc.id + ': مفتاح مكرر غير العنوان: ' + k);
    }
    ok(/^\w+#\d+#[\w]+$/.test(k), doc.id + ': صيغة مفتاح خاطئة: ' + k);
  });

  /* فحص الجداول */
  doc.pages.forEach((p, i)=>{
    if(KNOWN.indexOf(p.type) === -1) ok(false, doc.id + ' ص ' + (i+1) + ': نوع صفحة غير معروف ' + p.type);
    if(p.type === 'table'){
      const h = p.headers || [], w = p.widths || [], r = p.rows || [];
      ok(h.length >= 3, doc.id + ' ص ' + (i+1) + ': أعمدة قليلة');
      ok(h.length === w.length, doc.id + ' ص ' + (i+1) + ': عدد العناوين ≠ عدد العروض');
      const sum = w.reduce((a,b)=>a + (Number(b)||0), 0);
      ok(Math.abs(sum - 100) <= 1.5, doc.id + ' ص ' + (i+1) + ': مجموع العروض = ' + sum);
      r.forEach((row, ri)=>{
        ok(row.length === h.length,
          doc.id + ' ص ' + (i+1) + ' صف ' + (ri+1) + ': عدد الخلايا ' + row.length + ' ≠ ' + h.length);
      });
    }
    if(p.type === 'certificate'){
      ok(p.title && p.name, doc.id + ' ص ' + (i+1) + ': شهادة ناقصة');
    }
    if(p.type === 'form'){
      ok((p.fields || []).length >= 4, doc.id + ' ص ' + (i+1) + ': حقول قليلة');
    }
  });

  /* الفهرس يجب أن يحوي صفحات فعلية */
  const toc = MTLPages.buildTOC(doc);
  ok(toc.length >= 4, doc.id + ': فهرس قصير');
  toc.forEach(e=>{
    ok(e.page >= 1 && e.page <= doc.pages.length, doc.id + ': رقم صفحة خاطئ في الفهرس');
    ok(doc.pages[e.pageIndex] && doc.pages[e.pageIndex].title === e.title,
      doc.id + ': عنوان الفهرس لا يطابق صفحة المحتوى');
  });
});

console.log('== إجمالي الصفحات: ' + totalPages + ' ==');
ok(totalPages >= 50, 'الإجمالي أقل من 50 صفحة');

/* ---------- 2) فحص ملفات الواجهة ---------- */
console.log('== فحص ملفات الواجهة ==');
const htmlSrc = read('index.html');
['css/app.css','css/print.css','js/data.js','js/pages.js','js/store.js','js/app.js'].forEach(f=>{
  ok(htmlSrc.indexOf(f) !== -1, 'index.html لا يشير إلى ' + f);
  ok(fs.existsSync(path.join(ROOT, f)), 'الملف مفقود: ' + f);
});
['id="cards"','id="pages"','id="thumbs"','id="view-doc"','id="view-home"','data-set="teacher"','data-set="school"','data-set="year"'].forEach(t=>{
  ok(htmlSrc.indexOf(t) !== -1, 'index.html ينقصه عنصر: ' + t);
});
ok(/dir="rtl"/.test(htmlSrc), 'index.html خالٍ من اتجاه RTL');
ok(/lang="ar"/.test(htmlSrc), 'index.html خالٍ من اللغة العربية');
['btn-settings','btn-help','modal-settings','btn-print-doc','btn-print-page','import-file'].forEach(id=>{
  ok(htmlSrc.indexOf(id) !== -1, 'index.html ينقصه عنصر: ' + id);
});

const printCss = read('css/print.css');
ok(/@page\s*{\s*size:\s*A4/.test(printCss), 'print.css: لا يوجد @page A4');
ok(/margin:\s*0/.test(printCss), 'print.css: هوامش الصفحة ليست صفرية');
ok(/print-current/.test(printCss), 'print.css: توجد قواعد الطباعة للصفحة الحالية');

const appCss = read('css/app.css');
ok(/--rose/.test(appCss) && /--brown/.test(appCss) && /--paper/.test(appCss), 'app.css: لوحة الألوان ناقصة');
ok(/210mm/.test(appCss) && /297mm/.test(appCss), 'app.css: مقاس A4 غير محدد');
ok(/direction:\s*rtl/.test(appCss), 'app.css: اتجاه RTL غير محدد');

const appJs = read('js/app.js');
['localStorage','window.print','data-bind','data-k'].forEach(t=>{
  ok(appJs.indexOf(t) !== -1, 'app.js ينقصه: ' + t);
});

/* ---------- 3) فحص التخزين ---------- */
console.log('== فحص نموذج التخزين ==');
const storeSrc = read('js/store.js');
['getSettings','saveSettings','setValue','setValues','exportPayload','importPayload','clearAll'].forEach(fn=>{
  ok(storeSrc.indexOf('function ' + fn) !== -1, 'store.js ينقصه: ' + fn);
});

/* ---------- النتيجة ---------- */
console.log('----------------------------------');
if(failures === 0){
  console.log('✓ نجح كل الفحوصات (' + checks + ' فحصًا)');
  process.exit(0);
}else{
  console.log('✗ فشل ' + failures + ' من ' + checks + ' فحصًا');
  process.exit(1);
}
