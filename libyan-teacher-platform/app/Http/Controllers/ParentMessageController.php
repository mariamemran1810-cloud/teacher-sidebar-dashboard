<?php

namespace App\Http\Controllers;

use App\Models\ParentMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParentMessageController extends Controller
{
    public function index()
    {
        $parentMessages = ParentMessage::with(['teacher', 'student'])->latest()->paginate(10);

        return view('parent-messages.index', compact('parentMessages'));
    }

    public function create()
    {
        return view('parent-messages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'is_read' => 'nullable|boolean',
            'sent_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['is_read'] = $request->boolean('is_read', false);
        $data['sent_at'] = $data['sent_at'] ?? now();

        ParentMessage::create($data);

        return redirect()->route('parent-messages.index')->with('success', 'تم إرسال الرسالة بنجاح');
    }

    public function show(ParentMessage $parentMessage)
    {
        $parentMessage->load(['teacher', 'student']);

        return view('parent-messages.show', compact('parentMessage'));
    }

    public function edit(ParentMessage $parentMessage)
    {
        return view('parent-messages.edit', compact('parentMessage'));
    }

    public function update(Request $request, ParentMessage $parentMessage)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'is_read' => 'nullable|boolean',
            'sent_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $parentMessage->update($validator->validated());

        return redirect()->route('parent-messages.index')->with('success', 'تم تحديث الرسالة بنجاح');
    }

    public function destroy(ParentMessage $parentMessage)
    {
        $parentMessage->delete();

        return redirect()->route('parent-messages.index')->with('success', 'تم حذف الرسالة بنجاح');
    }
}
