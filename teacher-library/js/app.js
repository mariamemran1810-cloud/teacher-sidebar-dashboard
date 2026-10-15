/* =========================================================
   مكتبتي التعليمية للمعلمة الليبية — منطق التطبيق
   بطاقات الملفات • عارض الصفحات • التحرير والحفظ • الطباعة/PDF
   ========================================================= */

(function(){
'use strict';

const $  = (s, r) => (r || document).querySelector(s);
const $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
const ar = n => MTLPages.arNum(n);

const state = {
  doc: null,
  pages: [],
  current: 1,
  zoom: 1,
  pendingPrint: false,
  saveTimer: null,
  pending: {}          /* تعديلات لم تُحفظ بعد */
};

/* ---------------------------------------------------------
   1) الواجهة الرئيسية: بطاقات الملفات
   --------------------------------------------------------- */
function renderCards(){
  const host = $('#cards');
  host.innerHTML = MTL_DOCS.map(doc => `
    <article class="card" data-id="${doc.id}"
      data-search="${MTLPages.esc((doc.title + ' ' + doc.tag + ' ' + doc.desc + ' ' + (doc.chips||[]).join(' ')).toLowerCase())}">
      <div class="card-top">
        <div class="card-head">
          <span class="card-ico"><svg viewBox="0 0 24 24">${MTL_ICONS[doc.icon] || ''}</svg></span>
          <div>
            <h3 class="card-title">${MTLPages.esc(doc.title)}</h3>
            <span class="card-tag">${MTLPages.esc(doc.tag)}</span>
          </div>
        </div>
      </div>
      <div class="card-body">
        <p class="card-desc">${MTLPages.esc(doc.desc)}</p>
        <div class="card-meta">
          <span class="chip">${ar(doc.pages.length)} صفحة A4</span>
          ${(doc.chips||[]).map(c=>`<span class="chip">${MTLPages.esc(c)}</span>`).join('')}
        </div>
      </div>
      <div class="card-foot">
        <button class="btn btn-primary" data-open="${doc.id}" type="button">فتح الملف</button>
        <button class="btn btn-soft" data-print="${doc.id}" type="button">طباعة / PDF</button>
      </div>
    </article>`).join('');
  $('#files-count').textContent = MTL_DOCS.length + ' ملفًا • ' +
    MTL_DOCS.reduce((s,d)=>s + d.pages.length, 0) + ' صفحة';
}

function filterCards(){
  const q = $('#search').value.trim().toLowerCase();
  let visible = 0;
  $$('#cards .card').forEach(card=>{
    const ok = !q || (card.dataset.search || '').indexOf(q) !== -1;
    card.style.display = ok ? '' : 'none';
    if(ok) visible++;
  });
  $('#empty').hidden = visible > 0;
}

/* ---------------------------------------------------------
   2) فتح ملف وعرض صفحاته
   --------------------------------------------------------- */
function openDoc(id, opts){
  const doc = MTL_DOCS.find(d => d.id === id);
  if(!doc){ goHome(); return; }

  state.doc = doc;
  state.pages = doc.pages;
  state.current = 1;
  state.zoom = 1;
  state.pendingPrint = !!(opts && opts.print);

  $('#view-home').hidden = true;
  $('#view-doc').hidden  = false;
  document.title = doc.title + ' — مكتبتي التعليمية';

  $('#vt-icon').innerHTML = `<svg viewBox="0 0 24 24">${MTL_ICONS[doc.icon] || ''}</svg>`;
  $('#vt-name').textContent = doc.title;
  $('#vt-meta').textContent = doc.pages.length + ' صفحة • ' + doc.tag;

  /* بناء الصفحات */
  const html = MTLPages.render(doc);
  $('#pages').innerHTML = html.map(h => `<div class="page-wrap">${h}</div>`).join('');

  applyBindings();
  applyStoredContent();
  renderThumbs(doc);
  fitPages();
  setCurrent(1, true);

  $('#pages').scrollTop = 0;

  if(state.pendingPrint){
    state.pendingPrint = false;
    setTimeout(()=>{ printDoc(); }, 350);
  }
}

function goHome(){
  state.doc = null;
  $('#view-doc').hidden = true;
  $('#view-home').hidden = false;
  document.title = 'مكتبتي التعليمية للمعلمة الليبية';
  if(location.hash) history.replaceState(null, '', location.pathname + location.search);
  window.scrollTo(0, 0);
}

function renderThumbs(doc){
  $('#thumbs').innerHTML =
    '<h4>صفحات الملف</h4>' +
    doc.pages.map((spec, i)=>{
      let label = MTLPages.thumbLabel(spec, i);
      const stored = MTLStore.getContent()[doc.id + '#' + i + '#title'];
      if(stored && spec.type !== 'cover') label = stored;
      return `<button class="thumb${i === 0 ? ' active' : ''}" data-goto="${i + 1}" type="button">
        <span class="thumb-n">${ar(i + 1)}</span>
        <span class="thumb-t">${MTLPages.esc(label)}</span>
      </button>`;
    }).join('');
}

/* ---------------------------------------------------------
   3) الروابط (Hash) والبحث والبطاقات
   --------------------------------------------------------- */
function routeFromHash(){
  const m = (location.hash || '').match(/^#\/doc\/([\w-]+)$/);
  if(m) openDoc(m[1]);
  else if(!$('#view-doc').hidden) goHome();
}

/* ---------------------------------------------------------
   4) تطبيق الإعدادات والبيانات المحفوظة
   --------------------------------------------------------- */
function applyBindings(){
  const s = MTLStore.getSettings();
  $$('[data-bind]').forEach(el=>{
    const k = el.getAttribute('data-bind');
    if(Object.prototype.hasOwnProperty.call(s, k)){
      el.textContent = s[k];
      /* جعل الحقل قابلًا للكتابة مباشرة بالنقر عليه */
      el.setAttribute('contenteditable', 'true');
      el.setAttribute('spellcheck', 'false');
      el.classList.add('bind-edit');
    }
  });
}

function applyStoredContent(){
  const all = MTLStore.getContent();
  $$('[data-k]').forEach(el=>{
    const v = all[el.getAttribute('data-k')];
    if(typeof v === 'string') el.innerText = v;
  });
}

function flushPending(){
  const keys = Object.keys(state.pending);
  if(!keys.length) return;
  const map = {};
  keys.forEach(k => { map[k] = state.pending[k]; });
  state.pending = {};
  const ok = MTLStore.setValues(map);
  $('#save-state').textContent = ok
    ? 'تم حفظ التعديلات في هذا المتصفح ✓'
    : 'تعذّر الحفظ (مساحة التخزين ممتلئة)';
}

function onEdit(e){
  const el = e.target.closest ? e.target.closest('[data-bind], [data-k]') : null;
  if(!el) return;

  /* كتابة داخل حقل عام (اسم/مدرسة/سنة…) → حفظ في الإعدادات + مزامنة كل الصفحات */
  const bk = el.getAttribute('data-bind');
  if(bk){
    const s = MTLStore.getSettings();
    s[bk] = el.innerText.trim();
    MTLStore.saveSettings(s);
    $$('[data-bind="' + bk + '"]').forEach(o=>{
      if(o !== el) o.innerText = s[bk];
    });
    const inp = document.querySelector('[data-set="' + bk + '"]');
    if(inp) inp.value = s[bk];
    $('#save-state').textContent = 'تم حفظ التعديل ✓';
    return;
  }

  state.pending[el.getAttribute('data-k')] = el.innerText;
  $('#save-state').textContent = 'جارٍ الحفظ…';
  clearTimeout(state.saveTimer);
  state.saveTimer = setTimeout(flushPending, 450);
}

/* ---------------------------------------------------------
   5) التكبير والقياس التلقائي
   --------------------------------------------------------- */
const PAGE_W_PX = 210 * 96 / 25.4;   /* 210mm */

function fitPages(){
  const host = $('#pages');
  if(!host) return;
  const avail = host.clientWidth - 8;
  const base = Math.min(1, avail / PAGE_W_PX);
  const scale = Math.max(0.18, Math.min(2, base * state.zoom));
  host.style.setProperty('--scale', scale);
}

/* ---------------------------------------------------------
   6) التنقل بين الصفحات
   --------------------------------------------------------- */
function wraps(){ return $$('#pages .page-wrap'); }

function setCurrent(n, scroll){
  const list = wraps();
  if(!list.length) return;
  n = Math.max(1, Math.min(list.length, n));
  state.current = n;
  list.forEach((w, i)=>{
    w.classList.toggle('is-current', i === n - 1);
    if(i === n - 1 && scroll){
      $('#pages').scrollTop = w.offsetTop - 8;
    }
  });
  $('#page-ind').textContent = ar(n) + ' / ' + ar(list.length);
  $$('#thumbs .thumb').forEach(t=>{
    t.classList.toggle('active', Number(t.dataset.goto) === n);
  });
}

function detectCurrent(){
  const host = $('#pages');
  const list = wraps();
  if(!list.length) return;
  const top = host.scrollTop + 60;
  let n = 1;
  for(let i = 0; i < list.length; i++){
    if(list[i].offsetTop <= top) n = i + 1; else break;
  }
  if(n !== state.current) setCurrent(n, false);
}

function gotoPage(n){
  const list = wraps();
  n = Math.max(1, Math.min(list.length, n));
  $('#pages').scrollTop = list[n - 1].offsetTop - 8;
  setCurrent(n, false);
}

/* ---------------------------------------------------------
   7) الطباعة / تصدير PDF
   --------------------------------------------------------- */
function printDoc(){
  if(!state.doc) return;
  flushPending();
  const s = MTLStore.getSettings();
  document.body.classList.remove('print-current');
  const old = document.title;
  document.title = state.doc.title + ' — ' + s.teacher;
  window.print();
  setTimeout(()=>{ document.title = old; }, 800);
}

function printCurrentPage(){
  if(!state.doc) return;
  flushPending();
  const s = MTLStore.getSettings();
  document.body.classList.add('print-current');
  const old = document.title;
  document.title = state.doc.title + ' — صفحة ' + ar(state.current);
  window.print();
  setTimeout(()=>{ document.title = old; }, 800);
}

window.addEventListener('afterprint', ()=>{
  document.body.classList.remove('print-current');
});

/* ---------------------------------------------------------
   8) النوافذ (الإعدادات / الشرح)
   --------------------------------------------------------- */
function openModal(sel){
  const m = $(sel);
  m.hidden = false;
  const first = m.querySelector('input, button');
  if(first) first.focus();
}
function closeModal(sel){ $(sel).hidden = true; }

function fillSettingsForm(){
  const s = MTLStore.getSettings();
  $$('[data-set]').forEach(i => { i.value = s[i.getAttribute('data-set')] || ''; });
  const u = MTLStore.usage();
  $('#storage-info').textContent =
    'الحقول المحفوظة: ' + u.fields + ' • الحجم التقريبي: ' + u.kb + ' KB • ' +
    'الحفظ يتم في هذا المتصفح فقط (localStorage).';
}

function saveSettingsFromForm(){
  const s = MTLStore.getSettings();
  $$('[data-set]').forEach(i => { s[i.getAttribute('data-set')] = i.value.trim(); });
  MTLStore.saveSettings(s);
  applyBindings();
  fillSettingsForm();
  toast('تم حفظ الإعدادات وتطبيقها على كل الصفحات ✓');
}

function downloadJSON(){
  flushPending();
  const data = MTLStore.exportPayload();
  const blob = new Blob([JSON.stringify(data, null, 2)], {type:'application/json;charset=utf-8'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  const d = new Date().toISOString().slice(0,10);
  a.href = url;
  a.download = 'مكتبتي-التعليمية-نسخة-احتياطية-' + d + '.json';
  document.body.appendChild(a);
  a.click();
  a.remove();
  setTimeout(()=>URL.revokeObjectURL(url), 2000);
  toast('تم تنزيل النسخة الاحتياطية ✓');
}

function importJSON(file){
  const fr = new FileReader();
  fr.onload = () => {
    try{
      MTLStore.importPayload(JSON.parse(fr.result));
      toast('تم استيراد البيانات بنجاح ✓');
      setTimeout(()=>location.reload(), 700);
    }catch(err){
      toast('تعذّر الاستيراد: ملف غير صالح');
    }
  };
  fr.readAsText(file, 'utf-8');
}

let toastTimer = null;
function toast(msg){
  const t = $('#toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(()=>t.classList.remove('show'), 2600);
}

/* ---------------------------------------------------------
   9) ربط الأحداث
   --------------------------------------------------------- */
function bindEvents(){
  /* بحث */
  $('#search').addEventListener('input', filterCards);

  /* بطاقات */
  $('#cards').addEventListener('click', e=>{
    const open = e.target.closest('[data-open]');
    if(open){ location.hash = '#/doc/' + open.dataset.open; return; }
    const pr = e.target.closest('[data-print]');
    if(pr){ state.pendingPrint = true; location.hash = '#/doc/' + pr.dataset.print; }
  });

  /* الشريط العلوي للعارض */
  $('#btn-back').addEventListener('click', goHome);
  $('#btn-next').addEventListener('click', ()=>gotoPage(state.current + 1));
  $('#btn-prev').addEventListener('click', ()=>gotoPage(state.current - 1));
  $('#btn-zoom-in').addEventListener('click', ()=>{ state.zoom = Math.min(2, state.zoom + 0.15); fitPages(); });
  $('#btn-zoom-out').addEventListener('click', ()=>{ state.zoom = Math.max(0.4, state.zoom - 0.15); fitPages(); });
  $('#btn-print-doc').addEventListener('click', printDoc);
  $('#btn-print-page').addEventListener('click', printCurrentPage);

  /* الفهرس الجانبي */
  $('#thumbs').addEventListener('click', e=>{
    const b = e.target.closest('[data-goto]');
    if(b) gotoPage(Number(b.dataset.goto));
  });

  /* التمرير داخل الصفحات */
  let raf = null;
  $('#pages').addEventListener('scroll', ()=>{
    if(raf) return;
    raf = requestAnimationFrame(()=>{ raf = null; detectCurrent(); });
  }, {passive:true});

  window.addEventListener('resize', fitPages);

  /* التحرير */
  document.addEventListener('input', onEdit);
  document.addEventListener('paste', e=>{
    const el = e.target.closest ? e.target.closest('[data-k]') : null;
    if(!el) return;
    e.preventDefault();
    const txt = (e.clipboardData || window.clipboardData).getData('text/plain');
    document.execCommand('insertText', false, txt);
  });

  /* النوافذ */
  $('#btn-settings').addEventListener('click', ()=>{ fillSettingsForm(); openModal('#modal-settings'); });
  $('#btn-help').addEventListener('click', ()=>openModal('#modal-help'));
  $('#btn-backup').addEventListener('click', ()=>{ fillSettingsForm(); openModal('#modal-settings'); });
  $$('.modal').forEach(m=>{
    m.addEventListener('click', e=>{
      if(e.target === m || e.target.closest('[data-close]')) m.hidden = true;
    });
  });
  $('#btn-save-settings').addEventListener('click', saveSettingsFromForm);
  $('#btn-reset-settings').addEventListener('click', ()=>{
    MTLStore.saveSettings(Object.assign({}, MTL_DEFAULT_SETTINGS));
    applyBindings(); fillSettingsForm();
    toast('تمت استعادة الإعدادات الافتراضية');
  });
  $('#btn-export-json').addEventListener('click', downloadJSON);
  $('#import-file').addEventListener('change', e=>{
    if(e.target.files && e.target.files[0]) importJSON(e.target.files[0]);
    e.target.value = '';
  });
  $('#btn-reset-content').addEventListener('click', ()=>{
    if(confirm('سيتم مسح كل التعديلات المحفوظة واستعادة النماذج الأصلية. هل أنتِ متأكدة؟')){
      MTLStore.clearAll();
      location.reload();
    }
  });

  /* لوحة المفاتيح */
  document.addEventListener('keydown', e=>{
    if(!state.doc) return;
    const t = e.target;
    if(t && (t.isContentEditable || t.tagName === 'INPUT' || t.tagName === 'TEXTAREA')) return;
    if(e.key === 'ArrowLeft' || e.key === 'PageDown'){ e.preventDefault(); gotoPage(state.current + 1); }
    if(e.key === 'ArrowRight' || e.key === 'PageUp'){ e.preventDefault(); gotoPage(state.current - 1); }
    if(e.key === 'Escape'){ goHome(); }
  });

  window.addEventListener('hashchange', routeFromHash);
  window.addEventListener('beforeprint', flushPending);
}

/* ---------------------------------------------------------
   10) الإقلاع
   --------------------------------------------------------- */
function init(){
  renderCards();
  bindEvents();
  applyBindings();          /* لواجهة رئيسية (اسم المعلمة في التذييل لاحقًا) */
  const u = MTLStore.usage();
  $('#save-state').textContent = u.fields
    ? 'تم استرجاع ' + u.fields + ' حقلًا محفوظًا ✓'
    : 'تُحفظ التعديلات تلقائيًا في هذا المتصفح';
  routeFromHash();
}

if(document.readyState === 'loading'){
  document.addEventListener('DOMContentLoaded', init);
}else{
  init();
}

})();
