<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingController extends Controller
{
    /**
     * Display training courses.
     */
    public function index(Request $request): View
    {
        $courses = TrainingCourse::with('enrollments')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(12);

        return view('training.index', compact('courses'));
    }

    /**
     * Mark a training course as completed.
     */
    public function complete(Request $request, TrainingCourse $course): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $course->enrollments()->updateOrCreate(
            ['user_id' => $validated['user_id']],
            [
                'status' => 'completed',
                'completed_at' => now(),
                'score' => $validated['score'],
                'notes' => $validated['notes'],
            ]
        );

        return redirect()->route('training.index')
            ->with('success', 'Training course marked as completed.');
    }
}
