
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسائل أولياء الأمور</title>
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
                <li>
                    <span class="text-gray-500">رسائل أولياء الأمور</span>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">رسائل أولياء الأمور</h1>
            <a href="{{ route('parent-messages.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out">
                <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                إرسال رسالة جديدة
            </a>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form action="{{ route('parent-messages.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث في الرسائل..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:w-48">
                    <select name="is_read" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">جميع الرسائل</option>
                        <option value="1" {{ request('is_read') == '1' ? 'selected' : '' }}>مقروءة</option>
                        <option value="0" {{ request('is_read') == '0' ? 'selected' : '' }}>غير مقروءة</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-700 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ease-in-out">بحث</button>
            </form>
        </div>

        <!-- Parent Messages Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الطالب</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الرسالة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">التاريخ</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parentMessages as $message)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out {{ !$message->is_read ? 'bg-blue-50' : '' }}">
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm font-semibold">{{ $message->student->name ?? 'غير محدد' }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <span class="text-gray-600 line-clamp-2" title="{{ $message->message }}">{{ Str::limit($message->message, 40) }}</span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    @if($message->is_read)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">مقروءة</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">غير مقروءة</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $message->created_at->format('Y-m-d') }}</td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('parent-messages.show', $message) }}" class="text-blue-600 hover:text-blue-900" title="عرض">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('parent-messages.edit', $message) }}" class="text-green-600 hover:text-green-900" title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('parent-messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="حذف">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center text-gray-500">لا توجد رسائل مسجلة حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($parentMessages->hasPages())
                <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $parentMessages->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    @endsection

</body>
</html>