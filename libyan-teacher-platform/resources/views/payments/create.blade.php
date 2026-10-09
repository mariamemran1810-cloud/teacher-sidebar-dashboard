
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة دفعة جديدة</title>
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
                    <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800">المدفوعات</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-400" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg>
                </li>
                <li>
                    <span class="text-gray-500">إضافة دفعة</span>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">إضافة دفعة جديدة</h1>

            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <!-- Student and Type -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">معلومات الدفعة</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="student_id" class="block text-gray-700 text-sm font-bold mb-2">الطالب <span class="text-red-500">*</span></label>
                            <select name="student_id" id="student_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('student_id') border-red-500 @endif">
                                <option value="">اختر الطالب</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">نوع الدفعة <span class="text-red-500">*</span></label>
                            <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @endif">
                                <option value="">اختر النوع</option>
                                <option value="tuition" {{ old('type') == 'tuition' ? 'selected' : '' }}>رسوم دراسية</option>
                                <option value="exam" {{ old('type') == 'exam' ? 'selected' : '' }}>رسوم امتحان</option>
                                <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>رسوم نشاط</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Amount and Dates -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">المبلغ والتواريخ</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">المبلغ <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @endif">
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="due_date" class="block text-gray-700 text-sm font-bold mb-2">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('due_date') border-red-500 @endif">
                            @error('due_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="paid_date" class="block text-gray-700 text-sm font-bold mb-2">تاريخ الدفع</label>
                            <input type="date" name="paid_date" id="paid_date" value="{{ old('paid_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('paid_date') border-red-500 @endif">
                            @error('paid_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status and Notes -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">الحالة وملاحظات</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">الحالة <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @endif">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>متأخر</option>
                            </select>
                            @error('status')
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
                    <a href="{{ route('payments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">إلغاء</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">حفظ الدفعة</button>
                </div>
            </form>
        </div>
    </div>
    @endsection

</body>
</html>