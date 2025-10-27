<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // Show approved groups
    public function index()
    {
        $groups = Group::where('status', 'approved')->latest()->get();
        return view('groups.index', compact('groups'));
    }

    // Create group form
    public function create()
    {
        return view('groups.create');
    }

    // Store new group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('groups.index')->with('success', 'Group created and sent for admin approval.');
    }

    // Join a group
    public function join(Group $group)
    {
        if ($group->status !== 'approved') {
            return back()->with('error', 'This group is not approved yet.');
        }

        auth()->user()->groups()->syncWithoutDetaching([
            $group->id => ['role' => 'member'],
        ]);

        return redirect()
            ->route('groups.chat', $group)
            ->with('success', 'You have joined the group.');
    }

    public function leave(\App\Models\Group $group)
    {
        // must be a member to leave
        abort_unless($group->members()->where('users.id', auth()->id())->exists(), 403);

        // optional guard: prevent the creator from leaving without transferring ownership
        if ((int) $group->created_by === (int) auth()->id()) {
            return back()->with('error', 'You are the group owner. Transfer ownership before leaving.');
        }

        auth()->user()->groups()->detach($group->id);

        return redirect()->route('groups.index')
            ->with('success', 'You left the group.');
    }
}
