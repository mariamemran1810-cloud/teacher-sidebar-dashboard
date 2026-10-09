@extends('layout')

@section('title', 'الإشعارات - منصة المعلم الليبي')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="text-sm">
        <ol class="list-none p-0 inline-flex items-center gap-2">
            <li><a href="{{ url('/') }}" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-500">الإشعارات</span></li>
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
        <h1 class="text-2xl font-bold text-gray-800">الإشعارات</h1>
        <div class="flex gap-2">
            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">تحديد الكل كمقروء</button>
            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">حذف الكل</button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-2">
        <div class="flex flex-wrap gap-2">
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium transition">الكل</button>
            <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition">غير المقروءة</button>
            <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition">النظام</button>
            <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition">الرسائل</button>
            <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition">التنبيهات</button>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications ?? [] as $notification)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start gap-4 {{ $notification->read_at ? '' : 'border-r-4 border-r-green-500' }}">
                <div class="w-10 h-10 rounded-full {{ $notification->read_at ? 'bg-gray-100' : 'bg-green-100' }} flex items-center justify-center flex-shrink-0">
                    @if($notification->type === 'message')
                        <svg class="w-5 h-5 {{ $notification->read_at ? 'text-gray-400' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    @elseif($notification->type === 'alert')
                        <svg class="w-5 h-5 {{ $notification->read_at ? 'text-gray-400' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5 {{ $notification->read_at ? 'text-gray-400' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-bold text-gray-800 {{ $notification->read_at ? '' : 'text-green-700' }}">{{ $notification->title ?? 'عنوان الإشعار' }}</p>
                        @if(!$notification->read_at)
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message ?? 'هذا هو نص الإشعار الذي يظهر للمستخدم.' }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() ?? 'منذ 5 دقائق' }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    @if(!$notification->read_at)
                        <button class="text-green-600 hover:text-green-800 text-sm font-medium transition">تحديد كمقروء</button>
                    @endif
                    <button class="text-red-600 hover:text-red-800 text-sm font-medium transition">حذف</button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <p class="text-lg font-medium">لا توجد إشعارات</p>
                <p class="text-sm mt-1">ستظهر الإشعارات الجديدة هنا</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($notifications) && $notifications->hasPages())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
