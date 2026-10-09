
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المكتبة الرقمية - منصة المعلم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Breadcrumbs -->
    <nav class="bg-white shadow-sm border-b" aria-label="breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition">لوحة التحكم</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600 font-medium" aria-current="page">المكتبة الرقمية</li>
            </ol>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3" role="alert">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3" role="alert">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <p class="text-red-700 text-sm font-medium">{{ session('error') }}</p>
        </div>
        @endif

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">المكتبة الرقمية</h1>
                <p class="text-sm text-gray-500 mt-1">تصفح وحمّل المواد التعليمية أو ارفع محتوى جديداً</p>
            </div>
            <button onclick="openUploadModal()" class="bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-5 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                رفع ملف جديد
            </button>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
            <form action="{{ route('library.index') }}" method="GET" class="space-y-4">
                @csrf

                @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                        <li class="text-red-600 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                        <div class="relative">
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="ابحث في المكتبة..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            >
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                        <select id="type" name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">جميع الأنواع</option>
                            <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>كتاب</option>
                            <option value="worksheet" {{ request('type') == 'worksheet' ? 'selected' : '' }}>ورقة عمل</option>
                            <option value="presentation" {{ request('type') == 'presentation' ? 'selected' : '' }}>عرض تقديمي</option>
                            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>فيديو</option>
                            <option value="activity" {{ request('type') == 'activity' ? 'selected' : '' }}>نشاط</option>
                            <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>اختبار</option>
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">المادة</label>
                        <select id="subject" name="subject" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">جميع المواد</option>
                            <option value="math" {{ request('subject') == 'math' ? 'selected' : '' }}>الرياضيات</option>
                            <option value="arabic" {{ request('subject') == 'arabic' ? 'selected' : '' }}>اللغة العربية</option>
                            <option value="english" {{ request('subject') == 'english' ? 'selected' : '' }}>اللغة الإنجليزية</option>
                            <option value="science" {{ request('subject') == 'science' ? 'selected' : '' }}>العلوم</option>
                            <option value="social" {{ request('subject') == 'social' ? 'selected' : '' }}>الدراسات الاجتماعية</option>
                            <option value="islamic" {{ request('subject') == 'islamic' ? 'selected' : '' }}>التربية الإسلامية</option>
                            <option value="computer" {{ request('subject') == 'computer' ? 'selected' : '' }}>الحاسب الآلي</option>
                        </select>
                    </div>

                    <!-- Grade Filter -->
                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700 mb-1">الصف</label>
                        <select id="grade" name="grade" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">جميع الصفوف</option>
                            <option value="1" {{ request('grade') == '1' ? 'selected' : '' }}>الصف الأول</option>
                            <option value="2" {{ request('grade') == '2' ? 'selected' : '' }}>الصف الثاني</option>
                            <option value="3" {{ request('grade') == '3' ? 'selected' : '' }}>الصف الثالث</option>
                            <option value="4" {{ request('grade') == '4' ? 'selected' : '' }}>الصف الرابع</option>
                            <option value="5" {{ request('grade') == '5' ? 'selected' : '' }}>الصف الخامس</option>
                            <option value="6" {{ request('grade') == '6' ? 'selected' : '' }}>الصف السادس</option>
                            <option value="7" {{ request('grade') == '7' ? 'selected' : '' }}>الصف السابع</option>
                            <option value="8" {{ request('grade') == '8' ? 'selected' : '' }}>الصف الثامن</option>
                            <option value="9" {{ request('grade') == '9' ? 'selected' : '' }}>الصف التاسع</option>
                            <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>الصف العاشر</option>
                            <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>الصف الحادي عشر</option>
                            <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>الصف الثاني عشر</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        تصفية
                    </button>
                    <a href="{{ route('library.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <!-- Library Items Grid -->
        @if(isset($items) && $items->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $item)
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition overflow-hidden group">
                <!-- Item Header with Icon -->
                <div class="p-4 pb-3">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center
                            @if($item->type == 'book') bg-blue-100 text-blue-600
                            @elseif($item->type == 'worksheet') bg-green-100 text-green-600
                            @elseif($item->type == 'presentation') bg-yellow-100 text-yellow-600
                            @elseif($item->type == 'video') bg-red-100 text-red-600
                            @elseif($item->type == 'activity') bg-purple-100 text-purple-600
                            @elseif($item->type == 'exam') bg-orange-100 text-orange-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($item->type == 'book')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @elseif($item->type == 'worksheet')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @elseif($item->type == 'presentation')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                            @elseif($item->type == 'video')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            @elseif($item->type == 'activity')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($item->type == 'exam')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            @if($item->type == 'book') bg-blue-50 text-blue-700
                            @elseif($item->type == 'worksheet') bg-green-50 text-green-700
                            @elseif($item->type == 'presentation') bg-yellow-50 text-yellow-700
                            @elseif($item->type == 'video') bg-red-50 text-red-700
                            @elseif($item->type == 'activity') bg-purple-50 text-purple-700
                            @elseif($item->type == 'exam') bg-orange-50 text-orange-700
                            @else bg-gray-50 text-gray-700
                            @endif">
                            @if($item->type == 'book') كتاب
                            @elseif($item->type == 'worksheet') ورقة عمل
                            @elseif($item->type == 'presentation') عرض تقديمي
                            @elseif($item->type == 'video') فيديو
                            @elseif($item->type == 'activity') نشاط
                            @elseif($item->type == 'exam') اختبار
                            @else آخر
                            @endif
                        </span>
                    </div>

                    <h3 class="font-semibold text-gray-800 text-sm mb-1 line-clamp-2">{{ $item->title }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $item->description }}</p>

                    <!-- Meta Info -->
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            {{ $item->subject }}
                        </span>
                        <span>•</span>
                        <span>صف {{ $item->grade }}</span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ $item->downloads }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('library.download', $item->id) }}" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-lg text-xs font-medium transition flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            تحميل
                        </a>
                        <button onclick="viewDetails({{ $item->id }})" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition" title="عرض التفاصيل">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button onclick="deleteItem({{ $item->id }})" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="حذف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(isset($items) && $items->hasPages())
        <div class="mt-6">
            {{ $items->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">لا توجد عناصر</h3>
            <p class="text-sm text-gray-400 mb-4">لم يتم العثور على أي عناصر تطابق معايير البحث</p>
            <button onclick="openUploadModal()" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 rounded-lg text-sm font-medium transition">
                ارفع أول ملف
            </button>
        </div>
        @endif

    </div>

    <!-- Upload Modal -->
    <div id="upload-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">رفع ملف جديد</h2>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('library.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">عنوان الملف</label>
                        <input type="text" id="title" name="title" placeholder="أدخل عنوان الملف" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                        @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                        <textarea id="description" name="description" rows="2" placeholder="وصف مختصر للملف" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"></textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="upload_type" class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                            <select id="upload_type" name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                                <option value="">اختر النوع</option>
                                <option value="book">كتاب</option>
                                <option value="worksheet">ورقة عمل</option>
                                <option value="presentation">عرض تقديمي</option>
                                <option value="video">فيديو</option>
                                <option value="activity">نشاط</option>
                                <option value="exam">اختبار</option>
                            </select>
                            @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="upload_subject" class="block text-sm font-medium text-gray-700 mb-1">المادة</label>
                            <select id="upload_subject" name="subject" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                                <option value="">اختر المادة</option>
                                <option value="math">الرياضيات</option>
                                <option value="arabic">اللغة العربية</option>
                                <option value="english">اللغة الإنجليزية</option>
                                <option value="science">العلوم</option>
                                <option value="social">الدراسات الاجتماعية</option>
                                <option value="islamic">التربية الإسلامية</option>
                                <option value="computer">الحاسب الآلي</option>
                            </select>
                            @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="upload_grade" class="block text-sm font-medium text-gray-700 mb-1">الصف</label>
                        <select id="upload_grade" name="grade" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                            <option value="">اختر الصف</option>
                            <option value="1">الصف الأول</option>
                            <option value="2">الصف الثاني</option>
                            <option value="3">الصف الثالث</option>
                            <option value="4">الصف الرابع</option>
                            <option value="5">الصف الخامس</option>
                            <option value="6">الصف السادس</option>
                            <option value="7">الصف السابع</option>
                            <option value="8">الصف الثامن</option>
                            <option value="9">الصف التاسع</option>
                            <option value="10">الصف العاشر</option>
                            <option value="11">الصف الحادي عشر</option>
                            <option value="12">الصف الثاني عشر</option>
                        </select>
                        @error('grade')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">الملف</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer" onclick="document.getElementById('file').click()">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-sm text-gray-600">اضغط لاختيار ملف</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, DOC, PPT, MP4 (حد أقصى 50 ميجابايت)</p>
                        </div>
                        <input type="file" id="file" name="file" class="hidden" accept=".pdf,.doc,.docx,.ppt,.pptx,.mp4,.jpg,.png" required onchange="updateFileName(this)">
                        <p id="file-name" class="mt-2 text-sm text-blue-600"></p>
                        @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg text-sm font-medium transition">
                            رفع الملف
                        </button>
                        <button type="button" onclick="closeUploadModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 px-4 rounded-lg text-sm font-medium transition">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('upload-modal').classList.remove('hidden');
            document.getElementById('upload-modal').classList.add('flex');
        }

        function closeUploadModal() {
            document.getElementById('upload-modal').classList.add('hidden');
            document.getElementById('upload-modal').classList.remove('flex');
        }

        function updateFileName(input) {
            const fileName = input.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName ? 'الملف المختار: ' + fileName : '';
        }

        function viewDetails(id) {
            window.location.href = '/library/' + id;
        }

        function deleteItem(id) {
            if (confirm('هل أنت متأكد من حذف هذا العنصر؟')) {
                fetch('/library/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => window.location.reload());
            }
        }

        // Close modal on outside click
        document.getElementById('upload-modal').addEventListener('click', function(e) {
            if (e.target === this) closeUploadModal();
        });
    </script>

</body>
</html>
