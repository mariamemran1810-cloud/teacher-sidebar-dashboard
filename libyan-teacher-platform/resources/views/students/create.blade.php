
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة طالب جديد</title>
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
                    <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800">الطلاب</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-400" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg>
                </li>
                <li>
                    <span class="text-gray-500">إضافة طالب</span>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">إضافة طالب جديد</h1>

            <form action="{{ route('students.store') }}" method="POST">
                @csrf

                <!-- Personal Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">المعلومات الشخصية</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @endif" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @endif" dir="ltr">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @endif">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">الجنس <span class="text-red-500">*</span></label>
                            <select name="gender" id="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @endif">
                                <option value="">اختر الجنس</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="birth_date" class="block text-gray-700 text-sm font-bold mb-2">تاريخ الميلاد <span class="text-red-500">*</span></label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('birth_date') border-red-500 @endif">
                            @error('birth_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="national_id" class="block text-gray-700 text-sm font-bold mb-2">الرقم الوطني</label>
                            <input type="text" name="national_id" id="national_id" value="{{ old('national_id') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('national_id') border-red-500 @endif">
                            @error('national_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">العنوان</h2>
                    <div>
                        <label for="address" class="block text-gray-700 text-sm font-bold mb-2">العنوان <span class="text-red-500">*</span></label>
                        <textarea name="address" id="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @endif">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Parent Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">معلومات ولي الأمر</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="parent_name" class="block text-gray-700 text-sm font-bold mb-2">اسم ولي الأمر <span class="text-red-500">*</span></label>
                            <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('parent_name') border-red-500 @endif">
                            @error('parent_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="parent_phone" class="block text-gray-700 text-sm font-bold mb-2">هاتف ولي الأمر <span class="text-red-500">*</span></label>
                            <input type="tel" name="parent_phone" id="parent_phone" value="{{ old('parent_phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('parent_phone') border-red-500 @endif">
                            @error('parent_phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Classroom and Notes -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">المعلومات الأكاديمية</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="classroom_id" class="block text-gray-700 text-sm font-bold mb-2">الفصل الدراسي <span class="text-red-500">*</span></label>
                            <select name="classroom_id" id="classroom_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('classroom_id') border-red-500 @endif">
                                <option value="">اختر الفصل</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                                @endforeach
                            </select>
                            @error('classroom_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">ملاحظات</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @endif">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">إلغاء</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">حفظ الطالب</button>
                </div>
            </form>
        </div>
    </div>
    @endsection

</body>
</html>
