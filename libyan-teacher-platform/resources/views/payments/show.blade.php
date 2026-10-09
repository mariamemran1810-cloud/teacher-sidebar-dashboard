
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الدفعة</title>
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
                    <span class="text-gray-500">تفاصيل الدفعة</span>
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

        <!-- Payment Details Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">تفاصيل الدفعة #{{ $payment->id }}</h1>
                @if($payment->status == 'paid')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">مدفوع</span>
                @elseif($payment->status == 'pending')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">قيد الانتظار</span>
                @elseif($payment->status == 'overdue')
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">متأخر</span>
                @endif
            </div>

            <!-- Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">الطالب</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $payment->student->name ?? 'غير محدد' }}</p>
                    </div>

                    <!-- Type -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">نوع الدفعة</p>
                        <p class="text-lg font-semibold text-gray-800">
                            @switch($payment->type)
                                @case('tuition') رسوم دراسية @break
                                @case('exam') رسوم امتحان @break
                                @case('activity') رسوم نشاط @break
                                @default أخرى
                            @endswitch
                        </p>
                    </div>

                    <!-- Amount -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">المبلغ</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($payment->amount, 2) }}</p>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">الحالة</p>
                        @if($payment->status == 'paid')
                            <p class="text-lg font-semibold text-green-600">مدفوع</p>
                        @elseif($payment->status == 'pending')
                            <p class="text-lg font-semibold text-yellow-600">قيد الانتظار</p>
                        @elseif($payment->status == 'overdue')
                            <p class="text-lg font-semibold text-red-600">متأخر</p>
                        @endif
                    </div>

                    <!-- Due Date -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">تاريخ الاستحقاق</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $payment->due_date }}</p>
                    </div>

                    <!-- Paid Date -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">تاريخ الدفع</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $payment->paid_date ?? '—' }}</p>
                    </div>
                </div>

                <!-- Notes -->
                @if($payment->notes)
                    <div class="mt-6 bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">ملاحظات</p>
                        <p class="text-gray-800">{{ $payment->notes }}</p>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <p>تاريخ الإنشاء: {{ $payment->created_at->format('Y-m-d H:i') }}</p>
                        <p>آخر تحديث: {{ $payment->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('payments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    رجوع
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('payments.edit', $payment) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        تعديل
                    </a>
                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الدفعة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection

</body>
</html>