/* =========================================================
   مولّد صفحات A4 — يحوّل مواصفات الصفحات في data.js إلى HTML
   مفاتيح التحرير: docId#pageIndex#localKey (ثابتة لضمان الحفظ)
   ========================================================= */

const MTLPages = (function(){

  /* ---------- أدوات مساعدة ---------- */
  function esc(s){
    return String(s == null ? '' : s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function arNum(n){
    return String(n).replace(/[0-9]/g, d => '٠١٢٣٤٥٦٧٨٩'[d]);
  }

  /* زخرفة زهرة للزوايا */
  const CORNER = `
  <svg viewBox="0 0 100 100" aria-hidden="true">
    <g fill="none" stroke="#C89B9B" stroke-width="1.6" stroke-linecap="round">
      <path d="M6 94C26 82 40 62 44 36"/>
      <path d="M44 66c-14-4-22-16-24-32 14 3 24 14 24 32Z" fill="#EFDFCD"/>
      <path d="M44 52c12-10 28-13 44-10-9 13-25 18-44 10Z" fill="#F4DDDD"/>
      <path d="M44 36c-10-8-14-22-11-36 11 8 15 22 11 36Z" fill="#EFDFCD"/>
      <path d="M44 36c14-3 26 2 34 12-13 6-26 3-34-12Z" fill="#F4DDDD"/>
      <circle cx="44" cy="36" r="4" fill="#C89B9B" stroke="none"/>
      <path d="M62 88c6-8 8-18 6-28" stroke="#A08672"/>
    </g>
  </svg>`;

  /* رأس الصفحة */
  function head(doc){
    return `<header class="page-head">
      <span data-bind="school"></span>
      <span class="ph-title">${esc(doc.title)}</span>
      <span data-bind="year"></span>
    </header>`;
  }

  /* تذييل الصفحة */
  function foot(pn){
    return `<footer class="page-foot">
      <span>إعداد: <b data-bind="teacher"></b> &nbsp;•&nbsp; <span data-bind="grade"></span> &nbsp;•&nbsp; <span data-bind="subject"></span></span>
      <span class="page-num">${arNum(pn)}</span>
    </footer>`;
  }

  /* عنوان صفحة داخلية */
  function title(spec, key){
    if(!spec.title) return '';
    return `<h2 class="pg-title"><span class="pt-text" data-k="${key}">${esc(spec.title)}</span></h2>`;
  }

  /* خلية جدول */
  function cell(v, key, center, numCol){
    let cls = '';
    if(numCol)      cls = ' class="num"';
    else if(center) cls = ' class="c"';
    if(v && typeof v === 'object'){
      if(v.bind)   return `<td${cls}><span data-bind="${v.bind}"></span></td>`;
      if(v.fixed)  return `<td${cls}><span>${esc(v.text)}</span></td>`;
    }
    /* الخلية نفسها هي الحقل القابل للكتابة (كامل مساحتها قابلة للنقر) */
    return `<td${cls} data-k="${key}">${esc(v)}</td>`;
  }

  /* ---------- الغلاف ---------- */
  function cover(spec, ctx){
    return `
    <article class="page cover">
      <div class="cov-frame"></div>
      <div class="corner c1">${CORNER}</div>
      <div class="corner c2">${CORNER}</div>
      <div class="corner c3">${CORNER}</div>
      <div class="corner c4">${CORNER}</div>

      <div class="cov-top">
        <div class="cov-org" data-bind="school"></div>
        <div class="cov-org" data-bind="area"></div>
      </div>

      <div class="cov-mid">
        <div class="cov-sub" data-k="${ctx.key()}">${esc(spec.kicker || '')}</div>
        <h1 class="cov-title" data-k="${ctx.key()}">${esc(spec.title)}</h1>
        <p class="cov-sub" data-k="${ctx.key()}">${esc(spec.sub || '')}</p>
      </div>

      <div class="cov-fields">
        <div class="fields">
          <div class="field"><span class="lbl">اسم المعلمة</span><span class="val" data-bind="teacher"></span></div>
          <div class="field"><span class="lbl">المدرسة</span><span class="val" data-bind="school"></span></div>
          <div class="field"><span class="lbl">المادة</span><span class="val" data-bind="subject"></span></div>
          <div class="field"><span class="lbl">الصف / الشعبة</span><span class="val" data-bind="grade"></span></div>
          <div class="field"><span class="lbl">السنة الدراسية</span><span class="val" data-bind="year"></span></div>
          <div class="field"><span class="lbl">الإدارة التعليمية</span><span class="val" data-bind="area"></span></div>
        </div>
      </div>

      <div class="cov-bottom">
        <div class="cov-note" data-k="${ctx.key()}">${esc(spec.note || '')}</div>
        <div class="cov-year" data-bind="year"></div>
      </div>
    </article>`;
  }

  /* ---------- الفهرس ---------- */
  function index(spec, ctx){
    const toc = buildTOC(ctx.doc);
    let i = 0;
    const items = toc.map(e=>{
      if(e.group){
        i++;
        return `<li class="grp"><span class="i-n">${arNum(i)}.</span>
          <span class="i-t" data-k="${ctx.doc.id}#${e.pageIndex}#title">${esc(e.title)}</span>
          <span class="i-d"></span><span class="i-p">${arNum(e.page)}</span></li>`;
      }
      return `<li><span class="i-n">—</span>
        <span class="i-t" data-k="${ctx.doc.id}#${e.pageIndex}#title">${esc(e.title)}</span>
        <span class="i-d"></span><span class="i-p">${arNum(e.page)}</span></li>`;
    }).join('');

    return `
    <article class="page">
      ${head(ctx.doc)}
      <div class="page-body">
        ${title(spec, `${ctx.doc.id}#${ctx.idx}#title`)}
        <p class="pg-sub" data-k="${ctx.key()}">${esc(spec.sub || 'محتويات الملف مرتبة حسب تسلسل الصفحات.')}</p>
        <ul class="idx">${items}</ul>
        <div class="tbl-note"><b>ملاحظة:</b> أرقام الصفحات تتحدث تلقائيًا حسب ترتيب الصفحات داخل الملف.</div>
      </div>
      ${foot(ctx.pn)}
    </article>`;
  }

  /* ---------- الفاصل ---------- */
  function divider(spec, ctx){
    return `
    <article class="page divider">
      <div class="d-flower" aria-hidden="true">${CORNER}</div>
      <h2 class="d-title" data-k="${ctx.doc.id}#${ctx.idx}#title">${esc(spec.title)}</h2>
      <div class="d-rule"></div>
      <ul class="d-list">${(spec.items||[]).map((it,n)=>
        `<li><span data-k="${ctx.key()}">${esc(it)}</span></li>`).join('')}</ul>
      <div class="d-tag">${esc(ctx.doc.title)}</div>
      <div class="page-foot" style="width:100%">
        <span>إعداد: <b data-bind="teacher"></b> &nbsp;•&nbsp; <span data-bind="year"></span></span>
        <span class="page-num">${arNum(ctx.pn)}</span>
      </div>
    </article>`;
  }

  /* ---------- جدول ---------- */
  function table(spec, ctx){
    const headers = spec.headers || [];
    const widths  = spec.widths  || [];
    const rows    = (spec.rows || []).slice();
    const pad     = spec.padRows || 0;
    for(let n=0;n<pad;n++) rows.push(headers.map(()=>''));

    const colgroup = widths.map(w=>`<col style="width:${Number(w)||0}%">`).join('');
    const centerCols = headers.map(h => String(h).length <= 12);
    const autoNum = String(headers[0] || '').trim() === 'م';

    const body = rows.map((r,ri)=>{
      const cells = r.slice();
      /* ترقيم تلقائي للعمود الأول إذا كان فارغًا */
      if(autoNum){
        const c0 = cells[0];
        const empty = (c0 === undefined || c0 === null || c0 === '' ||
          (typeof c0 === 'object' && c0.fixed && !String(c0.text).trim()));
        if(empty) cells[0] = { fixed:true, text:String(ri + 1) };
      }
      return `<tr>${headers.map((h,ci)=>{
        const isNum = ci === 0;
        const v = cells[ci];
        const key = `${ctx.doc.id}#${ctx.idx}#r${ri}c${ci}`;
        if(isNum && (v === undefined || v === '')) return `<td class="num"><span></span></td>`;
        if(isNum) return cell(v, key, true, true);
        return cell(v, key, centerCols[ci]);
      }).join('')}</tr>`;
    }).join('');

    return `
    <article class="page">
      ${head(ctx.doc)}
      <div class="page-body">
        ${title(spec, `${ctx.doc.id}#${ctx.idx}#title`)}
        ${spec.sub ? `<p class="pg-sub" data-k="${ctx.key()}">${esc(spec.sub)}</p>` : ''}
        <table class="tbl rows-${spec.rowH || 'mid'}">
          <colgroup>${colgroup}</colgroup>
          <thead><tr>${headers.map((h,ci)=>`<th><span data-k="${ctx.doc.id}#${ctx.idx}#h${ci}">${esc(h)}</span></th>`).join('')}</tr></thead>
          <tbody>${body}</tbody>
        </table>
        ${spec.note ? `<div class="tbl-note">${spec.note}</div>` : ''}
      </div>
      ${foot(ctx.pn)}
    </article>`;
  }

  /* ---------- بطاقة / استمارة ---------- */
  function form(spec, ctx){
    const fields = (spec.fields||[]).map((f,n)=>{
      const key = `${ctx.doc.id}#${ctx.idx}#f${n}`;
      const val = f.bind
        ? `<span class="val" data-bind="${f.bind}"></span>`
        : `<span class="val" data-k="${key}">${esc(f.v || '')}</span>`;
      return `<div class="field"><span class="lbl">${esc(f.l)}</span>${val}</div>`;
    }).join('');

    const blocks = (spec.blocks||[]).map((b,n)=>{
      const hk = `${ctx.doc.id}#${ctx.idx}#b${n}h`;
      const bk = `${ctx.doc.id}#${ctx.idx}#b${n}`;
      return `<div class="block ${b.h || ''}">
        <div class="block-h" data-k="${hk}">${esc(b.l)}</div>
        <div class="block-b" data-k="${bk}">${esc(b.t || '')}</div>
      </div>`;
    }).join('');

    return `
    <article class="page">
      ${head(ctx.doc)}
      <div class="page-body">
        ${title(spec, `${ctx.doc.id}#${ctx.idx}#title`)}
        <div class="fields ${Number(spec.cols) === 1 ? 'one-col' : ''}">${fields}</div>
        <div style="height:4mm"></div>
        ${blocks}
      </div>
      ${foot(ctx.pn)}
    </article>`;
  }

  /* ---------- صفحة نصوص وقوائم ---------- */
  function text(spec, ctx){
    const paras = (spec.paras||[]).map((p,n)=>
      `<p class="p-para" data-k="${ctx.key()}">${esc(p)}</p>`).join('');
    const bullets = (spec.bullets||[]).length
      ? `<ul class="p-list">${spec.bullets.map(b=>`<li><span data-k="${ctx.key()}">${esc(b)}</span></li>`).join('')}</ul>` : '';
    const check = (spec.check||[]).length
      ? `<ul class="check">${spec.check.map(c=>`<li><span data-k="${ctx.key()}">${esc(c)}</span></li>`).join('')}</ul>` : '';

    return `
    <article class="page">
      ${head(ctx.doc)}
      <div class="page-body">
        ${title(spec, `${ctx.doc.id}#${ctx.idx}#title`)}
        ${paras}
        ${bullets}
        ${check}
        ${spec.note ? `<div class="tbl-note"><b>ملاحظة:</b> ${spec.note}</div>` : ''}
      </div>
      ${foot(ctx.pn)}
    </article>`;
  }

  /* ---------- الشهادة ---------- */
  function certificate(spec, ctx){
    const signs = (spec.signs||[]).map((s,n)=>{
      const key = `${ctx.doc.id}#${ctx.idx}#s${n}`;
      /* سطر التوقيع نفسه هو الحقل (كامل العرض قابل للكتابة) */
      const lnAttr = s.bind ? '' : ` data-k="${key}"`;
      const v = s.bind ? `<span data-bind="${s.bind}"></span>` : esc(s.v || '');
      return `<div class="sig"><div class="ln"${lnAttr}>${v}</div><div data-k="${ctx.key()}">${esc(s.l)}</div></div>`;
    }).join('');

    return `
    <article class="page cert">
      <div class="cert-frame"></div>
      <div class="corner c1">${CORNER}</div>
      <div class="corner c2">${CORNER}</div>
      <div class="corner c3">${CORNER}</div>
      <div class="corner c4">${CORNER}</div>

      <div class="cert-org"><span data-bind="school"></span> — <span data-bind="area"></span></div>
      <div class="cert-kicker" data-k="${ctx.key()}">${esc(spec.kicker || '')}</div>
      <div class="cert-title" data-k="${ctx.key()}">${esc(spec.title || '')}</div>
      <div class="cert-orn">❁ ❁ ❁</div>

      <p class="cert-body">
        <span data-k="${ctx.key()}">${esc(spec.body1 || '')}</span><br>
        <span data-bind="school"></span><br>
        <span data-k="${ctx.key()}">${esc(spec.body2 || '')}</span><br>
        <span class="cert-name" data-k="${ctx.key()}">${esc(spec.name || '')}</span><br>
        <span data-k="${ctx.key()}">${esc(spec.body3 || '')}</span><br>
        <span data-k="${ctx.key()}">${esc(spec.body4 || '')}</span> <span data-bind="year"></span>
      </p>

      <div class="cert-sign">${signs}</div>
    </article>`;
  }

  /* ---------- بناء الفهرس من المواصفات ---------- */
  function buildTOC(doc){
    const out = [];
    doc.pages.forEach((p,i)=>{
      const pn = i + 1;
      if(p.type === 'divider'){
        out.push({group:true, title:p.title, page:pn, pageIndex:i});
      } else if(['table','form','text','certificate'].includes(p.type) && p.title){
        out.push({group:false, title:p.title, page:pn, pageIndex:i});
      }
    });
    return out;
  }

  /* ---------- نقطة الدخول ---------- */
  const RENDERERS = { cover, index, divider, table, form, text, certificate };

  function render(doc){
    const out = [];
    doc.pages.forEach((spec, idx)=>{
      const ctx = {
        doc, idx, pn: idx + 1, n: 0,
        key(){ return `${doc.id}#${idx}#${this.n++}`; }
      };
      const fn = RENDERERS[spec.type];
      if(!fn){ console.warn('نوع صفحة غير معروف:', spec.type); return; }
      out.push(fn(spec, ctx));
    });
    return out;
  }

  /* عنوان مختصر للشريط الجانبي */
  function thumbLabel(spec, idx){
    if(spec.type === 'cover')    return 'الغلاف';
    if(spec.type === 'index')    return spec.title || 'الفهرس';
    if(spec.type === 'divider')  return 'فاصل: ' + (spec.title || '');
    if(spec.type === 'certificate') return 'شهادة: ' + (spec.title || '');
    return spec.title || 'صفحة ' + arNum(idx + 1);
  }

  return { render, buildTOC, thumbLabel, esc, arNum };
})();
