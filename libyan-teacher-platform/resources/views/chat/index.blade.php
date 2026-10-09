
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المحادثة - منصة المعلم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; }
        .chat-container { max-height: 500px; }
        .chat-message { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .typing-indicator span { animation: blink 1.4s infinite; }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes blink { 0%, 80%, 100% { opacity: 0; } 40% { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Breadcrumbs -->
    <nav class="bg-white shadow-sm border-b" aria-label="breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition">لوحة التحكم</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600 font-medium" aria-current="page">المحادثة</li>
            </ol>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

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

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-gradient-to-l from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-lg">المحادثة</h1>
                        <p class="text-blue-100 text-xs">تواصل مع الطلاب والمعلمين الآخرين</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                    <span class="text-white/80 text-sm">متصل</span>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="chat-container overflow-y-auto p-6 space-y-4 bg-gray-50" id="chat-messages">

                <!-- Received Message (Student) -->
                <div class="chat-message flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-800 text-sm">أحمد محمد</span>
                            <span class="text-xs text-gray-400">طالب</span>
                            <span class="text-xs text-gray-400">10:30 ص</span>
                        </div>
                        <div class="bg-white rounded-2xl rounded-tr-none p-4 shadow-sm border border-gray-100">
                            <p class="text-gray-700 text-sm leading-relaxed">أستاذ، هل يمكنك شرح درس الكسور بشكل مبسط؟ لم أفهم الجزء الخاص بالجمع والطرح.</p>
                        </div>
                    </div>
                </div>

                <!-- Sent Message (Teacher) -->
                <div class="chat-message flex items-start gap-3 flex-row-reverse">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/><path d="M3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-row-reverse">
                            <span class="font-semibold text-gray-800 text-sm">أنت</span>
                            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">معلم</span>
                            <span class="text-xs text-gray-400">10:32 ص</span>
                        </div>
                        <div class="bg-blue-600 text-white rounded-2xl rounded-tl-none p-4 shadow-sm">
                            <p class="text-sm leading-relaxed">بالتأكيد يا أحمد! الكسور بسيطة جداً. عند الجمع والطرح، نتأكد أولاً من أن المقامات متساوية. إذا لم تكن كذلك، نوجد المضاعف المشترك الأصغر. هل تريد أن أعطيك أمثلة محلولة؟</p>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div id="typing-indicator" class="hidden items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="bg-white rounded-2xl rounded-tr-none p-4 shadow-sm border border-gray-100">
                        <div class="typing-indicator flex gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Message Input -->
            <form action="{{ route('chat.send') }}" method="POST" class="border-t bg-white p-4">
                @csrf

                @if($errors->any())
                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                        <li class="text-red-600 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <label for="message" class="sr-only">اكتب رسالتك</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="2"
                            placeholder="اكتب رسالتك هنا..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition"
                            required
                        >{{ old('message') }}</textarea>
                    </div>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl transition flex items-center justify-center shadow-sm hover:shadow-md"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span class="sr-only">إرسال</span>
                    </button>
                </div>

                <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                    <label class="flex items-center gap-1 cursor-pointer hover:text-gray-700">
                        <input type="file" name="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        إرفاق ملف
                    </label>
                    <span class="text-gray-300">|</span>
                    <span>اضغط Enter للإرسال</span>
                </div>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="#" class="bg-white rounded-lg p-3 text-center shadow-sm border hover:shadow-md transition">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-1">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700">مجموعة الصف</span>
            </a>
            <a href="#" class="bg-white rounded-lg p-3 text-center shadow-sm border hover:shadow-md transition">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-1">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700">محادثة فردية</span>
            </a>
            <a href="#" class="bg-white rounded-lg p-3 text-center shadow-sm border hover:shadow-md transition">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-1">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700">الإعلانات</span>
            </a>
            <a href="#" class="bg-white rounded-lg p-3 text-center shadow-sm border hover:shadow-md transition">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-1">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-700">الواجبات</span>
            </a>
        </div>

    </div>

    <script>
        // Auto-scroll chat to bottom
        const chatContainer = document.getElementById('chat-messages');
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Focus textarea on load
        document.getElementById('message').focus();

        // Handle Enter key
        document.getElementById('message').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.form.submit();
            }
        });
    </script>

</body>
</html>
