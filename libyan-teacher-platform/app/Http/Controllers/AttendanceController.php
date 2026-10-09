<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request): View
    {
        $date = $request->input('date', today()->toDateString());
        $classroomId = $request->input('classroom_id');

        $attendance = Attendance::with('student')
            ->whereDate('date', $date)
            ->when($classroomId, fn ($q) => $q->where('classroom_id', $classroomId))
            ->get();

        $students = Student::all();

        return view('attendance.index', compact('attendance', 'students', 'date', 'classroomId'));
    }

    /**
     * Store newly created attendance records.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.notes' => 'nullable|string|max:500',
        ]);

        foreach ($validated['attendance'] as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance recorded successfully.');
    }

    /**
     * Generate attendance report.
     */
    public function report(Request $request): View
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $attendance = Attendance::with('student')
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->when($request->classroom_id, fn ($q) => $q->where('classroom_id', $request->classroom_id))
            ->get();

        $summary = [
            'total_days' => $attendance->groupBy('date')->count(),
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
        ];

        return view('attendance.report', compact('attendance', 'summary'));
    }
}
