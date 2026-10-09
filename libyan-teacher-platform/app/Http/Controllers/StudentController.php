<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'classroom'])->latest()->paginate(10);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:students,email',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,graduated,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Student::create($validator->validated());

        return redirect()->route('students.index')->with('success', 'تم إضافة الطالب بنجاح');
    }

    public function show(Student $student)
    {
        $student->load(['user', 'classroom', 'attendances', 'grades', 'examResults', 'payments']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:students,email,' . $student->id,
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,graduated,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student->update($validator->validated());

        return redirect()->route('students.index')->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
