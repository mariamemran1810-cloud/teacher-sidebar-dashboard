<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display chat messages.
     */
    public function index(Request $request): View
    {
        $messages = ChatMessage::with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('chat.index', compact('messages'));
    }

    /**
     * Store a new chat message.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'] ?? null,
            'message' => $validated['message'],
        ]);

        return redirect()->route('chat.index')
            ->with('success', 'Message sent successfully.');
    }
}
