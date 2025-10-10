<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class NoteController extends Controller
{

     use AuthorizesRequests;  
    // My notes
   // app/Http/Controllers/NoteController.php
public function mine(Request $request)
{
    $base = Note::where('user_id', auth()->id());

    $counts = [
        'all'       => (clone $base)->count(),
        'approved'  => (clone $base)->where('status','approved')->where('is_public',true)->whereNotNull('published_at')->count(),
        'pending'   => (clone $base)->where('status','pending')->count(),
        'rejected'  => (clone $base)->where('status','rejected')->count(),
    ];

    $q      = trim((string)$request->q);
    $status = $request->status;
    $sort   = $request->sort ?: 'latest';

    $notes = (clone $base)
        ->when($status, function ($q2) use ($status) {
            if ($status === 'approved') {
                $q2->where('status','approved')->where('is_public',true)->whereNotNull('published_at');
            } else {
                $q2->where('status',$status);
            }
        })
        ->when($q !== '', function ($q2) use ($q) {
            $like = '%'.str_replace(['%','_'],['\%','\_'],$q).'%';
            $q2->where(function ($qq) use ($like) {
                $qq->where('title','like',$like)
                   ->orWhere('description','like',$like);
            });
        })
        ->with(['faculty','semester','subject'])
        ->when($sort === 'title', fn($q2) => $q2->orderBy('title'), fn($q2) => $q2->latest())
        ->paginate(10)
        ->withQueryString();

    return view('public.notes.mine', compact('notes','counts'));
}


    // Edit form
    public function edit(Note $note)
    {
        $this->authorize('update', $note);

        $faculties = Faculty::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $subjects  = Subject::orderBy('name')->get();

        return view('public.notes.edit', compact('note','faculties','semesters','subjects'));
    }

    // Update action
    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'faculty_id' => 'nullable|exists:faculties,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:20480',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('notes/'.date('Y/m'), 'public');
            $data['file_ext']  = $request->file('file')->getClientOriginalExtension();
            $data['file_size'] = $request->file('file')->getSize();
        }
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers/'.date('Y/m'), 'public');
        }

        $note->update($data);

        return redirect()->route('public.notes.show', $note)
            ->with('success', 'Note updated successfully.');
    }

    // Delete
    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);

        $note->delete();

        return redirect()->route('notes.mine')->with('success','Note deleted.');
    }

    // Bookmarked notes
    public function bookmarks()
    {
        $notes = auth()->user()
            ->bookmarks()
            ->with('note.faculty','note.semester','note.subject')
            ->latest()
            ->paginate(10);

        return view('public.notes.bookmarks', compact('notes'));
    }
}
