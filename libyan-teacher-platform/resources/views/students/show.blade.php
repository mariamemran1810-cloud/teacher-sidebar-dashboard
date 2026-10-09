
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملف الطالب - {{ $student->name }}</title>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    @extends('layout')

    @section('content')
    <div class="container mx-auto px-4 py-8">
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
                    <span class="text-gray-500">{{ $student->name }}</span>
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

        <!-- Student Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start mb-4">
                <h1 class="text-2xl font-bold text-gray-800">ملف الطالب</h1>
                <div class="flex gap-2 mt-4 md:mt-0">
                    <a href="{{ route('students.edit', $student) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 ease-in-out">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        تعديل
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-500 text-sm">الاسم الكامل</p>
                    <p class="text-gray-800 font-semibold">{{ $student->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">البريد الإلكتروني</p>
                    <p class="text-gray-800 font-semibold" dir="ltr">{{ $student->email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">رقم الهاتف</p>
                    <p class="text-gray-800 font-semibold">{{ $student->phone }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">الجنس</p>
                    <p class="text-gray-800 font-semibold">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">تاريخ الميلاد</p>
                    <p class="text-gray-800 font-semibold">{{ $student->birth_date }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">الرقم الوطني</p>
                    <p class="text-gray-800 font-semibold">{{ $student->national_id ?? 'غير متوفر' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">العنوان</p>
                    <p class="text-gray-800 font-semibold">{{ $student->address }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">اسم ولي الأمر</p>
                    <p class="text-gray-800 font-semibold">{{ $student->parent_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">هاتف ولي الأمر</p>
                    <p class="text-gray-800 font-semibold">{{ $student->parent_phone }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">الفصل الدراسي</p>
                    <p class="text-gray-800 font-semibold">{{ $student->classroom->name ?? 'غير محدد' }}</p>
                </div>
                @if($student->notes)
                <div class="md:col-span-2 lg:col-span-3">
                    <p class="text-gray-500 text-sm">ملاحظات</p>
                    <p class="text-gray-800 font-semibold">{{ $student->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Grades Table -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">الدرجات</h2>
            @if($student->grades && $student->grades->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المادة</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">درجة الامتحان النصفي</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">درجة النهائي</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المعدل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->grades as $grade)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $grade->subject->name }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $grade->midterm_score }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $grade->final_score }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                        @php
                                            $average = ($grade->midterm_score + $grade->final_score) / 2;
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $average >= 90 ? 'bg-green-100 text-green-800' : ($average >= 75 ? 'bg-blue-100 text-blue-800' : ($average >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                            {{ number_format($average, 1) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لا توجد درجات مسجلة لهذا الطالب</p>
            @endif
        </div>

        <!-- Attendance Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ملخص الحضور</h2>
            @if($student->attendance && $student->attendance->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $student->attendance->where('status', 'present')->count() }}</p>
                        <p class="text-sm text-gray-600">أيام الحضور</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-red-600">{{ $student->attendance->where('status', 'absent')->count() }}</p>
                        <p class="text-sm text-gray-600">أيام الغياب</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-600">{{ $student->attendance->where('status', 'late')->count() }}</p>
                        <p class="text-sm text-gray-600">أيام التأخير</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600">
                            @php
                                $total = $student->attendance->count();
                                $present = $student->attendance->where('status', 'present')->count();
                                $rate = $total > 0 ? round(($present / $total) * 100) : 0;
                            @endphp
                            {{ $rate }}%
                        </p>
                        <p class="text-sm text-gray-600">نسبة الحضور</p>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لا يوجد سجل حضور لهذا الطالب</p>
            @endif
        </div>

        <!-- Payment History -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">سجل المدفوعات</h2>
            @if($student->payments && $student->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">رقم الفاتورة</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المبلغ</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">تاريخ الدفع</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">طريقة الدفع</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->payments as $payment)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $payment->invoice_number }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ number_format($payment->amount, 2) }} د.ل</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $payment->payment_date }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $payment->payment_method }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $payment->status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لا يوجد سجل مدفوعات لهذا الطالب</p>
            @endif
        </div>

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('students.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; العودة لقائمة الطلاب
            </a>
        </div>
    </div>
    @endsection

</body>
</html>
