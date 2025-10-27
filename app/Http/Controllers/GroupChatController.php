<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class GroupChatController extends Controller
{
    use AuthorizesRequests;

    public function index(Group $group)
    {
        abort_unless($group->status === 'approved', 403);
        $this->authorize('view', $group);

        $messages = $group->messages()
            ->with('user:id,name')
            ->latest()
            ->take(30)
            ->get()
            ->reverse();

        return view('groups.chat', compact('group', 'messages'));
    }

    public function store(Request $request, Group $group)
    {
        abort_unless($group->status === 'approved', 403);
        $this->authorize('message', $group);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $message = Message::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'content'  => trim($validated['content']),
        ])->load('user:id,name');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id'         => $message->id,
            'user'       => $message->user->name,
            'content'    => $message->content,
            'time'       => $message->created_at->diffForHumans(),
            'created_at' => $message->created_at->toIso8601String(),
        ]);
    }
}
