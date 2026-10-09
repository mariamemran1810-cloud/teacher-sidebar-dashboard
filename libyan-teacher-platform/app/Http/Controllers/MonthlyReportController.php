<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MonthlyReportController extends Controller
{
    public function index()
    {
        $monthlyReports = MonthlyReport::with(['teacher', 'classroom', 'subject'])->latest()->paginate(10);

        return view('monthly-reports.index', compact('monthlyReports'));
    }

    public function create()
    {
        return view('monthly-reports.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'content' => 'nullable|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,approved',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        MonthlyReport::create($validator->validated());

        return redirect()->route('monthly-reports.index')->with('success', 'تم إضافة التقرير الشهري بنجاح');
    }

    public function show(MonthlyReport $monthlyReport)
    {
        $monthlyReport->load(['teacher', 'classroom', 'subject']);

        return view('monthly-reports.show', compact('monthlyReport'));
    }

    public function edit(MonthlyReport $monthlyReport)
    {
        return view('monthly-reports.edit', compact('monthlyReport'));
    }

    public function update(Request $request, MonthlyReport $monthlyReport)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'content' => 'nullable|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,approved',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $monthlyReport->update($validator->validated());

        return redirect()->route('monthly-reports.index')->with('success', 'تم تحديث التقرير الشهري بنجاح');
    }

    public function destroy(MonthlyReport $monthlyReport)
    {
        $monthlyReport->delete();

        return redirect()->route('monthly-reports.index')->with('success', 'تم حذف التقرير الشهري بنجاح');
    }
}
