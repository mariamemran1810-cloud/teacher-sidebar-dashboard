<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    /**
     * Display community posts.
     */
    public function index(Request $request): View
    {
        $posts = CommunityPost::with(['user', 'comments.user'])
            ->withCount('likes', 'comments')
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->latest()
            ->paginate(15);

        return view('community.index', compact('posts'));
    }

    /**
     * Store a new community post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'category' => 'required|in:general,question,announcement,discussion',
        ]);

        CommunityPost::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
        ]);

        return redirect()->route('community.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Toggle like on a post.
     */
    public function like(Request $request, CommunityPost $post): RedirectResponse
    {
        $user = auth()->user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->where('user_id', $user->id)->delete();
            $message = 'Post unliked.';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $message = 'Post liked.';
        }

        return redirect()->route('community.index')
            ->with('success', $message);
    }

    /**
     * Add a comment to a post.
     */
    public function comment(Request $request, CommunityPost $post): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        CommunityComment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('community.index')
            ->with('success', 'Comment added successfully.');
    }
}
