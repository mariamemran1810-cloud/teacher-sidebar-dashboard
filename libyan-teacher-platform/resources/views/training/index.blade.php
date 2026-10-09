@extends('layout')

@section('title', 'الدورات التدريبية - منصة المعلم الليبي')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="text-sm">
        <ol class="list-none p-0 inline-flex items-center gap-2">
            <li><a href="{{ url('/') }}" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-500">الدورات التدريبية</span></li>
        </ol>
    </nav>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
            <strong class="font-bold">نجاح!</strong> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
            <strong class="font-bold">خطأ!</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800">الدورات التدريبية</h1>
        <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
            + إضافة دورة جديدة
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <input type="text" placeholder="ابحث عن دورة..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">جميع الفئات</option>
                <option value="pedagogy">التربية</option>
                <option value="tech">التكنولوجيا</option>
                <option value="management">الإدارة</option>
            </select>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg transition">بحث</button>
        </div>
    </div>

    <!-- Course Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses ?? [] as $course)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                <div class="h-40 bg-gradient-to-l from-green-500 to-green-700 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $course->title ?? 'دورة التربية الحديثة' }}</h3>
                    <p class="text-sm text-gray-500 mb-4">دورة شاملة في أساليب التعليم الحديثة وتقنيات التعلم النشط.</p>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">التقدم</span>
                            <span class="font-bold text-green-600">{{ $course->progress ?? 65 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $course->progress ?? 65 }}%"></div>
                        </div>
                    </div>
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition">
                        إكمال الدورة
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p>لا توجد دورات تدريبية حالياً</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
