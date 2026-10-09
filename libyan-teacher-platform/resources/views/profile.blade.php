@extends('layout')

@section('title', 'الملف الشخصي - منصة المعلم الليبي')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="text-sm">
        <ol class="list-none p-0 inline-flex items-center gap-2">
            <li><a href="{{ url('/') }}" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-500">الملف الشخصي</span></li>
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

    <!-- Profile Header -->
    <div class="bg-gradient-to-l from-green-600 to-green-700 rounded-2xl p-6 text-white">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center">
                <span class="text-4xl font-bold text-white">{{ substr(Auth::user()->name ?? 'أ', 0, 1) }}</span>
            </div>
            <div class="text-center md:text-right">
                <h1 class="text-2xl font-bold">{{ Auth::user()->name ?? 'أحمد محمد' }}</h1>
                <p class="text-green-100">{{ Auth::user()->email ?? 'ahmed@example.com' }}</p>
                <p class="text-green-200 text-sm mt-1">معلم الرياضيات</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">المعلومات الشخصية</h2>
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">التخصص</label>
                            <input type="text" name="specialization" value="{{ old('specialization', Auth::user()->specialization ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('specialization')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نبذة</label>
                        <textarea name="bio" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 resize-none">{{ old('bio', Auth::user()->bio ?? '') }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @endforeach
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">حفظ التغييرات</button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">تغيير كلمة المرور</h2>
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الحالية</label>
                        <input type="password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الجديدة</label>
                            <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg transition">تغيير كلمة المرور</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">إحصائيات الحساب</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-gray-600">الدروس المكتملة</span>
                        <span class="text-xl font-bold text-green-600">24</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="text-sm text-gray-600">سنوات الخبرة</span>
                        <span class="text-xl font-bold text-yellow-600">8</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-gray-600">الطلاب</span>
                        <span class="text-xl font-bold text-blue-600">156</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-gray-600">التقييمات</span>
                        <span class="text-xl font-bold text-purple-600">4.8</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-4">آخر النشاطات</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">أكملت دورة التربية الحديثة</p>
                            <p class="text-xs text-gray-500">منذ يومين</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">أضفت خطة درس جديدة</p>
                            <p class="text-xs text-gray-500">منذ 3 أيام</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">شاركت في نقاش المجتمع</p>
                            <p class="text-xs text-gray-500">منذ أسبوع</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
