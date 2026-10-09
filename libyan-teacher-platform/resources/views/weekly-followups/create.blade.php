
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة متابعة أسبوعية</title>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    @extends('layout')

    @section('content')
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <!-- Breadcrumbs -->
        <nav class="text-sm mb-6">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800">الرئيسية</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-400" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('weekly-followups.index') }}" class="text-blue-600 hover:text-blue-800">المتابعات الأسبوعية</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-400" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg>
                </li>
                <li>
                    <span class="text-gray-500">إضافة متابعة</span>
                </li>
            </ol>
        </nav>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">نجاح!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">خطأ!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">يرجى تصحيح الأخطاء التالية:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">إضافة متابعة أسبوعية</h1>

            <form action="{{ route('weekly-followups.store') }}" method="POST">
                @csrf

                <!-- Week Info -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">معلومات الأسبوع</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="week_number" class="block text-gray-700 text-sm font-bold mb-2">رقم الأسبوع <span class="text-red-500">*</span></label>
                            <input type="number" name="week_number" id="week_number" value="{{ old('week_number') }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('week_number') border-red-500 @endif" required>
                            @error('week_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="week_start" class="block text-gray-700 text-sm font-bold mb-2">تاريخ البداية <span class="text-red-500">*</span></label>
                            <input type="date" name="week_start" id="week_start" value="{{ old('week_start') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('week_start') border-red-500 @endif" required>
                            @error('week_start')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="week_end" class="block text-gray-700 text-sm font-bold mb-2">تاريخ النهاية <span class="text-red-500">*</span></label>
                            <input type="date" name="week_end" id="week_end" value="{{ old('week_end') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('week_end') border-red-500 @endif" required>
                            @error('week_end')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Classroom and Subject -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">الفصل والمادة</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="classroom_id" class="block text-gray-700 text-sm font-bold mb-2">الفصل الدراسي <span class="text-red-500">*</span></label>
                            <select name="classroom_id" id="classroom_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('classroom_id') border-red-500 @endif" required>
                                <option value="">اختر الفصل</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                                @endforeach
                            </select>
                            @error('classroom_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="subject_id" class="block text-gray-700 text-sm font-bold mb-2">المادة الدراسية <span class="text-red-500">*</span></label>
                            <select name="subject_id" id="subject_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject_id') border-red-500 @endif" required>
                                <option value="">اختر المادة</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Topics and Activities -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">المحتوى والأنشطة</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="topics_covered" class="block text-gray-700 text-sm font-bold mb-2">الموضوعات المغطاة <span class="text-red-500">*</span></label>
                            <textarea name="topics_covered" id="topics_covered" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('topics_covered') border-red-500 @endif" required>{{ old('topics_covered') }}</textarea>
                            @error('topics_covered')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="activities_done" class="block text-gray-700 text-sm font-bold mb-2">الأنشطة المنفذة <span class="text-red-500">*</span></label>
                            <textarea name="activities_done" id="activities_done" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('activities_done') border-red-500 @endif" required>{{ old('activities_done') }}</textarea>
                            @error('activities_done')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Challenges and Next Plan -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">التحديات والتخطيط</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="challenges" class="block text-gray-700 text-sm font-bold mb-2">التحديات <span class="text-red-500">*</span></label>
                            <textarea name="challenges" id="challenges" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('challenges') border-red-500 @endif" required>{{ old('challenges') }}</textarea>
                            @error('challenges')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="next_week_plan" class="block text-gray-700 text-sm font-bold mb-2">خطة الأسبوع القادم <span class="text-red-500">*</span></label>
                            <textarea name="next_week_plan" id="next_week_plan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('next_week_plan') border-red-500 @endif" required>{{ old('next_week_plan') }}</textarea>
                            @error('next_week_plan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('weekly-followups.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">إلغاء</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">حفظ المتابعة</button>
                </div>
            </form>
        </div>
    </div>
    @endsection

</body>
</html>