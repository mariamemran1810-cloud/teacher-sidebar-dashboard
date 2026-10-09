<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamResultController extends Controller
{
    public function index()
    {
        $examResults = ExamResult::with(['exam', 'student'])->latest()->paginate(10);

        return view('exam-results.index', compact('examResults'));
    }

    public function create()
    {
        return view('exam-results.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
            'marks_obtained' => 'nullable|numeric|min:0',
            'grade' => 'nullable|string|max:10',
            'remarks' => 'nullable|string',
            'status' => 'nullable|in:pass,fail,pending',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        ExamResult::create($validator->validated());

        return redirect()->route('exam-results.index')->with('success', 'تم إضافة نتيجة الامتحان بنجاح');
    }

    public function show(ExamResult $examResult)
    {
        $examResult->load(['exam', 'student']);

        return view('exam-results.show', compact('examResult'));
    }

    public function edit(ExamResult $examResult)
    {
        return view('exam-results.edit', compact('examResult'));
    }

    public function update(Request $request, ExamResult $examResult)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
            'marks_obtained' => 'nullable|numeric|min:0',
            'grade' => 'nullable|string|max:10',
            'remarks' => 'nullable|string',
            'status' => 'nullable|in:pass,fail,pending',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $examResult->update($validator->validated());

        return redirect()->route('exam-results.index')->with('success', 'تم تحديث نتيجة الامتحان بنجاح');
    }

    public function destroy(ExamResult $examResult)
    {
        $examResult->delete();

        return redirect()->route('exam-results.index')->with('success', 'تم حذف نتيجة الامتحان بنجاح');
    }
}
