@extends('layout')

@section('title', 'الجدول الدراسي - منصة المعلم الليبي')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="text-sm">
        <ol class="list-none p-0 inline-flex items-center gap-2">
            <li><a href="{{ url('/') }}" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-500">الجدول الدراسي</span></li>
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
        <h1 class="text-2xl font-bold text-gray-800">الجدول الدراسي الأسبوعي</h1>
        <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
            + إضافة عنصر جديد
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-4">
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">جميع المعلمين</option>
                <option value="1">أحمد محمد</option>
                <option value="2">فاطمة علي</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">جميع الفصول</option>
                <option value="1">الصف العاشر</option>
                <option value="2">الصف الحادي عشر</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">جميع المواد</option>
                <option value="1">الرياضيات</option>
                <option value="2">اللغة العربية</option>
            </select>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg transition">تصفية</button>
        </div>
    </div>

    <!-- Weekly Schedule Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-green-600 text-white">
                        <th class="px-4 py-3 text-right text-sm font-semibold">الحصة</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">الأحد</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">الإثنين</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">الثلاثاء</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">الأربعاء</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">الخميس</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @for($period = 1; $period <= 6; $period++)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm font-bold text-gray-700 bg-gray-50">الحصة {{ $period }}</td>
                            @foreach(['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'] as $day)
                                <td class="px-4 py-3 text-sm">
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-2 text-center">
                                        <p class="font-medium text-green-800 text-xs">الرياضيات</p>
                                        <p class="text-xs text-gray-500">أ. أحمد</p>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Schedule Legend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 bg-green-100 border border-green-300 rounded"></span>
                <span class="text-gray-600">حصة عادية</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded"></span>
                <span class="text-gray-600">امتحان</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 bg-blue-100 border border-blue-300 rounded"></span>
                <span class="text-gray-600">نشاط إضافي</span>
            </div>
        </div>
    </div>
</div>
@endsection
