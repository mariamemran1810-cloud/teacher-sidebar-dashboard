# منصة المعلم الليبي الرقمية 🇱🇾

منصة متكاملة للمعلم الليبي مبنية بـ Laravel 13 + Blade + MySQL.

## المميزات

### إدارة المدرسة
- إدارة الطلاب والمدرسين والمواد والفصول
- الجداول الدراسية
- الحضور والغياب
- الدرجات والاختبارات
- المحاسبة والرسوم

### أدوات المعلم
- لوحة تحكم شاملة
- التحضير الذكي للدروس
- مساعد الذكاء الاصطناعي
- مكتبة رقمية
- تدريب مهني
- مجتمع المعلمين

### التواصل
- رسائل أولياء الأمور
- غرفة محادثة
- إشعارات

## التثبيت

```bash
# استنساخ المشروع
git clone <repository-url>
cd libyan-teacher-platform

# تثبيت الاعتماديات
composer install

# إعداد البيئة
cp .env.example .env
php artisan key:generate

# إعداد قاعدة البيانات
# عدّل ملف .env بإدخال بيانات قاعدة البيانات

# تشغيل الـ migrations والـ seeders
php artisan migrate --seed

# تشغيل الخادم
php artisan serve
```

## بيانات الدخول التجريبية

| الدور | البريد الإلكتروني | كلمة المرور |
|-------|------------------|-------------|
| مدير | admin@libyan-teacher.ly | password123 |
| معلم | teacher@libyan-teacher.ly | password123 |
| طالب | student@libyan-teacher.ly | password123 |
| ولي أمر | parent@libyan-teacher.ly | password123 |

## البنية التقنية

- **Laravel 13** - إطار العمل
- **Blade** - محرك القوالب
- **MySQL** - قاعدة البيانات
- **Tailwind CSS** - التنسيقات

## الترخيص

MIT
