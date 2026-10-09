<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display class schedules.
     */
    public function index(Request $request): View
    {
        $query = Schedule::with(['subject', 'classroom', 'teacher']);

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get();
        $classrooms = Classroom::all();
        $subjects = Subject::all();

        return view('schedule.index', compact('schedules', 'classrooms', 'subjects'));
    }

    /**
     * Store a new schedule entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        // Check for conflicts
        $conflict = Schedule::where('classroom_id', $validated['classroom_id'])
            ->where('day', $validated['day'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withErrors(['conflict' => 'This time slot conflicts with an existing schedule.'])
                ->withInput();
        }

        Schedule::create($validated);

        return redirect()->route('schedule.index')
            ->with('success', 'Schedule entry created successfully.');
    }

    /**
     * Remove a schedule entry.
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('schedule.index')
            ->with('success', 'Schedule entry deleted successfully.');
    }
}
