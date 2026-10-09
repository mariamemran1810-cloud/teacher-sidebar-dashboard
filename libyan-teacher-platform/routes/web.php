<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\LessonPlanController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WeeklyFollowupController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\ParentMessageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\NotificationController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::resource('students', StudentController::class);

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Subjects
    Route::resource('subjects', SubjectController::class);

    // Lesson Plans
    Route::resource('lesson-plans', LessonPlanController::class);

    // Exams
    Route::resource('exams', ExamController::class);

    // Exam Results
    Route::resource('exam-results', ExamResultController::class);

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

    // Grades
    Route::resource('grades', GradeController::class);

    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('/payments/report', [PaymentController::class, 'report'])->name('payments.report');

    // Followups
    Route::resource('weekly-followups', WeeklyFollowupController::class);
    Route::resource('monthly-reports', MonthlyReportController::class);

    // Parent Messages
    Route::resource('parent-messages', ParentMessageController::class);

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    // AI Assistant
    Route::get('/ai-assistant', [AIController::class, 'index'])->name('ai.index');
    Route::post('/ai/generate-lesson', [AIController::class, 'generateLesson'])->name('ai.generateLesson');
    Route::post('/ai/generate-exam', [AIController::class, 'generateExam'])->name('ai.generateExam');
    Route::post('/ai/generate-questions', [AIController::class, 'generateQuestions'])->name('ai.generateQuestions');
    Route::post('/ai/generate-worksheet', [AIController::class, 'generateWorksheet'])->name('ai.generateWorksheet');
    Route::post('/ai/test-connection', [AIController::class, 'testConnection'])->name('ai.testConnection');

    // Library
    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::post('/library', [LibraryController::class, 'store'])->name('library.store');
    Route::get('/library/{id}/download', [LibraryController::class, 'download'])->name('library.download');
    Route::delete('/library/{id}', [LibraryController::class, 'destroy'])->name('library.destroy');

    // Training
    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
    Route::post('/training/{id}/complete', [TrainingController::class, 'complete'])->name('training.complete');

    // Community
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::post('/community/{id}/like', [CommunityController::class, 'like'])->name('community.like');
    Route::post('/community/{id}/comment', [CommunityController::class, 'comment'])->name('community.comment');

    // School Management
    Route::get('/school', [SchoolController::class, 'index'])->name('school.index');
    Route::put('/school', [SchoolController::class, 'update'])->name('school.update');

    // Schedule
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::delete('/schedule/{id}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});
