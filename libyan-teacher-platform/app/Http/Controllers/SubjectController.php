<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::latest()->paginate(10);

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'grade_level' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Subject::create($validator->validated());

        return redirect()->route('subjects.index')->with('success', 'تم إضافة المادة بنجاح');
    }

    public function show(Subject $subject)
    {
        $subject->load(['lessonPlans', 'exams', 'grades']);

        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'grade_level' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $subject->update($validator->validated());

        return redirect()->route('subjects.index')->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'تم حذف المادة بنجاح');
    }
}
