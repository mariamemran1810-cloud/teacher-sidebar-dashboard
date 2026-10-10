/* =====================================================
   games.js — محرك الألعاب الست
   quiz | arcade | tf | input | match | order
   كل لعبة تستقبل: { mode, gradeId, unitId, pool, title, sub,
                     done(summary), quit() }
   ===================================================== */
'use strict';

(function () {
  const UI = () => (window.UI || {});
  const metaOf = id => (window.GAME_META || []).find(m => m.id === id) || {};
  const esc = s => String(s == null ? '' : s).replace(/[&<>"]/g, c =>
    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

  function h(tag, cls, html) {
    const e = document.createElement(tag);
    if (cls) e.className = cls;
    if (html != null) e.innerHTML = html;
    return e;
  }

  /* ---------------- هيكل شاشة اللعب ---------------- */
  function shell(cfg) {
    const meta = metaOf(cfg.mode);
    const root = h('section', 'play');
    root.innerHTML =
      '<div class="play-top">' +
        '<button class="back" type="button" title="الخروج من اللعبة">↩</button>' +
        '<div class="play-title"><b></b><span></span></div>' +
        '<div class="hud">' +
          '<span class="pill" data-h="score"><i>⭐</i><b>0</b></span>' +
          '<span class="pill" data-h="streak"><i>🔥</i><b>0</b></span>' +
          '<span class="pill lives" data-h="lives" hidden><i>❤️</i><b></b></span>' +
          '<span class="pill" data-h="time" hidden><i>⏱️</i><b></b></span>' +
        '</div>' +
      '</div>' +
      '<div class="timer" data-el="timer" hidden><i></i></div>' +
      '<div class="q-progress" data-el="prog"></div>' +
      '<div data-el="stage"></div>';

    root.querySelector('.play-title b').textContent = cfg.title || meta.name || '';
    root.querySelector('.play-title span').textContent = cfg.sub || '';
    root.querySelector('.back').addEventListener('click', () => cfg.quit && cfg.quit());
    root.tabIndex = -1;
    setTimeout(() => { try { root.focus(); } catch (e) {} }, 60);

    const S = {
      root,
      stage: root.querySelector('[data-el="stage"]'),
      prog: root.querySelector('[data-el="prog"]'),
      timer: root.querySelector('[data-el="timer"]'),
      score: 0,
      streak: 0,
      bestStreak: 0,
      correct: 0,
      total: 0,
      mistakes: 0,
      wrong: [],
      _timer: null,

      hud(name, val) {
        const p = root.querySelector('[data-h="' + name + '"]');
        if (!p) return;
        p.hidden = false;
        const b = p.querySelector('b');
        if (name === 'lives') b.textContent = '❤️'.repeat(Math.max(0, val)) + '🖤'.repeat(Math.max(0, 3 - val));
        else b.textContent = val;
        p.classList.remove('flash'); void p.offsetWidth; p.classList.add('flash');
      },
      hideHud(name) { const p = root.querySelector('[data-h="' + name + '"]'); if (p) p.hidden = true; },

      progress(states, cur) {
        S.prog.innerHTML = states.map((st, i) =>
          '<i class="' + (st ? st : '') + (i === cur ? (st ? '' : 'cur') : '') + '"></i>').join('');
      },

      /** إضافة نقاط مع طيران الرقم */
      add(pts, ev) {
        S.score += pts;
        S.hud('score', S.score);
        if (pts > 0 && ev && UI().fly) UI().fly(ev, '+' + pts);
      },
      setStreak(v) {
        S.streak = v;
        S.bestStreak = Math.max(S.bestStreak, v);
        S.hud('streak', v);
      },
      time(sec) { S.hud('time', sec); },

      finish(summary) {
        if (S.destroyed) return;
        S.stopTimer();
        const s = Object.assign({
          mode: cfg.mode, gradeId: cfg.gradeId, unitId: cfg.unitId,
          correct: S.correct, total: S.total, score: S.score,
          bestStreak: S.bestStreak, mistakes: S.mistakes, wrong: S.wrong
        }, summary || {});
        cfg.done && cfg.done(s);
      }
    };

    S.stopTimer = function () { if (S._timer) { clearInterval(S._timer); S._timer = null; } };
    S.destroyed = false;
    S.destroy = function () { S.destroyed = true; S.stopTimer(); };
    return S;
  }

  /** طابور يعيد الخلط عند النفاد (للأسئلة الكثيرة) */
  function queue(items) {
    let arr = R.shuffle(items), i = 0;
    return {
      get len() { return arr.length; },
      next() {
        if (i >= arr.length) { arr = R.shuffle(arr); i = 0; }
        return arr[i++];
      },
      all() { return arr; }
    };
  }

  const nextBtn = (label) =>
    '<button class="btn btn-primary btn-sm next-btn" type="button" data-next>' + (label || 'التالي ←') + '</button>';

  const explainHTML = (ok, title, body, label) =>
    '<div class="explain ' + (ok ? 'ok' : 'bad') + '">' +
      '<span class="em">' + (ok ? '🎉' : '💡') + '</span>' +
      '<span><b>' + esc(title) + '</b>' + (body ? esc(body) : '') + '</span>' +
      nextBtn(label) +
    '</div>';

  function bindNext(box, fn) {
    const b = box.querySelector('[data-next]');
    if (b) b.addEventListener('click', fn);
    return b;
  }

  const answerText = it => {
    if (!it) return '';
    if (it.t === 'mc') return it.o[it.c];
    if (it.t === 'tf') return it.c ? 'صح' : 'خطأ';
    if (it.t === 'num') return String(it.a);
    return '';
  };

  /* ================= اختبار سريع (quiz) ================= */
  function quiz(cfg) {
    const S = shell(cfg);
    const pool = queue(cfg.pool);
    const N = Math.min(metaOf(cfg.mode).len || 10, cfg.pool.length);
    const states = new Array(N).fill('');
    let i = 0, locked = false, tick = null, left = 0;

    S.total = N;

    function render() {
      locked = false;
      const it = pool.next();
      left = 20;
      S.hud('time', left);
      S.hideHud('lives');
      S.progress(states, i);

      const box = h('div', 'q-card');
      box.innerHTML = '<div class="q-label">سؤال ' + (i + 1) + ' من ' + N + '</div>' +
        '<div class="q-text' + (/[a-zA-Z0-9]/.test(it.q) && !/[\u0600-\u06FF]/.test(it.q) ? ' ltr' : '') + '">' + esc(it.q) + '</div>';
      const opts = h('div', 'opts');
      it.o.forEach((o, k) => {
        const b = h('button', 'opt', '<span class="key">' + (k + 1) + '</span>' + esc(o));
        b.type = 'button';
        b.addEventListener('click', ev => pick(k, b, it, ev));
        opts.appendChild(b);
      });
      box.appendChild(opts);
      const foot = h('div');
      box.appendChild(foot);

      S.stage.innerHTML = '';
      S.stage.appendChild(box);

      if (tick) clearInterval(tick);
      tick = setInterval(() => {
        left--;
        S.hud('time', Math.max(0, left));
        if (left <= 0) { clearInterval(tick); tick = null; if (!locked) pick(-1, null, it, null); }
      }, 1000);

      box._key = ev => {
        if (locked) {
          if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); next(); }
          return;
        }
        const k = '1234'.indexOf(ev.key);
        if (k > -1) { const b = opts.children[k]; if (b) b.click(); }
      };
      S._key = box._key;
    }

    function pick(k, btn, it, ev) {
      if (locked) return;
      locked = true;
      if (tick) { clearInterval(tick); tick = null; }
      S.hideHud('time');
      const ok = k === it.c;
      const btns = Array.from(S.stage.querySelectorAll('.opt'));
      btns.forEach((b, idx) => {
        b.classList.add('lock');
        if (idx === it.c) b.classList.add('correct');
        else if (idx === k) b.classList.add('wrong');
        else b.classList.add('dim');
      });

      let pts = 0;
      if (ok) {
        S.correct++;
        S.setStreak(S.streak + 1);
        pts = 10 + Math.max(0, left) + Math.min(10, S.streak * 2);
        S.add(pts, ev);
        UI().sfx && UI().sfx('good');
        UI().buzz && UI().buzz(30);
        states[i] = 'done';
      } else {
        S.setStreak(0);
        S.mistakes++;
        S.wrong.push({ q: it.q, a: answerText(it), k: k > -1 ? it.o[k] : 'انتهى الوقت' });
        UI().sfx && UI().sfx('bad');
        states[i] = 'bad';
      }
      S.progress(states, i);

      const foot = S.stage.querySelector('.q-card > div:last-child');
      foot.innerHTML = explainHTML(ok, ok ? 'إجابة صحيحة! +' + pts : 'الإجابة الصحيحة: ' + answerText(it), it.e);
      bindNext(foot, next);
      S._nextBtn = foot.querySelector('[data-next]');
      if (S._nextBtn) S._nextBtn.focus();
    }

    function next() {
      i++;
      if (i >= N) {
        S.progress(states, -1);
        S.finish({});
        return;
      }
      render();
    }

    S.root.addEventListener('keydown', e => { if (S._key) S._key(e); });
    S.root.tabIndex = -1;
    render();
    setTimeout(() => S.root.focus(), 30);
    return S;
  }

  /* ================= سباق الوقت (arcade) ================= */
  function arcade(cfg) {
    const S = shell(cfg);
    const pool = queue(cfg.pool);
    const LIMIT = metaOf(cfg.mode).timed || 60;
    let left = LIMIT, lives = 3, locked = false, finished = false;

    S.total = 0;
    S.hud('lives', lives);
    S.time(left);
    S.timer.hidden = false;

    let tick = S._timer = setInterval(() => {
      left--;
      S.time(Math.max(0, left));
      S.timer.querySelector('i').style.width = Math.max(0, (left / LIMIT) * 100) + '%';
      if (left <= 10) S.timer.classList.add('low');
      if (left <= 0) end('انتهى الوقت!');
    }, 1000);

    function end(reason) {
      if (finished || S.destroyed) return;
      finished = true;
      clearInterval(tick);
      const rate = S.total ? Math.round((S.correct / S.total) * 100) : 0;
      S.finish({ reason, rate });
    }

    function render() {
      locked = false;
      const it = pool.next();
      const box = h('div', 'q-card');
      box.innerHTML = '<div class="q-label">⏱️ أسرع! بقي ' + left + ' ثانية</div>' +
        '<div class="q-text">' + esc(it.q) + '</div>';
      const opts = h('div', 'opts');
      it.o.forEach((o, k) => {
        const b = h('button', 'opt', esc(o));
        b.type = 'button';
        b.addEventListener('click', ev => pick(k, b, it, ev));
        opts.appendChild(b);
      });
      box.appendChild(opts);
      S.stage.innerHTML = '';
      S.stage.appendChild(box);
      S._key = ev => {
        const k = '1234'.indexOf(ev.key);
        if (k > -1 && !locked) { const b = opts.children[k]; if (b) b.click(); }
      };
    }

    function pick(k, btn, it, ev) {
      if (locked) return;
      locked = true;
      const ok = k === it.c;
      S.total++;
      const btns = Array.from(S.stage.querySelectorAll('.opt'));
      btns.forEach((b, idx) => {
        b.classList.add('lock');
        if (idx === it.c) b.classList.add('correct');
        else if (idx === k) b.classList.add('wrong');
      });

      if (ok) {
        S.correct++;
        S.setStreak(S.streak + 1);
        S.add(10 + Math.min(20, S.streak * 3), ev);
        UI().sfx && UI().sfx('good');
        UI().buzz && UI().buzz(25);
      } else {
        S.setStreak(0);
        S.mistakes++;
        S.wrong.push({ q: it.q, a: answerText(it), k: it.o[k] });
        lives--;
        S.hud('lives', lives);
        UI().sfx && UI().sfx('bad');
        UI().buzz && UI().buzz([40, 60, 40]);
        if (lives <= 0) { setTimeout(() => end('نفدت الأرواح!'), 550); return; }
      }
      setTimeout(() => { if (!S.destroyed) render(); }, 550);
    }

    S.root.addEventListener('keydown', e => { if (S._key) S._key(e); });
    render();
    return S;
  }

  /* ================= صح أم خطأ (tf) ================= */
  function tfGame(cfg) {
    const S = shell(cfg);
    const pool = queue(cfg.pool);
    const N = Math.min(metaOf(cfg.mode).len || 12, cfg.pool.length);
    const states = new Array(N).fill('');
    let i = 0, locked = false;

    S.total = N;
    S.hideHud('time');

    function render() {
      locked = false;
      const it = pool.next();
      S.progress(states, i);
      const box = h('div', 'q-card');
      box.innerHTML = '<div class="q-label">عبارة رقم ' + (i + 1) + ' من ' + N + '</div>' +
        '<div class="q-text">' + esc(it.q) + '</div>';
      const grid = h('div', 'tf-grid');
      const bt = h('button', 'tf-btn true', '✔ صح<small>صحيحة</small>'); bt.type = 'button';
      const bf = h('button', 'tf-btn false', '✘ خطأ<small>غير صحيحة</small>'); bf.type = 'button';
      bt.addEventListener('click', ev => pick(true, bt, bf, it, ev));
      bf.addEventListener('click', ev => pick(false, bf, bt, it, ev));
      grid.appendChild(bt); grid.appendChild(bf);
      box.appendChild(grid);
      const foot = h('div');
      box.appendChild(foot);
      S.stage.innerHTML = '';
      S.stage.appendChild(box);
      S._key = ev => {
        if (locked) { if (ev.key === 'Enter') { ev.preventDefault(); next(); } return; }
        if (ev.key === '1' || ev.key === 'ArrowRight') bt.click();
        if (ev.key === '2' || ev.key === 'ArrowLeft') bf.click();
      };
    }

    function pick(val, btn, other, it, ev) {
      if (locked) return;
      locked = true;
      const ok = val === it.c;
      [btn, other].forEach(b => b.classList.add('lock'));
      btn.classList.add(ok ? 'good' : 'bad');
      other.classList.add('dim');

      let pts = 0;
      if (ok) {
        S.correct++;
        S.setStreak(S.streak + 1);
        pts = 10 + Math.min(15, S.streak * 3);
        S.add(pts, ev);
        UI().sfx && UI().sfx('good');
        UI().buzz && UI().buzz(30);
        states[i] = 'done';
      } else {
        S.setStreak(0);
        S.mistakes++;
        S.wrong.push({ q: it.q, a: it.c ? 'صح' : 'خطأ', k: val ? 'صح' : 'خطأ' });
        UI().sfx && UI().sfx('bad');
        states[i] = 'bad';
      }
      S.progress(states, i);
      const foot = S.stage.querySelector('.q-card > div:last-child');
      foot.innerHTML = explainHTML(ok, ok ? 'أحسنت! +' + pts : 'الصواب: ' + (it.c ? 'صح' : 'خطأ'), it.e);
      bindNext(foot, next);
      S._nextBtn = foot.querySelector('[data-next]');
      if (S._nextBtn) S._nextBtn.focus();
    }

    function next() {
      i++;
      if (i >= N) { S.progress(states, -1); S.finish({}); return; }
      render();
    }

    S.root.addEventListener('keydown', e => { if (S._key) S._key(e); });
    render();
    return S;
  }

  /* ================= اكتب الإجابة (input) ================= */
  function inputGame(cfg) {
    const S = shell(cfg);
    const pool = queue(cfg.pool);
    const N = Math.min(metaOf(cfg.mode).len || 10, cfg.pool.length);
    const states = new Array(N).fill('');
    let i = 0, it = null, buf = '', tries = 0, locked = false;

    S.total = N;
    S.hideHud('time');

    const KEYS = [
      ['7', '7'], ['8', '8'], ['9', '9'], ['⌫', 'del'],
      ['4', '4'], ['5', '5'], ['6', '6'], ['−', '-'],
      ['1', '1'], ['2', '2'], ['3', '3'], ['/', '/'],
      ['0', '0'], ['.', '.'], ['مسح', 'clear'], ['✔ تأكيد', 'go']
    ];

    function render() {
      locked = false;
      it = pool.next();
      buf = ''; tries = 0;
      S.progress(states, i);
      const box = h('div', 'q-card');
      box.innerHTML = '<div class="q-label">اكتب الإجابة · سؤال ' + (i + 1) + ' من ' + N + '</div>' +
        '<div class="q-text">' + esc(it.q) + '</div>';
      const wrap = h('div', 'pad-wrap');
      const disp = h('div', 'pad-display empty', 'اكتب إجابتك هنا…');
      const pad = h('div', 'keypad');
      KEYS.forEach(([label, val]) => {
        const b = h('button', 'kp' + (val === 'go' ? ' go' : val === 'del' || val === 'clear' ? ' del' : ''), label);
        b.type = 'button';
        b.addEventListener('click', ev => press(val, ev));
        pad.appendChild(b);
      });
      wrap.appendChild(disp); wrap.appendChild(pad);
      box.appendChild(wrap);
      const foot = h('div');
      box.appendChild(foot);
      S.stage.innerHTML = '';
      S.stage.appendChild(box);
      S._disp = disp;
      S._key = ev => {
        if (locked) { if (ev.key === 'Enter') { ev.preventDefault(); next(); } return; }
        if (/^[0-9]$/.test(ev.key)) press(ev.key);
        else if (ev.key === '.' || ev.key === '/') press(ev.key);
        else if (ev.key === '-') press('-');
        else if (ev.key === 'Backspace') press('del');
        else if (ev.key === 'Enter') press('go');
        else if (ev.key === 'Escape') press('clear');
      };
    }

    function paint() {
      const d = S._disp;
      d.textContent = buf || 'اكتب إجابتك هنا…';
      d.classList.toggle('empty', !buf);
      d.classList.remove('ok', 'bad');
    }

    function press(val, ev) {
      if (locked) return;
      UI().sfx && UI().sfx('tap');
      if (val === 'del') { buf = buf.slice(0, -1); paint(); return; }
      if (val === 'clear') { buf = ''; paint(); return; }
      if (val === 'go') { submit(ev); return; }
      if (buf.length > 12) return;
      if (val === '-') { if (buf.includes('-')) return; buf = '-' + buf; }
      else if (val === '.') { if (buf.includes('.')) return; buf = (buf || '0') + '.'; }
      else if (val === '/') { if (!buf || buf.includes('/')) return; buf += '/'; }
      else buf += val;
      paint();
    }

    function submit(ev) {
      if (!buf) return;
      const ok = acceptAns(buf, it);
      const d = S._disp;
      d.classList.remove('ok', 'bad');
      d.classList.add(ok ? 'ok' : 'bad');
      const foot = S.stage.querySelector('.q-card > div:last-child');

      if (ok) {
        locked = true;
        S.correct++;
        S.setStreak(S.streak + 1);
        const pts = (tries === 0 ? 15 : 8) + Math.min(10, S.streak * 2);
        S.add(pts, ev);
        UI().sfx && UI().sfx('good');
        UI().buzz && UI().buzz(30);
        states[i] = 'done';
        S.progress(states, i);
        foot.innerHTML = explainHTML(true, 'صحيح! +' + pts, it.e);
        bindNext(foot, next);
        S._nextBtn = foot.querySelector('[data-next]');
        if (S._nextBtn) S._nextBtn.focus();
      } else {
        tries++;
        UI().sfx && UI().sfx('bad');
        UI().buzz && UI().buzz(60);
        if (tries === 1) {
          d.textContent = buf;
          foot.innerHTML = '<div class="explain bad"><span class="em">🤔</span><span><b>ليست صحيحة… حاول مرة أخرى</b>' +
            'لديك محاولة ثانية، ثم نعرض لك الحل.</span></div>';
          buf = '';
          setTimeout(paint, 700);
        } else {
          locked = true;
          S.setStreak(0);
          S.mistakes++;
          S.wrong.push({ q: it.q, a: String(it.a), k: buf });
          states[i] = 'bad';
          S.progress(states, i);
          foot.innerHTML = explainHTML(false, 'الإجابة الصحيحة: ' + it.a, it.e, 'التالي ←');
          bindNext(foot, next);
          S._nextBtn = foot.querySelector('[data-next]');
          if (S._nextBtn) S._nextBtn.focus();
        }
      }
    }

    function next() {
      i++;
      if (i >= N) { S.progress(states, -1); S.finish({}); return; }
      render();
    }

    S.root.addEventListener('keydown', e => { if (S._key) S._key(e); });
    render();
    return S;
  }

  /* ================= المطابقة (match) ================= */
  function matchGame(cfg) {
    const S = shell(cfg);
    const rounds = cfg.pool.slice();
    const N = Math.min(metaOf(cfg.mode).len || 3, rounds.length);
    const states = new Array(N).fill('');
    let r = 0, sel = null, done = 0, locked = false;

    S.total = N;
    S.hideHud('time');

    function render() {
      locked = false;
      sel = null; done = 0;
      const item = rounds[r];
      const left = item.p.map((p, idx) => ({ i: idx, t: p[0], pair: idx }));
      const right = item.p.map((p, idx) => ({ i: idx, t: p[1], pair: idx }));
      const L = R.shuffle(left), Rt = R.shuffle(right);

      S.progress(states, r);
      const box = h('div', 'q-card');
      box.innerHTML = '<div class="q-label">طابق كل عنصر بمقابله · جولة ' + (r + 1) + ' من ' + N + '</div>' +
        '<div class="q-hint">انقر عنصراً من العمود الأول ثم نظيره في العمود الثاني.</div>';

      const board = h('div', 'match-board');
      const c1 = h('div', 'match-col'), c2 = h('div', 'match-col');
      L.forEach(o => c1.appendChild(cell(o, 'L')));
      Rt.forEach(o => c2.appendChild(cell(o, 'R')));
      board.appendChild(c1); board.appendChild(c2);
      box.appendChild(board);
      const foot = h('div');
      box.appendChild(foot);
      S.stage.innerHTML = '';
      S.stage.appendChild(box);

      function cell(o, side) {
        const b = h('button', 'm-item', esc(o.t));
        b.type = 'button';
        b.dataset.pair = o.pair;
        b.dataset.side = side;
        b.addEventListener('click', ev => tap(b, ev));
        return b;
      }
    }

    function tap(b, ev) {
      if (locked || b.classList.contains('done')) return;
      UI().sfx && UI().sfx('tap');
      if (b.dataset.side === 'L') {
        S.stage.querySelectorAll('.m-item[data-side="L"]').forEach(x => x.classList.remove('sel'));
        b.classList.add('sel');
        sel = b;
        return;
      }
      if (!sel) {
        b.classList.add('bump');
        setTimeout(() => b.classList.remove('bump'), 320);
        return;
      }
      if (b.dataset.pair === sel.dataset.pair) {
        const l = sel;
        l.classList.remove('sel');
        l.classList.add('done');
        b.classList.add('done');
        sel = null;
        done++;
        S.setStreak(S.streak + 1);
        S.add(6 + Math.min(9, S.streak * 2), ev);
        UI().sfx && UI().sfx('good');
        UI().buzz && UI().buzz(25);
        if (done === 4) roundDone();
      } else {
        b.classList.add('err');
        sel.classList.add('err');
        S.setStreak(0);
        S.mistakes++;
        UI().sfx && UI().sfx('bad');
        UI().buzz && UI().buzz(50);
        const a = sel;
        setTimeout(() => {
          b.classList.remove('err');
          if (a) a.classList.remove('err', 'sel');
        }, 480);
        sel = null;
      }
    }

    function roundDone() {
      locked = true;
      S.correct++;
      states[r] = 'done';
      S.progress(states, r);
      const foot = S.stage.querySelector('.q-card > div:last-child');
      foot.innerHTML = explainHTML(true, 'اكتملت المطابقة! 🎯', 'ممتاز — الجولة التالية قريبة.');
      bindNext(foot, next);
      S._nextBtn = foot.querySelector('[data-next]');
      if (S._nextBtn) S._nextBtn.focus();
    }

    function next() {
      r++;
      if (r >= N) { S.progress(states, -1); S.finish({}); return; }
      render();
    }

    S.root.addEventListener('keydown', e => { if (e.key === 'Enter' && S._nextBtn && !locked) S._nextBtn.click(); });
    render();
    return S;
  }

  /* ================= ترتيب الأرقام (order) ================= */
  function orderGame(cfg) {
    const S = shell(cfg);
    const rounds = cfg.pool.slice();
    const N = Math.min(metaOf(cfg.mode).len || 4, rounds.length);
    const states = new Array(N).fill('');
    let r = 0, picked = [], locked = false;

    S.total = N;
    S.hideHud('time');

    const numv = v => {
      const n = Number(v);
      if (!isNaN(n)) return n;
      const f = typeof fracVal === 'function' ? fracVal(v) : null;
      return f == null ? NaN : f;
    };

    function render() {
      locked = false;
      picked = [];
      const item = rounds[r];
      const vals = item.v.slice();
      const nums = vals.map(numv);
      const allNum = nums.every(x => !isNaN(x));
      const sorted = vals.slice().sort((a, b) => allNum
        ? numv(a) - numv(b)
        : String(a).localeCompare(String(b), 'ar'));
      const target = item.dir === 'desc' ? sorted.slice().reverse() : sorted;

      S.progress(states, r);
      const box = h('div', 'q-card');
      const dirTxt = item.dir === 'desc' ? 'تنازلياً (من الأكبر إلى الأصغر)' : 'تصاعدياً (من الأصغر إلى الأكبر)';
      box.innerHTML = '<div class="q-label">جولة ' + (r + 1) + ' من ' + N + ' · اضغط بالترتيب ' + dirTxt + '</div>' +
        '<div class="q-text" style="font-size:19px">' + esc(item.q || ('رتب الأعداد ' + dirTxt)) + '</div>';

      const board = h('div', 'order-board');
      const track = h('div', 'order-track', '<span class="ph">سيظهر ترتيبك هنا…</span>');
      const pool = h('div', 'order-pool');
      R.shuffle(vals).forEach(v => {
        const t = h('button', 'tile', esc(typeof v === 'number' ? M.n(v) : v));
        t.type = 'button';
        t.dataset.v = v;
        t.addEventListener('click', ev => pick(v, t, target, ev));
        pool.appendChild(t);
      });
      board.appendChild(track); board.appendChild(pool);
      box.appendChild(board);
      const foot = h('div');
      box.appendChild(foot);
      S.stage.innerHTML = '';
      S.stage.appendChild(box);
      S._track = track;
      S._key = ev => {
        if (locked && ev.key === 'Enter') { ev.preventDefault(); next(); }
      };
    }

    function pick(v, tile, target, ev) {
      if (locked || tile.classList.contains('used')) return;
      const want = target[picked.length];
      const ok = String(v) === String(want);
      if (ok) {
        tile.classList.add('used', 'picked');
        picked.push(v);
        S.setStreak(S.streak + 1);
        S.add(8 + Math.min(8, S.streak * 2), ev);
        UI().sfx && UI().sfx('good');
        UI().buzz && UI().buzz(20);
        const tr = S._track;
        if (picked.length === 1) tr.innerHTML = '';
        tr.appendChild(h('span', 'tile picked', esc(typeof v === 'number' ? M.n(v) : v)));
        if (picked.length === target.length) roundDone();
      } else {
        S.setStreak(0);
        S.mistakes++;
        tile.classList.add('err');
        UI().sfx && UI().sfx('bad');
        UI().buzz && UI().buzz(50);
        setTimeout(() => tile.classList.remove('err'), 450);
      }
    }

    function roundDone() {
      locked = true;
      S.correct++;
      states[r] = 'done';
      S.progress(states, r);
      const item = rounds[r];
      const foot = S.stage.querySelector('.q-card > div:last-child');
      foot.innerHTML = explainHTML(true, 'ترتيب صحيح! 🎯', item.e || 'أحسنت في الترتيب.');
      bindNext(foot, next);
      S._nextBtn = foot.querySelector('[data-next]');
      if (S._nextBtn) S._nextBtn.focus();
    }

    function next() {
      r++;
      if (r >= N) { S.progress(states, -1); S.finish({}); return; }
      render();
    }

    S.root.addEventListener('keydown', e => { if (S._key) S._key(e); });
    render();
    return S;
  }

  /* ================= المشغّل ================= */
  const impl = { quiz, arcade, tf: tfGame, input: inputGame, match: matchGame, order: orderGame };

  window.Games = {
    list: () => window.GAME_META || [],
    run(cfg) {
      const fn = impl[cfg.mode];
      if (!fn) throw new Error('لعبة غير معروفة: ' + cfg.mode);
      return fn(cfg);
    }
  };
})();
