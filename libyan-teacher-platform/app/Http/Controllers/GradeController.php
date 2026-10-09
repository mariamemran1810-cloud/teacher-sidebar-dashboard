<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['student', 'subject', 'teacher'])->latest()->paginate(10);

        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        return view('grades.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'grade' => 'required|string|max:10',
            'term' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Grade::create($validator->validated());

        return redirect()->route('grades.index')->with('success', 'تم إضافة الدرجة بنجاح');
    }

    public function show(Grade $grade)
    {
        $grade->load(['student', 'subject', 'teacher']);

        return view('grades.show', compact('grade'));
    }

    public function edit(Grade $grade)
    {
        return view('grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'grade' => 'required|string|max:10',
            'term' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $grade->update($validator->validated());

        return redirect()->route('grades.index')->with('success', 'تم تحديث الدرجة بنجاح');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'تم حذف الدرجة بنجاح');
    }
}
