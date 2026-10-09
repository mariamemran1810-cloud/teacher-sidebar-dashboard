<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TrainingCourse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'مدير المنصة',
            'email' => 'admin@libyan-teacher.ly',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create demo teacher
        $teacherUser = User::create([
            'name' => 'أ. محمد عبدالله',
            'email' => 'teacher@libyan-teacher.ly',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        // Create demo student user
        User::create([
            'name' => 'أحمد محمد',
            'email' => 'student@libyan-teacher.ly',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        // Create demo parent user
        User::create([
            'name' => 'محمد أحمد',
            'email' => 'parent@libyan-teacher.ly',
            'password' => Hash::make('password123'),
            'role' => 'parent',
        ]);

        // Create school
        $school = School::create([
            'name' => 'مدرسة النور النموذجية',
            'city' => 'طرابلس',
            'address' => 'شارع 14 يوليو، طرابلس',
            'phone' => '+218 91 234 5678',
            'email' => 'info@alnoor-school.ly',
            'description' => 'مدرسة نموذجية متخصصة في التعليم الأساسي والثانوي',
        ]);

        // Create classrooms
        $classrooms = [
            ['name' => 'الأول الأساسي «أ»', 'grade_level' => 'الأول الأساسي', 'section' => 'أ'],
            ['name' => 'الأول الأساسي «ب»', 'grade_level' => 'الأول الأساسي', 'section' => 'ب'],
            ['name' => 'الثاني الأساسي «أ»', 'grade_level' => 'الثاني الأساسي', 'section' => 'أ'],
            ['name' => 'الثالث الأساسي «أ»', 'grade_level' => 'الثالث الأساسي', 'section' => 'أ'],
            ['name' => 'الرابع الأساسي «أ»', 'grade_level' => 'الرابع الأساسي', 'section' => 'أ'],
            ['name' => 'الخامس الأساسي «أ»', 'grade_level' => 'الخامس الأساسي', 'section' => 'أ'],
            ['name' => 'السادس الأساسي «أ»', 'grade_level' => 'السادس الأساسي', 'section' => 'أ'],
        ];

        foreach ($classrooms as $classroom) {
            Classroom::create(array_merge($classroom, ['school_id' => $school->id]));
        }

        // Create subjects
        $subjects = [
            ['name' => 'اللغة العربية', 'code' => 'ARB', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'الرياضيات', 'code' => 'MTH', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'اللغة الإنجليزية', 'code' => 'ENG', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'العلوم', 'code' => 'SCI', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'الدراسات الاجتماعية', 'code' => 'SOC', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'التربية الإسلامية', 'code' => 'ISL', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'التربية الفنية', 'code' => 'ART', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
            ['name' => 'التربية الرياضية', 'code' => 'PE', 'grade_level' => 'جميع المراحل', 'stage' => 'أساسي'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // Create teacher record
        Teacher::create([
            'user_id' => $teacherUser->id,
            'name' => 'أ. محمد عبدالله',
            'email' => 'teacher@libyan-teacher.ly',
            'phone' => '+218 91 234 5678',
            'specialization' => 'الرياضيات',
            'qualification' => 'ماجستير رياضيات',
            'experience_years' => 10,
            'bio' => 'معلم رياضيات بخبرة 10 سنوات في التعليم الأساسي والثانوي',
        ]);

        // Create demo students
        $students = [
            ['name' => 'أحمد محمد', 'gender' => 'male', 'classroom_id' => 1],
            ['name' => 'فاطمة علي', 'gender' => 'female', 'classroom_id' => 1],
            ['name' => 'عمر خالد', 'gender' => 'male', 'classroom_id' => 2],
            ['name' => 'مريم سالم', 'gender' => 'female', 'classroom_id' => 2],
            ['name' => 'يوسف أحمد', 'gender' => 'male', 'classroom_id' => 3],
            ['name' => 'زينب محمود', 'gender' => 'female', 'classroom_id' => 3],
        ];

        foreach ($students as $student) {
            Student::create(array_merge($student, [
                'email' => strtolower(str_replace(' ', '.', $student['name'])) . '@student.ly',
                'phone' => '+218 92 000 0000',
                'birth_date' => '2015-01-01',
                'address' => 'طرابلس، ليبيا',
                'parent_name' => 'ولي أمر',
                'parent_phone' => '+218 92 000 0000',
            ]));
        }

        // Create training courses
        $courses = [
            [
                'title' => 'استخدام التكنولوجيا في التعليم',
                'description' => 'تعلم كيفية استخدام الأدوات التكنولوجية الحديثة في الفصل الدراسي',
                'instructor' => 'د. أحمد الفيتوري',
                'duration_hours' => 20,
                'level' => 'مبتدئ',
                'category' => 'تكنولوجيا التعليم',
                'is_free' => true,
            ],
            [
                'title' => 'تصميم الاختبارات الإلكترونية',
                'description' ' => 'أساليب تصميم اختبارات إلكترونية فعالة وموثوقة',
                'instructor' => 'أ. سارة المبروك',
                'duration_hours' => 15,
                'level' => 'متوسط',
                'category' => 'التقويم',
                'is_free' => true,
            ],
            [
                'title' => 'إدارة الصف الدراسي',
                'description' => 'استراتيجيات فعالة لإدارة الصف وتنظيم التعلم',
                'instructor' => 'أ. محمد قاسم',
                'duration_hours' => 10,
                'level' => 'مبتدئ',
                'category' => 'إدارة الصف',
                'is_free' => true,
            ],
            [
                'title' => 'التعليم التفاعلي',
                'description' => 'تفعيل مشاركة الطلاب في عملية التعلم',
                'instructor' => 'د. فاطمة الزهراء',
                'duration_hours' => 25,
                'level' => 'متقدم',
                'category' => 'استراتيجيات التدريس',
                'is_free' => true,
            ],
        ];

        foreach ($courses as $course) {
            TrainingCourse::create($course);
        }
    }
}
