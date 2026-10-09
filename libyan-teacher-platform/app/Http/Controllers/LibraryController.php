<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibraryController extends Controller
{
    /**
     * Display library items.
     */
    public function index(Request $request): View
    {
        $query = LibraryItem::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $items = $query->latest()->paginate(20);

        return view('library.index', compact('items'));
    }

    /**
     * Store a new library item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:book,video,document,worksheet,other',
            'file' => 'required|file|max:51200',
            'subject_id' => 'nullable|exists:subjects,id',
            'grade_level' => 'nullable|string|max:50',
        ]);

        $path = $request->file('file')->store('library', 'public');

        LibraryItem::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientoriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'mime_type' => $request->file('file')->getMimeType(),
            'subject_id' => $validated['subject_id'],
            'grade_level' => $validated['grade_level'],
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('library.index')
            ->with('success', 'Library item uploaded successfully.');
    }

    /**
     * Download a library item.
     */
    public function download(LibraryItem $item): StreamedResponse
    {
        if (!Storage::disk('public')->exists($item->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($item->file_path, $item->file_name);
    }

    /**
     * Remove a library item.
     */
    public function destroy(LibraryItem $item): RedirectResponse
    {
        if (Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()->route('library.index')
            ->with('success', 'Library item deleted successfully.');
    }
}
