<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->latest()->paginate(10);

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:teachers,email',
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'status' => 'nullable|active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Teacher::create($validator->validated());

        return redirect()->route('teachers.index')->with('success', 'تم إضافة المعلم بنجاح');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'lessonPlans', 'exams', 'grades', 'schedules']);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:teachers,email,' . $teacher->id,
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $teacher->update($validator->validated());

        return redirect()->route('teachers.index')->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'تم حذف المعلم بنجاح');
    }
}
