@extends('layout')

@section('title', 'لوحة التحكم - منصة المعلم الليبي')
@section('page-title', 'لوحة التحكم')
@section('page-description', 'مرحباً بك في منصة المعلم الليبي')

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Welcome Section -->
    <div class="gradient-bg rounded-2xl p-6 lg:p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-48 h-48 bg-yellow-400/10 rounded-full translate-x-1/4 translate-y-1/4"></div>
        <div class="relative flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold mb-2">مرحباً، {{ Auth::user()->name ?? 'المعلم' }}</h1>
                <p class="text-green-100 text-sm lg:text-base">إليك نظرة عامة على أداء المدرسة اليوم</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 text-center">
                    <div class="text-yellow-300 text-2xl font-bold">{{ \Carbon\Carbon::now()->format('d') }}</div>
                    <div class="text-green-100 text-xs">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">1,248</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">الطلاب</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20V5a2 2 0 00-2-2H7a2 2 0 00-2 2v15m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-full">+5%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">86</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">المعلمون</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 px-2 py-1 rounded-full">+8%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">24</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">المواد الدراسية</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 px-2 py-1 rounded-full">+15%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">45</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">الامتحانات</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-full">+20%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">342K</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">المدفوعات (د.ل)</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">إجراءات سريعة</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-green-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">إضافة طالب</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">إضافة معلم</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-purple-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">إنشاء امتحان</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-yellow-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">إضافة درجة</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">تسجيل حضور</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-gray-600 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">إضافة مادة</span>
            </a>
        </div>
    </div>

    <!-- Charts & Activities Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Placeholder -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">إحصائيات الأداء</h3>
                <select class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>هذا الأسبوع</option>
                    <option>هذا الشهر</option>
                    <option>هذا الفصل</option>
                </select>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">رسم بياني للأداء الأكاديمي</p>
                    <p class="text-gray-300 dark:text-gray-600 text-xs mt-1">Chart.js أو مكتبة رسوم بيانية</p>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">آخر النشاطات</h3>
                <a href="#" class="text-sm text-green-600 dark:text-green-400 hover:underline">عرض الكل</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">تم تسجيل طالب جديد</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">منذ 5 دقائق</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">تم إنشاء امتحان جديد</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">منذ 15 دقيقة</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">تم استلام دفعة جديدة</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">منذ 30 دقيقة</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">تم إضافة خطة درس جديدة</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">منذ ساعة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams & Attendance Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Exams -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">الامتحانات القادمة</h3>
                <a href="{{ route('exams.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">عرض الكل</a>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30">
                    <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 dark:text-red-400 font-bold text-sm">15</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">امتحان الرياضيات</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">الصف العاشر - الساعة 9:00 صباحاً</p>
                    </div>
                    <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-2.5 py-1 rounded-full">غداً</span>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-900/30">
                    <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-yellow-600 dark:text-yellow-400 font-bold text-sm">18</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">امتحان اللغة العربية</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">الصف الحادي عشر - الساعة 10:00 صباحاً</p>
                    </div>
                    <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-1 rounded-full">بعد 3 أيام</span>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900/30">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">22</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">امتحان الفيزياء</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">الصف الثاني عشر - الساعة 8:00 صباحاً</p>
                    </div>
                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">بعد أسبوع</span>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">ملخص الحضور اليوم</h3>
                <a href="{{ route('attendance.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">التفاصيل</a>
            </div>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">الحضور</span>
                        <span class="text-sm font-bold text-green-600 dark:text-green-400">92%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: 92%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">الغياب</span>
                        <span class="text-sm font-bold text-red-600 dark:text-red-400">5%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-red-500 h-3 rounded-full transition-all duration-500" style="width: 5%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">التأخير</span>
                        <span class="text-sm font-bold text-yellow-600 dark:text-yellow-400">3%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500" style="width: 3%"></div>
                    </div>
                </div>
                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-white">1,148</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">حاضر</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">62</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">غائب</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">38</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">متأخر</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dashboard specific scripts
    console.log('Dashboard loaded');
</script>
@endpush