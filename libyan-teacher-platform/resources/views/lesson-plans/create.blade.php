@extends('layout')

@section('title', 'إنشاء خطة درس')

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
                <span class="mr-2 text-gray-700 font-medium">إنشاء خطة درس</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">إنشاء خطة درس جديدة</h1>
        <p class="mt-1 text-sm text-gray-500">أدخل تفاصيل خطة الدرس</p>
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
    <form action="{{ route('lesson-plans.store') }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf

        <div class="px-4 py-5 sm:p-6 space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">عنوان الدرس <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') border-red-500 @endif" placeholder="أدخل عنوان الدرس">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject and Classroom -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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

            <!-- Date, Duration, Period -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('date') border-red-500 @endif">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700">المدة (دقيقة) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration" id="duration" value="{{ old('duration') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('duration') border-red-500 @endif" placeholder="45">
                    @error('duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700">الحصة <span class="text-red-500">*</span></label>
                    <select name="period" id="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('period') border-red-500 @endif">
                        <option value="">اختر الحصة</option>
                        <option value="1" {{ old('period') == '1' ? 'selected' : '' }}>الحصة الأولى</option>
                        <option value="2" {{ old('period') == '2' ? 'selected' : '' }}>الحصة الثانية</option>
                        <option value="3" {{ old('period') == '3' ? 'selected' : '' }}>الحصة الثالثة</option>
                        <option value="4" {{ old('period') == '4' ? 'selected' : '' }}>الحصة الرابعة</option>
                        <option value="5" {{ old('period') == '5' ? 'selected' : '' }}>الحصة الخامسة</option>
                        <option value="6" {{ old('period') == '6' ? 'selected' : '' }}>الحصة السادسة</option>
                    </select>
                    @error('period')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Objectives -->
            <div>
                <label for="objectives" class="block text-sm font-medium text-gray-700">الأهداف <span class="text-red-500">*</span></label>
                <textarea name="objectives" id="objectives" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('objectives') border-red-500 @endif" placeholder="أدخل أهداف الدرس">{{ old('objectives') }}</textarea>
                @error('objectives')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Introduction -->
            <div>
                <label for="introduction" class="block text-sm font-medium text-gray-700">التمهيد <span class="text-red-500">*</span></label>
                <textarea name="introduction" id="introduction" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('introduction') border-red-500 @endif" placeholder="أدخل تمهيد الدرس">{{ old('introduction') }}</textarea>
                @error('introduction')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endif
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">المحتوى <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('content') border-red-500 @endif" placeholder="أدخل محتوى الدرس">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Activities -->
            <div>
                <label for="activities" class="block text-sm font-medium text-gray-700">الأنشطة <span class="text-red-500">*</span></label>
                <textarea name="activities" id="activities" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('activities') border-red-500 @endif" placeholder="أدخل الأنشطة">{{ old('activities') }}</textarea>
                @error('activities')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endif
            </div>

            <!-- Assessment -->
            <div>
                <label for="assessment" class="block text-sm font-medium text-gray-700">التقييم <span class="text-red-500">*</span></label>
                <textarea name="assessment" id="assessment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('assessment') border-red-500 @endif" placeholder="أدخل طرق التقييم">{{ old('assessment') }}</textarea>
                @error('assessment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @endif
            </div>

            <!-- Homework -->
            <div>
                <label for="homework" class="block text-sm font-medium text-gray-700">الواجب المنزلي</label>
                <textarea name="homework" id="homework" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('homework') border-red-500 @endif" placeholder="أدخل الواجب المنزلي">{{ old('homework') }}</textarea>
                @error('homework')
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
                حفظ الخطة
            </button>
            <a href="{{ route('lesson-plans.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                إلغاء
            </a>
        </div>
    </form>
</div>
