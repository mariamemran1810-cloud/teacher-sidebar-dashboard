@extends('layout')

@section('title', 'إضافة درجة')

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
                <a href="{{ route('grades.index') }}" class="mr-2 text-gray-500 hover:text-indigo-600">الدرجات</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="mr-2 text-gray-700 font-medium">إضافة درجة</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">إضافة درجة جديدة</h1>
        <p class="mt-1 text-sm text-gray-500">أدخل تفاصيل الدرجة</p>
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

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md flex items-center">
            <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('grades.store') }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf

        <div class="px-4 py-5 sm:p-6 space-y-6">
            <!-- Student -->
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">الطالب <span class="text-red-500">*</span></label>
                <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('student_id') border-red-500 @endif">
                    <option value="">اختر الطالب</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Exam -->
            <div>
                <label for="exam_id" class="block text-sm font-medium text-gray-700">الامتحان <span class="text-red-500">*</span></label>
                <select name="exam_id" id="exam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('exam_id') border-red-500 @endif">
                    <option value="">اختر الامتحان</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->title }} - {{ $exam->subject->name ?? '' }}</option>
                    @endforeach
                </select>
                @error('exam_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endif
            </div>

            <!-- Marks Obtained -->
            <div>
                <label for="marks_obtained" class="block text-sm font-medium text-gray-700">الدرجة الحاصل عليها <span class="text-red-500">*</span></label>
                <input type="number" name="marks_obtained" id="marks_obtained" value="{{ old('marks_obtained') }}" min="0" step="0.5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('marks_obtained') border-red-500 @endif" placeholder="أدخل الدرجة">
                @error('marks_obtained')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">ملاحظات</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('notes') border-red-500 @endif" placeholder="أدخل ملاحظات إضافية">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-4 py-3 bg-gray-50 text-left sm:px-6 sm:rounded-b-lg flex gap-3">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                حفظ الدرجة
            </button>
            <a href="{{ route('grades.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                إلغاء
            </a>
        </div>
    </form>
</div>
