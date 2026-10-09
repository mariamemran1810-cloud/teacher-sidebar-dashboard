
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المساعد الذكي - منصة المعلم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Tajawal', sans-serif; }
        .output-content { line-height: 1.8; }
        .output-content h3 { font-weight: 700; font-size: 1.1rem; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #1f2937; }
        .output-content h4 { font-weight: 600; font-size: 1rem; margin-top: 1rem; margin-bottom: 0.375rem; color: #374151; }
        .output-content p { margin-bottom: 0.5rem; }
        .output-content ul, .output-content ol { padding-right: 1.5rem; margin-bottom: 0.75rem; }
        .output-content ul { list-style: disc; }
        .output-content ol { list-style: decimal; }
        .output-content li { margin-bottom: 0.25rem; }
        .output-content strong { font-weight: 700; }
        .output-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .output-content table th, .output-content table td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: right; }
        .output-content table th { background: #f3f4f6; font-weight: 600; }
        .spinner { border: 3px solid #e5e7eb; border-top: 3px solid #2563eb; border-radius: 50%; width: 20px; height: 20px; animation: spin 0.8s linear infinite; display: inline-block; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Breadcrumbs -->
    <nav class="bg-white shadow-sm border-b" aria-label="breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 transition">لوحة التحكم</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600 font-medium" aria-current="page">المساعد الذكي</li>
            </ol>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

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

        <!-- Connection Test Result -->
        @if(session('connection_status'))
        <div class="mb-4 p-4 rounded-lg flex items-center gap-3 {{ session('connection_status') === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}" role="alert">
            @if(session('connection_status') === 'success')
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="text-green-700 text-sm font-medium">{{ session('connection_message', 'تم الاتصال بنجاح!') }}</p>
            @else
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <p class="text-red-700 text-sm font-medium">{{ session('connection_message', 'فشل الاتصال!') }}</p>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Settings Panel -->
            <div class="lg:col-span-1 space-y-6">

                <!-- API Configuration -->
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <h2 class="font-bold text-gray-800">إعدادات API</h2>
                    </div>

                    <form action="{{ route('ai-assistant.save-api') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Provider Selection -->
                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-700 mb-1">مزود الخدمة</label>
                            <select id="provider" name="provider" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                                <option value="">اختر المزود</option>
                                <option value="gemini" {{ old('provider', $settings->provider ?? '') == 'gemini' ? 'selected' : '' }}>Gemini (Google)</option>
                                <option value="groq" {{ old('provider', $settings->provider ?? '') == 'groq' ? 'selected' : '' }}>Groq</option>
                                <option value="openrouter" {{ old('provider', $settings->provider ?? '') == 'openrouter' ? 'selected' : '' }}>OpenRouter</option>
                                <option value="openai" {{ old('provider', $settings->provider ?? '') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                            </select>
                            @error('provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- API Key -->
                        <div>
                            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">مفتاح API</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="api_key"
                                    name="api_key"
                                    value="{{ old('api_key', $settings->api_key ?? '') }}"
                                    placeholder="أدخل مفتاح API الخاص بك"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    required
                                >
                                <button type="button" onclick="togglePassword()" class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            @error('api_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                حفظ
                            </button>
                            <button type="button" onclick="testConnection()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                اختبار الاتصال
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Task Configuration -->
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <h2 class="font-bold text-gray-800">إعدادات المهمة</h2>
                    </div>

                    <form action="{{ route('ai-assistant.generate') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Task Type -->
                        <div>
                            <label for="task_type" class="block text-sm font-medium text-gray-700 mb-1">نوع المهمة</label>
                            <select id="task_type" name="task_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                                <option value="">اختر نوع المهمة</option>
                                <option value="lesson_plan" {{ old('task_type') == 'lesson_plan' ? 'selected' : '' }}>خطة درس</option>
                                <option value="exam_questions" {{ old('task_type') == 'exam_questions' ? 'selected' : '' }}>أسئلة اختبار</option>
                                <option value="worksheet" {{ old('task_type') == 'worksheet' ? 'selected' : '' }}>ورقة عمل</option>
                                <option value="activities" {{ old('task_type') == 'activities' ? 'selected' : '' }}>أنشطة صفية</option>
                                <option value="simplify" {{ old('task_type') == 'simplify' ? 'selected' : '' }}>تبسيط المحتوى</option>
                                <option value="video_script" {{ old('task_type') == 'video_script' ? 'selected' : '' }}>سيناريو فيديو</option>
                            </select>
                            @error('task_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">المادة الدراسية</label>
                            <select id="subject" name="subject" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                                <option value="">اختر المادة</option>
                                <option value="math" {{ old('subject') == 'math' ? 'selected' : '' }}>الرياضيات</option>
                                <option value="arabic" {{ old('subject') == 'arabic' ? 'selected' : '' }}>اللغة العربية</option>
                                <option value="english" {{ old('subject') == 'english' ? 'selected' : '' }}>اللغة الإنجليزية</option>
                                <option value="science" {{ old('subject') == 'science' ? 'selected' : '' }}>العلوم</option>
                                <option value="social" {{ old('subject') == 'social' ? 'selected' : '' }}>الدراسات الاجتماعية</option>
                                <option value="islamic" {{ old('subject') == 'islamic' ? 'selected' : '' }}>التربية الإسلامية</option>
                                <option value="computer" {{ old('subject') == 'computer' ? 'selected' : '' }}>الحاسب الآلي</option>
                            </select>
                            @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Grade Level -->
                        <div>
                            <label for="grade" class="block text-sm font-medium text-gray-700 mb-1">الصف الدراسي</label>
                            <select id="grade" name="grade" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                                <option value="">اختر الصف</option>
                                <option value="1" {{ old('grade') == '1' ? 'selected' : '' }}>الصف الأول</option>
                                <option value="2" {{ old('grade') == '2' ? 'selected' : '' }}>الصف الثاني</option>
                                <option value="3" {{ old('grade') == '3' ? 'selected' : '' }}>الصف الثالث</option>
                                <option value="4" {{ old('grade') == '4' ? 'selected' : '' }}>الصف الرابع</option>
                                <option value="5" {{ old('grade') == '5' ? 'selected' : '' }}>الصف الخامس</option>
                                <option value="6" {{ old('grade') == '6' ? 'selected' : '' }}>الصف السادس</option>
                                <option value="7" {{ old('grade') == '7' ? 'selected' : '' }}>الصف السابع</option>
                                <option value="8" {{ old('grade') == '8' ? 'selected' : '' }}>الصف الثامن</option>
                                <option value="9" {{ old('grade') == '9' ? 'selected' : '' }}>الصف التاسع</option>
                                <option value="10" {{ old('grade') == '10' ? 'selected' : '' }}>الصف العاشر</option>
                                <option value="11" {{ old('grade') == '11' ? 'selected' : '' }}>الصف الحادي عشر</option>
                                <option value="12" {{ old('grade') == '12' ? 'selected' : '' }}>الصف الثاني عشر</option>
                            </select>
                            @error('grade')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Topic -->
                        <div>
                            <label for="topic" class="block text-sm font-medium text-gray-700 mb-1">الموضوع</label>
                            <input
                                type="text"
                                id="topic"
                                name="topic"
                                value="{{ old('topic') }}"
                                placeholder="مثال: جمع الكسور، الخلية، الطاقة المتجددة..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required
                            >
                            @error('topic')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات إضافية (اختياري)</label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="أي تفاصيل إضافية تريد تضمينها..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                            >{{ old('notes') }}</textarea>
                        </div>

                        <button
                            type="submit"
                            id="generate-btn"
                            class="w-full bg-gradient-to-l from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 px-4 rounded-lg font-medium transition flex items-center justify-center gap-2 shadow-sm hover:shadow-md"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            توليد المحتوى
                        </button>
                    </form>
                </div>
            </div>

            <!-- Output Area -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border min-h-[600px] flex flex-col">

                    <!-- Output Header -->
                    <div class="border-b px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <h2 class="font-bold text-gray-800">المخرجات</h2>
                        </div>
                        <div id="loading-spinner" class="hidden items-center gap-2 text-sm text-blue-600">
                            <div class="spinner"></div>
                            <span>جاري التوليد...</span>
                        </div>
                    </div>

                    <!-- Output Content -->
                    <div class="flex-1 p-6 overflow-y-auto" id="output-area">
                        @if(session('output'))
                        <div class="output-content text-gray-700 text-sm">
                            @if(is_array(session('output')))
                                @foreach(session('output') as $section)
                                    @if(isset($section['title']))
                                    <h3>{{ $section['title'] }}</h3>
                                    @endif
                                    @if(isset($section['content']))
                                    <p>{{ $section['content'] }}</p>
                                    @endif
                                    @if(isset($section['items']))
                                    <ul>
                                        @foreach($section['items'] as $item)
                                        <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                    @endif
                                @endforeach
                            @else
                                {!! nl2br(e(session('output'))) %}
                            @endif
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center h-full text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">جاهز للمساعدة</h3>
                            <p class="text-sm text-gray-400 max-w-sm">اختر نوع المهمة، أدخل الموضوع، ثم اضغط "توليد المحتوى" للبدء</p>
                        </div>
                        @endif
                    </div>

                    <!-- Output Actions -->
                    <div class="border-t px-6 py-3 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button onclick="copyOutput()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                نسخ
                            </button>
                            <button onclick="downloadOutput()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                تحميل
                            </button>
                        </div>
                        <span class="text-xs text-gray-400">آخر تحديث: الآن</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Statistics -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4 shadow-sm border flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_requests'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">إجمالي الطلبات</p>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['today_requests'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">طلبات اليوم</p>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm border flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['tokens_used'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">الرموز المستخدمة</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('api_key');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function testConnection() {
            const btn = document.querySelector('button[onclick="testConnection()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<div class="spinner" style="width:16px;height:16px;border-width:2px;"></div> جاري الاختبار...';
            btn.disabled = true;

            // Simulate test - replace with actual AJAX call
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('تم اختبار الاتصال بنجاح!');
            }, 2000);
        }

        document.getElementById('generate-btn').addEventListener('click', function() {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('loading-spinner').classList.add('flex');
        });

        function copyOutput() {
            const output = document.getElementById('output-area').innerText;
            navigator.clipboard.writeText(output).then(() => {
                alert('تم النسخ بنجاح!');
            });
        }

        function downloadOutput() {
            const output = document.getElementById('output-area').innerText;
            const blob = new Blob([output], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'ai-output-' + new Date().toISOString().slice(0,10) + '.txt';
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>

</body>
</html>
