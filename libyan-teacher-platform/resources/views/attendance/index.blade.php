@extends('layout')

@section('title', 'الحضور')

@section('content')
<div class="space-y-6">
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
                <span class="mr-2 text-gray-700 font-medium">الحضور</span>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إدارة الحضور</h1>
            <p class="mt-1 text-sm text-gray-500">تسجيل ومتابعة حضور الطلاب</p>
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

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md flex items-center">
            <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow sm:rounded-lg p-4">
        <form action="{{ route('attendance.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                <input type="date" name="date" id="date" value="{{ request('date', date('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex-1">
                <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">الفصل</label>
                <select name="classroom_id" id="classroom_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">جميع الفصول</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    عرض
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white shadow sm:rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $students->where('attendance_status', 'present')->count() }}</div>
            <div class="text-sm text-gray-500">حاضر</div>
        </div>
        <div class="bg-white shadow sm:rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $students->where('attendance_status', 'absent')->count() }}</div>
            <div class="text-sm text-gray-500">غائب</div>
        </div>
        <div class="bg-white shadow sm:rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $students->where('attendance_status', 'late')->count() }}</div>
            <div class="text-sm text-gray-500">متأخر</div>
        </div>
        <div class="bg-white shadow sm:rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $students->where('attendance_status', 'excused')->count() }}</div>
            <div class="text-sm text-gray-500">عذر</div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="{{ route('attendance.store') }}" method="POST" class="bg-white shadow sm:rounded-lg overflow-hidden">
        @csrf
        <input type="hidden" name="date" value="{{ request('date', date('Y-m-d')) }}">
        <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">

        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">تسجيل الحضور</h3>
            <p class="mt-1 text-sm text-gray-500">حدد حالة الحضور لكل طالب</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم الطالب</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفصل</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">حاضر</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">غائب</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">متأخر</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">عذر</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student->classroom->name ?? 'غير محدد' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio" name="attendance[{{ $student->id }}]" value="present" {{ ($student->attendance_status ?? 'present') == 'present' ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio" name="attendance[{{ $student->id }}]" value="absent" {{ ($student->attendance_status ?? '') == 'absent' ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio" name="attendance[{{ $student->id }}]" value="late" {{ ($student->attendance_status ?? '') == 'late' ? 'checked' : '' }} class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="radio" name="attendance[{{ $student->id }}]" value="excused" {{ ($student->attendance_status ?? '') == 'excused' ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">لا يوجد طلاب</h3>
                                <p class="mt-1 text-sm text-gray-500">لم يتم العثور على طلاب في هذا الفصل.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->count() > 0)
            <div class="px-4 py-3 bg-gray-50 text-left sm:px-6 sm:rounded-b-lg flex gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    حفظ الحضور
                </button>
                <button type="button" onclick="setAll('present')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    تحديد الكل كحاضر
                </button>
                <button type="button" onclick="setAll('absent')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    تحديد الكل كغائب
                </button>
            </div>
        @endif
    </form>
</div>

<script>
    function setAll(status) {
        document.querySelectorAll('input[type="radio"][value="' + status + '"]').forEach(radio => {
            radio.checked = true;
        });
    }
</script>
