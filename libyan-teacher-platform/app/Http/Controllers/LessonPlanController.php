<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonPlanController extends Controller
{
    public function index()
    {
        $lessonPlans = LessonPlan::with(['teacher', 'subject', 'classroom'])->latest()->paginate(10);

        return view('lesson-plans.index', compact('lessonPlans'));
    }

    public function create()
    {
        return view('lesson-plans.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'objectives' => 'nullable|string',
            'date' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,published,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        LessonPlan::create($validator->validated());

        return redirect()->route('lesson-plans.index')->with('success', 'تم إضافة خطة الدرس بنجاح');
    }

    public function show(LessonPlan $lessonPlan)
    {
        $lessonPlan->load(['teacher', 'subject', 'classroom']);

        return view('lesson-plans.show', compact('lessonPlan'));
    }

    public function edit(LessonPlan $lessonPlan)
    {
        return view('lesson-plans.edit', compact('lessonPlan'));
    }

    public function update(Request $request, LessonPlan $lessonPlan)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'objectives' => 'nullable|string',
            'date' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,published,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $lessonPlan->update($validator->validated());

        return redirect()->route('lesson-plans.index')->with('success', 'تم تحديث خطة الدرس بنجاح');
    }

    public function destroy(LessonPlan $lessonPlan)
    {
        $lessonPlan->delete();

        return redirect()->route('lesson-plans.index')->with('success', 'تم حذف خطة الدرس بنجاح');
    }
}
