<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'منصة المعلم الليبي')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        gold: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', 'Tajawal', sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200; }
        .sidebar-link:hover { @apply bg-emerald-700 text-white; }
        .sidebar-link.active { @apply bg-emerald-700 text-white shadow-lg; }
        .card { @apply bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300; }
        .card:hover { @apply shadow-md border-emerald-200; }
        .btn { @apply inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 cursor-pointer; }
        .btn-primary { @apply bg-gradient-to-l from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-200 hover:shadow-xl hover:-translate-y-0.5; }
        .btn-gold { @apply bg-gradient-to-l from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 hover:shadow-xl hover:-translate-y-0.5; }
        .btn-ghost { @apply bg-white border-2 border-gray-200 text-gray-700 hover:border-emerald-500 hover:text-emerald-700; }
        .btn-danger { @apply bg-red-500 text-white hover:bg-red-600; }
        .btn-sm { @apply px-3 py-1.5 text-xs rounded-lg; }
        .input { @apply w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:outline-none transition-colors duration-200 bg-white; }
        .input:focus { @apply ring-4 ring-emerald-100; }
        .label { @apply block text-sm font-bold text-gray-700 mb-1.5; }
        .table-container { @apply overflow-x-auto rounded-2xl border border-gray-100; }
        .table { @apply w-full text-sm; }
        .table th { @apply bg-emerald-50 text-emerald-800 font-bold px-4 py-3 text-right border-b border-emerald-100; }
        .table td { @apply px-4 py-3 border-b border-gray-50 hover:bg-emerald-50/50 transition-colors; }
        .table tr:last-child td { @apply border-b-0; }
        .badge { @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-bold; }
        .badge-success { @apply bg-emerald-100 text-emerald-700; }
        .badge-warning { @apply bg-amber-100 text-amber-700; }
        .badge-danger { @apply bg-red-100 text-red-700; }
        .badge-info { @apply bg-blue-100 text-blue-700; }
        .stat-card { @apply relative overflow-hidden rounded-2xl p-6 text-white shadow-lg; }
        .stat-card .icon { @apply absolute -left-4 -bottom-4 text-6xl opacity-20; }
        .breadcrumb { @apply flex items-center gap-2 text-sm text-gray-500 mb-6; }
        .breadcrumb a { @apply text-emerald-600 hover:text-emerald-700 font-semibold; }
        .breadcrumb span { @apply text-gray-400; }
        .empty-state { @apply text-center py-16; }
        .empty-state .icon { @apply text-6xl mb-4 opacity-30; }
        .toast { @apply fixed bottom-6 left-6 z-50 px-6 py-4 rounded-2xl shadow-2xl text-white font-bold transform transition-all duration-300; }
        .toast-success { @apply bg-emerald-600; }
        .toast-error { @apply bg-red-500; }
        @media (max-width: 768px) {
            .sidebar { @apply fixed inset-y-0 right-0 z-40 transform translate-x-full transition-transform duration-300; }
            .sidebar.open { @apply translate-x-0; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased dark:bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="sidebar fixed md:static inset-y-0 right-0 z-40 w-72 bg-gradient-to-b from-emerald-800 to-emerald-900 text-white flex flex-col transform transition-transform duration-300 md:translate-x-0" id="sidebar">
            <div class="p-6 border-b border-emerald-700/50">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🎓</span>
                    <div>
                        <h1 class="font-black text-lg leading-tight">منصة المعلم الليبي</h1>
                        <p class="text-emerald-300 text-xs">كل أدوات المعلم في مكان واحد</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="text-lg">🏠</span> لوحة التحكم
                </a>
                <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <span class="text-lg">👨‍🎓</span> الطلاب
                </a>
                <a href="{{ route('teachers.index') }}" class="sidebar-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <span class="text-lg">👨‍🏫</span> المدرسون
                </a>
                <a href="{{ route('subjects.index') }}" class="sidebar-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                    <span class="text-lg">📚</span> المواد
                </a>
                <a href="{{ route('lesson-plans.index') }}" class="sidebar-link {{ request()->routeIs('lesson-plans.*') ? 'active' : '' }}">
                    <span class="text-lg">📝</span> خطط الدروس
                </a>
                <a href="{{ route('exams.index') }}" class="sidebar-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                    <span class="text-lg">📊</span> الاختبارات
                </a>
                <a href="{{ route('grades.index') }}" class="sidebar-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                    <span class="text-lg">📈</span> الدرجات
                </a>
                <a href="{{ route('attendance.index') }}" class="sidebar-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <span class="text-lg">🗓️</span> الحضور
                </a>
                <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <span class="text-lg">💰</span> المدفوعات
                </a>
                <a href="{{ route('weekly-followups.index') }}" class="sidebar-link {{ request()->routeIs('weekly-followups.*') ? 'active' : '' }}">
                    <span class="text-lg">📋</span> المتابعة الأسبوعية
                </a>
                <a href="{{ route('monthly-reports.index') }}" class="sidebar-link {{ request()->routeIs('monthly-reports.*') ? 'active' : '' }}">
                    <span class="text-lg">📅</span> التقارير الشهرية
                </a>
                <a href="{{ route('parent-messages.index') }}" class="sidebar-link {{ request()->routeIs('parent-messages.*') ? 'active' : '' }}">
                    <span class="text-lg">👨‍👩‍👧</span> رسائل أولياء الأمور
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <span class="text-lg">💬</span> المحادثة
                </a>
                <a href="{{ route('ai.index') }}" class="sidebar-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                    <span class="text-lg">🤖</span> مساعد AI
                </a>
                <a href="{{ route('library.index') }}" class="sidebar-link {{ request()->routeIs('library.*') ? 'active' : '' }}">
                    <span class="text-lg">📦</span> المكتبة
                </a>
                <a href="{{ route('training.index') }}" class="sidebar-link {{ request()->routeIs('training.*') ? 'active' : '' }}">
                    <span class="text-lg">🎓</span> التدريب
                </a>
                <a href="{{ route('community.index') }}" class="sidebar-link {{ request()->routeIs('community.*') ? 'active' : '' }}">
                    <span class="text-lg">📢</span> المجتمع
                </a>
                <a href="{{ route('school.index') }}" class="sidebar-link {{ request()->routeIs('school.*') ? 'active' : '' }}">
                    <span class="text-lg">🏫</span> المدرسة
                </a>
                <a href="{{ route('schedule.index') }}" class="sidebar-link {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
                    <span class="text-lg">🗓️</span> الجدول
                </a>
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <span class="text-lg">🔔</span> الإشعارات
                </a>
            </nav>
            <div class="p-4 border-t border-emerald-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name ?? 'م', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm truncate">{{ auth()->user()->name ?? 'مستخدم' }}</p>
                        <p class="text-emerald-300 text-xs">{{ auth()->user()->role ?? 'معلم' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-emerald-300 hover:text-white transition-colors" title="خروج">⏻</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button class="md:hidden text-gray-600 hover:text-emerald-600" id="menuBtn">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            <h2 class="font-bold text-gray-800">@yield('page-title', 'لوحة التحكم')</h2>
                            <p class="text-xs text-gray-500 hidden sm:block">@yield('page-description', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:text-emerald-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </a>
                        <a href="{{ route('profile') }}" class="flex items-center gap-2 text-gray-700 hover:text-emerald-600 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm">
                                {{ substr(auth()->user()->name ?? 'م', 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold hidden sm:block">{{ auth()->user()->name ?? 'مستخدم' }}</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-semibold flex items-center gap-2">
                        <span>✅</span> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-semibold flex items-center gap-2">
                        <span>❌</span> {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div class="fixed inset-0 bg-black/50 z-30 hidden md:hidden" id="sidebarOverlay"></div>

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (menuBtn) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('hidden');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.add('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
