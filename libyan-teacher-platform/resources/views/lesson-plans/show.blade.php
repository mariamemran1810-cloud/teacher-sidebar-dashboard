@extends('layout')

@section('title', $lessonPlan->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumbs -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 space-x-reverse">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-indigo-600">لوحة التحكم</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('lesson-plans.index') }}" class="mr-2 text-gray-500 hover:text-indigo-600">خطط الدروس</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="mr-2 text-gray-700 font-medium">{{ $lessonPlan->title }}</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $lessonPlan->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $lessonPlan->subject->name ?? 'غير محدد' }} - {{ $lessonPlan->classroom->name ?? 'غير محدد' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('lesson-plans.edit', $lessonPlan) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
            <form action="{{ route('lesson-plans.destroy', $lessonPlan) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف
                </button>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md flex items-center">
            <svg class="w-5 h-5 ml-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Lesson Plan Details -->
    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <!-- Meta Info -->
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">التاريخ</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $lessonPlan->date }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">المدة</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $lessonPlan->duration }} دقيقة</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">الحصة</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $lessonPlan->period }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">المادة</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $lessonPlan->subject->name ?? 'غير محدد' }}</p>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <div class="px-4 py-5 sm:px-6 space-y-6">
            <!-- Objectives -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    الأهداف
                </h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->objectives }}</p>
            </div>

            <!-- Introduction -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    التمهيد
                </h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->introduction }}</p>
            </div>

            <!-- Content -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    المحتوى
                </h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->content }}</p>
            </div>

            <!-- Activities -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    الأنشطة
                </h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->activities }}</p>
            </div>

            <!-- Assessment -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    التقييم
                </h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->assessment }}</p>
            </div>

            <!-- Homework -->
            @if($lessonPlan->homework)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        الواجب المنزلي
                    </h3>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->homework }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('lesson-plans.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600">
            <svg class="w-4 h-4 ml-1 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى خطط الدروس
        </a>
    </div>
</div>
