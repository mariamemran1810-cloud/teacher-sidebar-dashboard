@extends('layout')

@section('title', 'إدارة المدرسة - منصة المعلم الليبي')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="text-sm">
        <ol class="list-none p-0 inline-flex items-center gap-2">
            <li><a href="{{ url('/') }}" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-500">إدارة المدرسة</span></li>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- School Info Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">معلومات المدرسة</h2>
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم المدرسة</label>
                            <input type="text" name="name" value="{{ old('name', $school->name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', $school->phone ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ old('email', $school->email ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                            <input type="text" name="address" value="{{ old('address', $school->address ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 resize-none">{{ old('description', $school->description ?? '') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">إحصائيات سريعة</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-gray-600">الطلاب</span>
                        <span class="text-xl font-bold text-green-600">1,248</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm text-gray-600">المعلمون</span>
                        <span class="text-xl font-bold text-yellow-600">86</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-gray-600">الفصول</span>
                        <span class="text-xl font-bold text-blue-600">32</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-gray-600">المواد</span>
                        <span class="text-xl font-bold text-purple-600">24</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">آخر النشاطات</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">تم تسجيل طالب جديد</p>
                            <p class="text-xs text-gray-500">منذ 5 دقائق</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">تم إنشاء امتحان جديد</p>
                            <p class="text-xs text-gray-500">منذ 15 دقيقة</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">تم إضافة مادة جديدة</p>
                            <p class="text-xs text-gray-500">منذ ساعة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
