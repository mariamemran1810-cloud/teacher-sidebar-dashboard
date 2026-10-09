<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['teacher', 'subject', 'classroom'])->latest()->paginate(10);

        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        return view('exams.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
            'total_marks' => 'nullable|numeric|min:0',
            'passing_marks' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:scheduled,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Exam::create($validator->validated());

        return redirect()->route('exams.index')->with('success', 'تم إضافة الامتحان بنجاح');
    }

    public function show(Exam $exam)
    {
        $exam->load(['teacher', 'subject', 'classroom', 'examResults']);

        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        return view('exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
            'total_marks' => 'nullable|numeric|min:0',
            'passing_marks' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:scheduled,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $exam->update($validator->validated());

        return redirect()->route('exams.index')->with('success', 'تم تحديث الامتحان بنجاح');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'تم حذف الامتحان بنجاح');
    }
}
