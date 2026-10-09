<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Payment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index(Request $request): View
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalSubjects = Subject::count();
        $totalExams = Exam::count();
        $totalPayments = Payment::sum('amount');

        $recentActivities = collect([
            ['type' => 'student', 'message' => 'New student registered', 'time' => now()->subMinutes(5)],
            ['type' => 'exam', 'message' => 'Exam created for Grade 10', 'time' => now()->subMinutes(15)],
            ['type' => 'payment', 'message' => 'Payment received', 'time' => now()->subMinutes(30)],
            ['type' => 'attendance', 'message' => 'Attendance marked', 'time' => now()->subHours(1)],
        ]);

        $upcomingExams = Exam::where('date', '>=', now())
            ->orderBy('date')
            ->take(5)
            ->get();

        $attendanceOverview = [
            'present' => Attendance::whereDate('date', today())->where('status', 'present')->count(),
            'absent' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
            'late' => Attendance::whereDate('date', today())->where('status', 'late')->count(),
        ];

        return view('dashboard.index', compact(
            'totalStudents',
            'totalTeachers',
            'totalSubjects',
            'totalExams',
            'totalPayments',
            'recentActivities',
            'upcomingExams',
            'attendanceOverview'
        ));
    }
}
