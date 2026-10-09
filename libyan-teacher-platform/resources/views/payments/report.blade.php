
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المدفوعات</title>
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
                    <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800">المدفوعات</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-400" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg>
                </li>
                <li>
                    <span class="text-gray-500">تقرير المدفوعات</span>
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

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">تقرير المدفوعات</h1>
            <a href="{{ route('payments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out">
                <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                رجوع للمدفوعات
            </a>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form action="{{ route('payments.report') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">من تاريخ</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">عرض التقرير</button>
                <a href="{{ route('payments.export', request()->all()) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    تصدير
                </a>
            </form>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                <p class="text-gray-500 text-sm">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($grandTotal, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
                <p class="text-gray-500 text-sm">إجمالي المحصل</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalCollected, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500">
                <p class="text-gray-500 text-sm">إجمالي قيد الانتظار</p>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalPending, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                <p class="text-gray-500 text-sm">إجمالي المتأخر</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($totalOverdue, 2) }}</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Status Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">المدفوعات حسب الحالة</h2>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-green-600 font-semibold">مدفوع</span>
                            <span class="text-gray-600">{{ $byStatus['paid']['count'] }} دفعة - {{ number_format($byStatus['paid']['amount'], 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: {{ $byStatus['paid']['percentage'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-yellow-600 font-semibold">قيد الانتظار</span>
                            <span class="text-gray-600">{{ $byStatus['pending']['count'] }} دفعة - {{ number_format($byStatus['pending']['amount'], 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-500 h-3 rounded-full" style="width: {{ $byStatus['pending']['percentage'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-red-600 font-semibold">متأخر</span>
                            <span class="text-gray-600">{{ $byStatus['overdue']['count'] }} دفعة - {{ number_format($byStatus['overdue']['amount'], 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-red-500 h-3 rounded-full" style="width: {{ $byStatus['overdue']['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Type Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">المدفوعات حسب النوع</h2>
                <div class="space-y-4">
                    @foreach($byType as $type => $data)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-semibold text-gray-700">{{ $data['label'] }}</span>
                                <span class="text-gray-600">{{ $data['count'] }} دفعة - {{ number_format($data['amount'], 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown Chart Placeholder -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">التحليل الشهري</h2>
            <div class="h-64 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <p class="text-gray-500 text-sm">رسم بياني للتحليل الشهري</p>
                    <p class="text-gray-400 text-xs mt-1">يمكن إضافة مكتبة رسوم بيانية مثل Chart.js هنا</p>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-700">تفصيل شهري</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الشهر</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">عدد المدفوعات</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المحصل</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">قيد الانتظار</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المتأخر</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyBreakdown as $month)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm font-semibold">{{ $month['label'] }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $month['count'] }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-green-600 font-semibold">{{ number_format($month['collected'], 2) }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-yellow-600 font-semibold">{{ number_format($month['pending'], 2) }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-red-600 font-semibold">{{ number_format($month['overdue'], 2) }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm font-bold">{{ number_format($month['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center text-gray-500">لا توجد بيانات للعرض</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endsection

</body>
</html>