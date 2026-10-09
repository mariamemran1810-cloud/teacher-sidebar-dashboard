@extends('layout')

@section('title', 'إنشاء امتحان')

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
                <span class="mr-2 text-gray-700 font-medium">إنشاء امتحان</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">إنشاء امتحان جديد</h1>
        <p class="mt-1 text-sm text-gray-500">أدخل تفاصيل الامتحان</p>
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
    <form action="{{ route('exams.store') }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf

        <div class="px-4 py-5 sm:p-6 space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">عنوان الامتحان <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') border-red-500 @endif" placeholder="أدخل عنوان الامتحان">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @endif" placeholder="أدخل وصف الامتحان">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endif
            </div>

            <!-- Type, Subject, Classroom -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">النوع <span class="text-red-500">*</span></label>
                    <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('type') border-red-500 @endif">
                        <option value="">اختر النوع</option>
                        <option value="monthly" {{ old('type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="midterm" {{ old('type') == 'midterm' ? 'selected' : '' }}>نصفي</option>
                        <option value="final" {{ old('type') == 'final' ? 'selected' : '' }}>نهائي</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">المادة <span class="text-red-500">*</span></label>
                    <select name="subject_id" id="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('subject_id') border-red-500 @endif">
                        <option value="">اختر المادة</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-gray-700">الفصل <span class="text-red-500">*</span></label>
                    <select name="classroom_id" id="classroom_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('classroom_id') border-red-500 @endif">
                        <option value="">اختر الفصل</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Total Marks, Duration, Exam Date -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700">الدرجات الكلية <span class="text-red-500">*</span></label>
                    <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('total_marks') border-red-500 @endif" placeholder="100">
                    @error('total_marks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">المدة (دقيقة) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('duration_minutes') border-red-500 @endif" placeholder="60">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700">تاريخ الامتحان <span class="text-red-500">*</span></label>
                    <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('exam_date') border-red-500 @endif">
                    @error('exam_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-4 py-3 bg-gray-50 text-left sm:px-6 sm:rounded-b-lg flex gap-3">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                حفظ الامتحان
            </button>
            <a href="{{ route('exams.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                إلغاء
            </a>
        </div>
    </form>
</div>
