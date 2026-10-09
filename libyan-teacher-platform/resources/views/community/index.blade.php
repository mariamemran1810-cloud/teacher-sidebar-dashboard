@extends('layout')

@section('title', 'المجتمع - منصة المعلم الليبي')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="text-sm">
        <ol class="list-none p-0 inline-flex items-center gap-2">
            <li><a href="{{ url('/') }}" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-500">المجتمع</span></li>
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
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Create Post -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">إنشاء منشور جديد</h3>
                <form action="#" method="POST">
                    @csrf
                    <textarea name="content" rows="3" placeholder="ما الذي تريد مشاركته مع المجتمع؟" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">نشر</button>
                    </div>
                </form>
            </div>

            <!-- Posts List -->
            <div class="space-y-4">
                @forelse($posts ?? [] as $post)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-bold">{{ substr($post->user->name ?? 'أ', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $post->user->name ?? 'أحمد محمد' }}</p>
                                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() ?? 'منذ ساعة' }}</p>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-4">{{ $post->content ?? 'تجربة رائعة في استخدام التكنولوجيا في التعليم. شكراً لكل من شارك في هذه المناقشة.' }}</p>
                        <div class="flex items-center gap-4 text-sm">
                            <button class="flex items-center gap-1 text-gray-500 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <span>{{ $post->likes_count ?? 12 }}</span>
                            </button>
                            <button class="flex items-center gap-1 text-gray-500 hover:text-green-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>{{ $post->comments_count ?? 5 }}</span>
                            </button>
                        </div>
                        <!-- Comments Section -->
                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                            @foreach($post->comments ?? [] as $comment)
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-yellow-600 text-sm font-bold">{{ substr($comment->user->name ?? 'م', 0, 1) }}</span>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg px-3 py-2 flex-1">
                                        <p class="text-sm font-bold text-gray-800">{{ $comment->user->name ?? 'محمد علي' }}</p>
                                        <p class="text-sm text-gray-600">{{ $comment->content ?? 'تعليق رائع!' }}</p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex gap-3">
                                <input type="text" placeholder="أضف تعليقاً..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">إرسال</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                        <p>لا توجد منشورات بعد. كن أول من يشارك!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Trending Topics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">المواضيع الرائجة</h3>
                <div class="space-y-3">
                    <a href="#" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                        <span class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600 font-bold text-sm">#</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">التعليم_النشط</p>
                            <p class="text-xs text-gray-500">128 منشور</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                        <span class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-sm">#</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">التكنولوجيا_التعليمية</p>
                            <p class="text-xs text-gray-500">95 منشور</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">#</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">تطوير_المناهج</p>
                            <p class="text-xs text-gray-500">67 منشور</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Community Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">إحصائيات المجتمع</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">1,248</p>
                        <p class="text-xs text-gray-500">عضو</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">3,456</p>
                        <p class="text-xs text-gray-500">منشور</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
