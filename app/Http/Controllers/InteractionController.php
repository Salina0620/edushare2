<?php

namespace App\Http\Controllers;

use App\Models\{Note, Like, Bookmark, Comment, Report};
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function like(Request $request, Note $note)
    {
        abort_if(!$note->isApproved(), 404);

        $user = $request->user();

        $existing = $user->likes()->where('note_id', $note->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
            $msg = 'Like removed';
        } else {
            $user->likes()->create(['note_id' => $note->id]);
            $liked = true;
            $msg = 'Post liked';
        }

        // fresh count
        $count = $note->likes()->count();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'liked' => $liked, 'likes' => $count, 'message' => $msg]);
        }

        return back();
    }

    public function bookmark(Request $request, Note $note)
    {
        abort_if(!$note->isApproved(), 404);

        $user = $request->user();

        $existing = $user->bookmarks()->where('note_id', $note->id)->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
                    $msg = 'Bookmark removed';

        } else {
            $user->bookmarks()->create(['note_id' => $note->id]);
            $bookmarked = true;
                    $msg = 'Post bookmarked successfully';

        }

        $count = $note->bookmarks()->count();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'bookmarked' => $bookmarked, 'bookmarks' => $count, 'message'    => $msg,]);
        }

        return back();
    }

   public function comment(Request $request, Note $note)
{
    abort_if(!$note->isApproved(), 404);

    $data = $request->validate(['body' => 'required|string|max:5000']);
    $comment = $note->comments()->create([
        'user_id' => $request->user()->id,
        'body'    => $data['body'],
    ]);

    if ($request->wantsJson()) {
        return response()->json([
            'ok' => true,
            'body' => $comment->body,
            'user_name' => $comment->user->name,
            'message' => 'Comment posted successfully',
        ]);
    }

    return back();
}

public function report(Request $request, Note $note)
{
    abort_if(!$note->isApproved(), 404);

    $data = $request->validate(['reason' => 'required|string|max:255']);
    Report::create([
        'user_id' => $request->user()->id,
        'note_id' => $note->id,
        'reason' => $data['reason'],
    ]);

    if ($request->wantsJson()) {
        return response()->json([
            'ok' => true,
            'message' => 'Report submitted successfully',
        ]);
    }

    return back()->with('success', 'Report submitted. Our moderators will review.');
}

}
