/**
 * منصة المعلم الليبي — ملف الجافاسكربت الرئيسي
 * Libyan Teacher Platform — Main JavaScript
 */

(function () {
  'use strict';

  /* ============================================
     Theme Toggle (Dark/Light) with localStorage
     ============================================ */
  const themeBtn = document.getElementById('themeBtn');
  const body = document.body;

  function getPreferredTheme() {
    const saved = localStorage.getItem('ltp-theme');
    if (saved) return saved;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    body.setAttribute('data-theme', theme);
    localStorage.setItem('ltp-theme', theme);
    if (themeBtn) {
      themeBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
      themeBtn.title = theme === 'dark' ? 'الوضع النهاري' : 'الوضع الليلي';
    }
  }

  applyTheme(getPreferredTheme());

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const current = body.getAttribute('data-theme');
      applyTheme(current === 'dark' ? 'light' : 'dark');
    });
  }

  /* ============================================
     Mobile Menu Toggle
     ============================================ */
  const menuBtn = document.getElementById('menuBtn');
  const nav = document.getElementById('nav');

  if (menuBtn && nav) {
    menuBtn.addEventListener('click', () => {
      nav.classList.toggle('open');
      menuBtn.textContent = nav.classList.contains('open') ? '✕' : '☰';
    });

    // Close menu when clicking a nav link
    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('open');
        menuBtn.textContent = '☰';
      });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && !menuBtn.contains(e.target)) {
        nav.classList.remove('open');
        menuBtn.textContent = '☰';
      }
    });
  }

  /* ============================================
     Header Scroll Effect
     ============================================ */
  const header = document.querySelector('.header');

  function handleHeaderScroll() {
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  }

  window.addEventListener('scroll', handleHeaderScroll, { passive: true });
  handleHeaderScroll();

  /* ============================================
     Scroll to Top Button
     ============================================ */
  const toTopBtn = document.getElementById('toTop');

  function handleToTopVisibility() {
    if (window.scrollY > 400) {
      toTopBtn.classList.add('visible');
    } else {
      toTopBtn.classList.remove('visible');
    }
  }

  window.addEventListener('scroll', handleToTopVisibility, { passive: true });
  handleToTopVisibility();

  if (toTopBtn) {
    toTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ============================================
     Counter Animation for Stats
     ============================================ */
  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-count'), 10);
    const duration = 2000;
    const startTime = performance.now();
    const startValue = 0;

    function update(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);

      // Ease out cubic
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = Math.round(startValue + (target - startValue) * eased);

      el.textContent = current.toLocaleString('ar-LY');

      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }

    requestAnimationFrame(update);
  }

  // Intersection Observer for stats
  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        statsObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('.num[data-count]').forEach(el => {
    statsObserver.observe(el);
  });

  /* ============================================
     Tool Tabs Switching
     ============================================ */
  const toolTabs = document.querySelectorAll('.tool-tab');
  const toolPanels = document.querySelectorAll('.tool-panel');

  toolTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.getAttribute('data-tool');

      toolTabs.forEach(t => t.classList.remove('active'));
      toolPanels.forEach(p => p.classList.remove('active'));

      tab.classList.add('active');
      const panel = document.getElementById('tool-' + target);
      if (panel) panel.classList.add('active');
    });
  });

  /* ============================================
     FAQ Accordion
     ============================================ */
  const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach(item => {
    const question = item.querySelector('.faq-q');
    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');

      // Close all
      faqItems.forEach(i => i.classList.remove('open'));

      // Open clicked (if it wasn't open)
      if (!isOpen) {
        item.classList.add('open');
      }
    });
  });

  /* ============================================
     Lesson Generator
     ============================================ */
  const lsBtn = document.getElementById('ls_btn');
  const lsOut = document.getElementById('ls_out');

  function generateLesson() {
    const title = document.getElementById('ls_title').value.trim();
    const subject = document.getElementById('ls_subject').value.trim();
    const grade = document.getElementById('ls_grade').value;
    const time = document.getElementById('ls_time').value;

    if (!title || !subject) {
      lsOut.innerHTML = '⚠️ يرجى إدخال عنوان الدرس واسم المادة على الأقل.';
      return;
    }

    const timeNum = parseInt(time);

    // Calculate time distribution
    const introTime = Math.round(timeNum * 0.1);
    const presentationTime = Math.round(timeNum * 0.4);
    const practiceTime = Math.round(timeNum * 0.3);
    const assessmentTime = Math.round(timeNum * 0.15);
    const wrapTime = timeNum - introTime - presentationTime - practiceTime - assessmentTime;

    // Subject-specific content
    const subjectTips = getSubjectTips(subject);
    const objectives = getObjectives(subject, title);
    const activities = getActivities(subject, title);

    const lesson = `
📋 تحضير درس: ${title}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📌 المادة: ${subject}
📌 الصف: ${grade}
⏱️ مدة الحصة: ${time}

🎯 الأهداف التعليمية:
${objectives}

📊 توزيع الوقت:
  • التمهيد: ${introTime} دقيقة
  • العرض والشرح: ${presentationTime} دقيقة
  • التطبيق والتدريب: ${practiceTime} دقيقة
  • التقويم: ${assessmentTime} دقيقة
  • الخاتمة: ${wrapTime} دقيقة

📖 الإجراءات:

1️⃣ التمهيد (${introTime} دقائق):
   • تحفيز الطلاب بطرح سؤال مرتبط بموضوع ${title}
   • ربط الدرس السابق بالدرس الحالي
   • إعلان عنوان الدرس وأهدافه

2️⃣ العرض والشرح (${presentationTime} دقائق):
   • شرح المفاهيم الأساسية لـ ${title}
   • استخدام الوسائل التعليمية المناسبة
   • توضيح الأمثلة خطوة بخطوة
${subjectTips.presentation}

3️⃣ التطبيق والتدريب (${practiceTime} دقائق):
${activities}

4️⃣ التقويم (${assessmentTime} دقائق):
   • طرح أسئلة شاملة على الدرس
   • تصحيح الأخطاء الشائعة
   • تعزيز الإجابات الصحيحة

5️⃣ الخاتمة (${wrapTime} دقائق):
   • تلخيص أهم النقاط
   • إعطاء واجب منزلي
   • إعلان موضوع الدرس القادم

💡 ملاحظات تربوية:
${subjectTips.notes}

📎 الوسائل المطلوبة: سبورة، طباشير/أقلام، وسائل تعليمية مناسبة للموضوع
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✨ تم توليد التحضير بواسطة منصة المعلم الليبي
`;

    lsOut.textContent = lesson;
  }

  function getSubjectTips(subject) {
    const s = subject.toLowerCase();
    if (s.includes('رياض') || s.includes('math')) {
      return {
        presentation: '   • حل أمثلة على السبورة خطوة بخطوة\n   • استخدام الوسائل الحسية والرسوم التوضيحية',
        notes: '   • تأكد من إتقان الطلاب للمهارات الأساسية قبل الانتقال للموضوع الجديد\n   • نوّع في طرق الحل وعرّف الطلاب بأكثر من طريقة'
      };
    } else if (s.includes('عربي') || s.includes('لغة')) {
      return {
        presentation: '   • قراءة نموذجية من المعلم ثم قراءة الطلاب\n   • شرح القواعد والبلاغة بأمثلة من النص',
        notes: '   • شجّع الطلاب على القراءة الجهرية والتعبير الشفهي\n   • اهتم بتصحيح الأخطاء الإملائية والنحوية فوراً'
      };
    } else if (s.includes('علوم') || s.includes('science')) {
      return {
        presentation: '   • إجراء تجربة عملية أو محاكاة إن أمكن\n   • ربط النظرية بالتطبيقات الحياتية',
        notes: '   • اجعل الطلاب يشاركون في التجارب قدر الإمكان\n   • استخدم الصور والرسوم التوضيحية لتبسيط المفاهيم'
      };
    } else if (s.includes('إنجليزي') || s.includes('english')) {
      return {
        presentation: '   • استخدام اللغة الإنجليزية قدر الإمكان في الشرح\n   • تدريب الطلاب على النطق الصحيح',
        notes: '   • شجّع المحادثة باللغة الإنجليزية\n   • نوّع في الأنشطة السمعية والقرائية'
      };
    } else if (s.includes('اجتماع') || s.includes('تاريخ') || s.includes('جغرافيا')) {
      return {
        presentation: '   • استخدام الخرائط والصور التاريخية\n   • ربط الأحداث بالواقع المعاصر',
        notes: '   • شجّع البحث والاطلاع خارج الكتاب\n   • استخدم أسلوب القصص والسرد التاريخي'
      };
    }
    return {
      presentation: '   • استخدام الوسائل التعليمية المناسبة\n   • التدرج في الشرح من السهل إلى الصعب',
      notes: '   • نوّع في أساليب التدريس\n   • تابع الفروق الفردية بين الطلاب'
    };
  }

  function getObjectives(subject, title) {
    return `   • أن يتعرف الطالب على مفهوم ${title}
   • أن يشرح الطالب أهم عناصر وأسس ${title}
   • أن يطبق الطالب ما تعلمه في مواقف جديدة
   • أن يُظهر الطالب اتجاهات إيجابية نحو مادة ${subject}`;
  }

  function getActivities(subject, title) {
    return `   • تقسيم الطلاب إلى مجموعات صغيرة
   • تكليف كل مجموعة بحل تمارين حول ${title}
   • مناقشة إجابات المجموعات وتصحيحها
   • تكليف الطلاب بحل تمرين فردي على السبورة`;
  }

  if (lsBtn) {
    lsBtn.addEventListener('click', generateLesson);
  }

  /* ============================================
     Grade Calculator
     ============================================ */
  const gBtn = document.getElementById('g_btn');
  const gTotal = document.getElementById('g_total');
  const gGrade = document.getElementById('g_grade');
  const gNote = document.getElementById('g_note');

  function calculateGrade() {
    const work = Math.min(Math.max(parseFloat(document.getElementById('g_work').value) || 0, 0), 20);
    const quiz = Math.min(Math.max(parseFloat(document.getElementById('g_quiz').value) || 0, 0), 20);
    const exam = Math.min(Math.max(parseFloat(document.getElementById('g_exam').value) || 0, 0), 60);

    const total = Math.round((work + quiz + exam) * 10) / 10;

    gTotal.textContent = total + ' / 100';

    let grade, note, color;

    if (total >= 90) {
      grade = 'ممتاز 🏆';
      note = 'أداء رائع ومتميز — استمر على هذا المستوى!';
      color = '#10b981';
    } else if (total >= 80) {
      grade = 'جيد جداً ⭐';
      note = 'أداء جيد جداً — أنت قريب من التميز.';
      color = '#0f766e';
    } else if (total >= 70) {
      grade = 'جيد 👍';
      note = 'أداء جيد — راجع نقاط الضعف لتحسين النتيجة.';
      color = '#d97706';
    } else if (total >= 60) {
      grade = 'مقبول 📋';
      note = 'تحتاج إلى مزيد من الجهد والمراجعة.';
      color = '#f59e0b';
    } else {
      grade = 'يحتاج دعماً ⚠️';
      note = 'يُنصح بمراجعة الدروس الأساسية وحل تمارين إضافية.';
      color = '#ef4444';
    }

    gGrade.textContent = grade;
    gGrade.style.color = color;
    gNote.textContent = note;
  }

  if (gBtn) {
    gBtn.addEventListener('click', calculateGrade);
  }

  /* ============================================
     Certificate Generator
     ============================================ */
  const cBtn = document.getElementById('c_btn');

  function generateCertificate() {
    const name = document.getElementById('c_name').value.trim();
    const teacher = document.getElementById('c_teacher').value.trim();
    const type = document.getElementById('c_type').value;
    const school = document.getElementById('c_school').value.trim();
    const reason = document.getElementById('c_reason').value.trim();

    if (!name) {
      alert('يرجى إدخال اسم الطالب/الطالبة');
      return;
    }

    const today = new Date();
    const dateStr = today.toLocaleDateString('ar-LY', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    const certHTML = `
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>${type} — ${name}</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Cairo', 'Tajawal', sans-serif;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem;
    }
    .certificate {
      width: 800px;
      max-width: 100%;
      background: #fff;
      border: 8px double #0f766e;
      border-radius: 16px;
      padding: 3rem;
      text-align: center;
      position: relative;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .certificate::before {
      content: '';
      position: absolute;
      top: 12px; right: 12px; left: 12px; bottom: 12px;
      border: 2px solid #d97706;
      border-radius: 10px;
      pointer-events: none;
    }
    .cert-header { margin-bottom: 2rem; }
    .cert-header .logo { font-size: 3rem; margin-bottom: 0.5rem; }
    .cert-header h1 { font-size: 1.6rem; color: #0f766e; font-weight: 800; }
    .cert-header h2 { font-size: 1.2rem; color: #d97706; font-weight: 700; margin-top: 0.25rem; }
    .cert-body { margin: 2rem 0; }
    .cert-body .to { font-size: 1.1rem; color: #64748b; margin-bottom: 0.5rem; }
    .cert-body .name {
      font-size: 2.2rem;
      font-weight: 900;
      color: #0b1f3a;
      margin: 1rem 0;
      padding: 0.5rem 2rem;
      border-bottom: 2px solid #d97706;
      display: inline-block;
    }
    .cert-body .reason {
      font-size: 1.1rem;
      color: #334155;
      line-height: 2;
      max-width: 550px;
      margin: 1rem auto;
    }
    .cert-footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 3rem;
      padding: 0 2rem;
    }
    .cert-footer .sig { text-align: center; }
    .cert-footer .sig .line {
      width: 180px;
      border-bottom: 2px solid #334155;
      margin-bottom: 0.5rem;
    }
    .cert-footer .sig span { font-size: 0.9rem; color: #64748b; }
    .cert-footer .date { text-align: center; }
    .cert-footer .date span { font-size: 0.9rem; color: #64748b; display: block; }
    .cert-footer .date .d { font-size: 1rem; color: #334155; font-weight: 600; }
    .cert-stamp {
      position: absolute;
      bottom: 3rem;
      left: 3rem;
      width: 100px;
      height: 100px;
      border: 3px solid #0f766e;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #0f766e;
      font-weight: 800;
      font-size: 0.75rem;
      transform: rotate(-15deg);
      opacity: 0.6;
    }
    @media print {
      body { background: #fff; padding: 0; }
      .certificate { border: 4px double #0f766e; box-shadow: none; border-radius: 0; }
    }
  </style>
</head>
<body>
  <div class="certificate">
    <div class="cert-header">
      <div class="logo">🎓</div>
      <h1>${type}</h1>
      <h2>منصة المعلم الليبي</h2>
    </div>
    <div class="cert-body">
      <p class="to">تشهد إدارة المدرسة بأن الطالب/الطالبة</p>
      <div class="name">${name}</div>
      <p class="reason">${reason}</p>
      ${school ? `<p style="margin-top:1rem;font-size:1rem;color:#64748b;">المدرسة: <b>${school}</b></p>` : ''}
    </div>
    <div class="cert-footer">
      <div class="sig">
        <div class="line"></div>
        <span>توقيع المعلم: ${teacher || '＿＿＿＿＿＿'}</span>
      </div>
      <div class="date">
        <span>تاريخ الإصدار</span>
        <span class="d">${dateStr}</span>
      </div>
    </div>
    <div class="cert-stamp">معتمد ✓</div>
  </div>
  <script>window.onload = function() { window.print(); };</script>
</body>
</html>`;

    const printWindow = window.open('', '_blank', 'width=900,height=700');
    if (printWindow) {
      printWindow.document.write(certHTML);
      printWindow.document.close();
    } else {
      alert('يرجى السماح بالنوافذ المنبثقة لعرض الشهادة');
    }
  }

  if (cBtn) {
    cBtn.addEventListener('click', generateCertificate);
  }

  /* ============================================
     Contact Form Handler
     ============================================ */
  const contactForm = document.getElementById('contactForm');
  const formOut = document.getElementById('formOut');

  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const name = contactForm.querySelector('input').value.trim();
      const contact = contactForm.querySelectorAll('input')[1].value.trim();
      const message = contactForm.querySelector('textarea').value.trim();

      if (!name || !contact || !message) {
        formOut.style.display = 'block';
        formOut.innerHTML = '⚠️ يرجى ملء جميع الحقول المطلوبة.';
        formOut.style.color = '#ef4444';
        return;
      }

      // Simulate form submission
      formOut.style.display = 'block';
      formOut.style.color = '#10b981';
      formOut.innerHTML = `✅ شكراً ${name}! تم استلام رسالتك بنجاح. سنتواصل معك قريباً عبر ${contact}.`;

      contactForm.reset();

      // Hide success message after 5 seconds
      setTimeout(() => {
        formOut.style.display = 'none';
      }, 5000);
    });
  }

  /* ============================================
     Smooth Scroll for Anchor Links
     ============================================ */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '') return;

      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        const headerHeight = header ? header.offsetHeight : 0;
        const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 10;

        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

  /* ============================================
     Active Nav Link Highlighting
     ============================================ */
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav a');

  function highlightNavOnScroll() {
    const scrollPos = window.scrollY + 100;

    sections.forEach(section => {
      const top = section.offsetTop;
      const height = section.offsetHeight;
      const id = section.getAttribute('id');

      if (scrollPos >= top && scrollPos < top + height) {
        navLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + id) {
            link.classList.add('active');
          }
        });
      }
    });
  }

  window.addEventListener('scroll', highlightNavOnScroll, { passive: true });
  highlightNavOnScroll();

  /* ============================================
     Animate on Scroll
     ============================================ */
  const animateElements = document.querySelectorAll('.card, .level-card, .quote, .section-head');

  const animateObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animated');
        animateObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  animateElements.forEach(el => {
    el.classList.add('animate-on-scroll');
    animateObserver.observe(el);
  });

  /* ============================================
     Input Validation for Grade Calculator
     ============================================ */
  const gradeInputs = document.querySelectorAll('#g_work, #g_quiz, #g_exam');
  gradeInputs.forEach(input => {
    input.addEventListener('change', () => {
      const min = parseFloat(input.min) || 0;
      const max = parseFloat(input.max) || 100;
      let val = parseFloat(input.value) || 0;
      val = Math.min(Math.max(val, min), max);
      input.value = val;
    });
  });

})();
