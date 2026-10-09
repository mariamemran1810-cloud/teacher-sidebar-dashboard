<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - منصة المعلم الليبي</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                        tajawal: ['Tajawal', 'sans-serif'],
                    },
                    colors: {
                        libyan: {
                            green: '#006233',
                            gold: '#D4AF37',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Cairo', 'Tajawal', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #006233 0%, #004d29 50%, #003d20 100%); }
        .gold-gradient { background: linear-gradient(135deg, #D4AF37 0%, #B8960C 100%); }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 font-cairo">
    <div class="min-h-screen flex">
        <!-- Right Side - Login/Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-xl gold-gradient flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">منصة المعلم الليبي</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">نظام إدارة التعليم الليبي</p>
                </div>

                <!-- Tabs -->
                <div class="flex bg-gray-100 dark:bg-gray-800 rounded-xl p-1 mb-6">
                    <button id="login-tab" onclick="switchTab('login')" class="flex-1 py-2.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-700 text-green-700 dark:text-green-400 shadow-sm transition-all">
                        تسجيل الدخول
                    </button>
                    <button id="register-tab" onclick="switchTab('register')" class="flex-1 py-2.5 rounded-lg text-sm font-semibold text-gray-500 dark:text-gray-400 transition-all">
                        حساب جديد
                    </button>
                </div>

                <!-- Login Form -->
                <div id="login-form" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm" placeholder="example@mail.com" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">كلمة المرور</label>
                        <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm" placeholder="••••••••" dir="ltr">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-600 dark:text-gray-400">تذكرني</span>
                        </label>
                        <a href="#" class="text-sm text-green-600 dark:text-green-400 hover:underline">نسيت كلمة المرور؟</a>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-green-700 hover:bg-green-800 text-white font-semibold shadow-lg shadow-green-700/30 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        تسجيل الدخول
                    </button>
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-gray-700"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-4 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">أو</span></div>
                    </div>
                    <button onclick="demoLogin()" class="w-full py-3 rounded-xl gold-gradient text-white font-semibold shadow-lg shadow-yellow-600/30 transition-all transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        دخول تجريبي سريع
                    </button>
                </div>

                <!-- Register Form -->
                <div id="register-form" class="space-y-5 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">الاسم الكامل</label>
                        <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm" placeholder="أدخل اسمك الكامل">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">البريد الإلكتروني</label>
                        <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm" placeholder="example@mail.com" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">كلمة المرور</label>
                        <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm" placeholder="••••••••" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm" placeholder="••••••••" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">نوع الحساب</label>
                        <select name="role" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-sm">
                            <option value="student">طالب</option>
                            <option value="teacher">معلم</option>
                            <option value="admin">مشرف</option>
                        </select>
                    </div>
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-600 dark:text-gray-400">أوافق على <a href="#" class="text-green-600 dark:text-green-400 hover:underline">الشروط والأحكام</a> و <a href="#" class="text-green-600 dark:text-green-400 hover:underline">سياسة الخصوصية</a></span>
                    </label>
                    <button type="submit" class="w-full py-3 rounded-xl bg-green-700 hover:bg-green-800 text-white font-semibold shadow-lg shadow-green-700/30 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        إنشاء الحساب
                    </button>
                </div>

                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                    &copy; {{ date('Y') }} منصة المعلم الليبي
                </p>
            </div>
        </div>

        <!-- Left Side - Decorative -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg relative items-center justify-center p-12">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 right-20 w-72 h-72 rounded-full bg-white blur-3xl"></div>
                <div class="absolute bottom-20 left-20 w-96 h-96 rounded-full bg-yellow-300 blur-3xl"></div>
            </div>
            <div class="relative text-center text-white max-w-lg">
                <div class="w-20 h-20 mx-auto mb-8 rounded-2xl gold-gradient flex items-center justify-center shadow-2xl pulse-gold">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-6">مرحباً بك في منصة المعلم الليبي</h2>
                <p class="text-lg text-green-100 leading-relaxed mb-8">
                    منصة متكاملة لإدارة العملية التعليمية في ليبيا، تجمع بين المعلمين والطلاب وأولياء الأمور في بيئة تعليمية تفاعلية حديثة.
                </p>
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-3xl font-bold text-yellow-300">+500</div>
                        <div class="text-sm text-green-200">مدرسة</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-yellow-300">+10K</div>
                        <div class="text-sm text-green-200">معلم</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-yellow-300">+50K</div>
                        <div class="text-sm text-green-200">طالب</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.getElementById('login-form').classList.toggle('hidden', tab !== 'login');
            document.getElementById('register-form').classList.toggle('hidden', tab !== 'register');
            document.getElementById('login-tab').classList.toggle('bg-white', tab === 'login');
            document.getElementById('register-tab').classList.toggle('bg-white', tab === 'register');
            document.getElementById('login-tab').classList.toggle('dark:bg-gray-700', tab === 'login');
            document.getElementById('register-tab').classList.toggle('dark:bg-gray-700', tab === 'register');
        }
        function demoLogin() {
            alert('سيتم تسجيل الدخول بحساب تجريبي');
        }
    </script>
</body>
</html>