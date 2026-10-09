
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملف المعلم - {{ $teacher->name }}</title>
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
                    <a href="{{ route('teachers.index') }}" class="text-blue-600 hover:text-blue-800">المعلمين</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-400" viewBox="0 0 320 512"><path d="M96 480c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L242.8 256L73.38 86.63c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l192 192c12.5 12.5 12.5 32.75 0 45.25l-192 192C112.4 476.9 104.2 480 96 480z"/></svg>
                </li>
                <li>
                    <span class="text-gray-500">{{ $teacher->name }}</span>
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

        <!-- Teacher Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start mb-4">
                <h1 class="text-2xl font-bold text-gray-800">ملف المعلم</h1>
                <div class="flex gap-2 mt-4 md:mt-0">
                    <a href="{{ route('teachers.edit', $teacher) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 ease-in-out">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        تعديل
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-500 text-sm">الاسم الكامل</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">البريد الإلكتروني</p>
                    <p class="text-gray-800 font-semibold" dir="ltr">{{ $teacher->email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">رقم الهاتف</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->phone }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">الجنس</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">تاريخ الميلاد</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->birth_date }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">الرقم الوطني</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->national_id ?? 'غير متوفر' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">العنوان</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->address }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">المادة الدراسية</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->subject->name ?? 'غير محدد' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">تاريخ التعيين</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->hire_date }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">الراتب</p>
                    <p class="text-gray-800 font-semibold">{{ number_format($teacher->salary, 2) }} د.ل</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">المؤهل العلمي</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->qualification }}</p>
                </div>
                @if($teacher->notes)
                <div class="md:col-span-2 lg:col-span-3">
                    <p class="text-gray-500 text-sm">ملاحظات</p>
                    <p class="text-gray-800 font-semibold">{{ $teacher->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Classes Taught -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">الفصول التي يدرّسها</h2>
            @if($teacher->classrooms && $teacher->classrooms->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الفصل</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">عدد الطلاب</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الجدول</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacher->classrooms as $classroom)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $classroom->name }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $classroom->students->count() }}</td>
                                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $classroom->schedule ?? 'غير محدد' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لا توجد فصول محددة لهذا المعلم</p>
            @endif
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">آخر الأنشطة</h2>
            @if($teacher->activities && $teacher->activities->count() > 0)
                <div class="space-y-3">
                    @foreach($teacher->activities as $activity)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                            <div>
                                <p class="text-gray-800 text-sm">{{ $activity->description }}</p>
                                <p class="text-gray-500 text-xs mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لا توجد أنشطة مسجلة</p>
            @endif
        </div>

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('teachers.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; العودة لقائمة المعلمين
            </a>
        </div>
    </div>
    @endsection

</body>
</html>
