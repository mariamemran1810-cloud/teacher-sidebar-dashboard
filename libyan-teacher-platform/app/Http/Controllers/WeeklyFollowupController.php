<?php

namespace App\Http\Controllers;

use App\Models\WeeklyFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WeeklyFollowupController extends Controller
{
    public function index()
    {
        $weeklyFollowups = WeeklyFollowup::with(['teacher', 'classroom', 'subject'])->latest()->paginate(10);

        return view('weekly-followups.index', compact('weeklyFollowups'));
    }

    public function create()
    {
        return view('weekly-followups.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'week_start' => 'required|date',
            'week_end' => 'required|date|after_or_equal:week_start',
            'content' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,reviewed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        WeeklyFollowup::create($validator->validated());

        return redirect()->route('weekly-followups.index')->with('success', 'تم إضافة المتابعة الأسبوعية بنجاح');
    }

    public function show(WeeklyFollowup $weeklyFollowup)
    {
        $weeklyFollowup->load(['teacher', 'classroom', 'subject']);

        return view('weekly-followups.show', compact('weeklyFollowup'));
    }

    public function edit(WeeklyFollowup $weeklyFollowup)
    {
        return view('weekly-followups.edit', compact('weeklyFollowup'));
    }

    public function update(Request $request, WeeklyFollowup $weeklyFollowup)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'week_start' => 'required|date',
            'week_end' => 'required|date|after_or_equal:week_start',
            'content' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,reviewed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $weeklyFollowup->update($validator->validated());

        return redirect()->route('weekly-followups.index')->with('success', 'تم تحديث المتابعة الأسبوعية بنجاح');
    }

    public function destroy(WeeklyFollowup $weeklyFollowup)
    {
        $weeklyFollowup->delete();

        return redirect()->route('weekly-followups.index')->with('success', 'تم حذف المتابعة الأسبوعية بنجاح');
    }
}
