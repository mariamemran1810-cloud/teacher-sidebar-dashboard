@extends('layout')

@section('title', $exam->title)

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
                <a href="{{ route('exams.index') }}" class="mr-2 text-gray-500 hover:text-indigo-600">الامتحانات</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="mr-2 text-gray-700 font-medium">{{ $exam->title }}</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $exam->subject->name ?? 'غير محدد' }} - {{ $exam->classroom->name ?? 'غير محدد' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('exams.edit', $exam) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
            <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الامتحان؟');">
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

    <!-- Exam Details -->
    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <!-- Meta Info -->
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">النوع</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $exam->type == 'midterm' ? 'bg-yellow-100 text-yellow-800' : ($exam->type == 'final' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ $exam->type == 'midterm' ? 'نصفي' : ($exam->type == 'final' ? 'نهائي' : 'شهري') }}
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">التاريخ</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $exam->exam_date }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">المدة</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $exam->duration_minutes }} دقيقة</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">الدرجات الكلية</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $exam->total_marks }}</p>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($exam->description)
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    الوصف
                </h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $exam->description }}</p>
            </div>
        @endif

        <!-- Results Section -->
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium text-gray-900 flex items-center mb-4">
                <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                النتائج
            </h3>

            @if($exam->results && $exam->results->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدرجة</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النسبة</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($exam->results as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $result->student->name ?? 'غير محدد' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $result->marks_obtained }} / {{ $exam->total_marks }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ round(($result->marks_obtained / $exam->total_marks) * 100, 1) }}%</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $percentage = ($result->marks_obtained / $exam->total_marks) * 100;
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $percentage >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $percentage >= 50 ? 'ناجح' : 'راسب' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد نتائج</h3>
                    <p class="mt-1 text-sm text-gray-500">لم يتم إدخال نتائج لهذا الامتحان بعد.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('exams.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600">
            <svg class="w-4 h-4 ml-1 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى الامتحانات
        </a>
    </div>
</div>
