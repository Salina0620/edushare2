<?php

namespace App\Http\Controllers;

use App\Models\{Note,Faculty,Semester,Subject,Tag};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteUploadController extends Controller
{
    public function create(){
        $faculties = Faculty::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $subjects  = Subject::orderBy('name')->get();
        return view('public.notes.create', compact('faculties','semesters','subjects'));
    }

  public function store(Request $request){
    $data = $request->validate([
        'title' => ['required','string','max:255'],
        'description' => ['nullable','string','max:5000'],
        'faculty_id' => ['nullable','exists:faculties,id'],
        'semester_id' => ['nullable','exists:semesters,id'],
        'subject_id' => ['nullable','exists:subjects,id'],
        'file' => ['required','file','mimes:pdf,doc,docx,ppt,pptx','max:20480'],
        'cover' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
        'tags' => ['nullable','string'],
        'is_public' => ['sometimes','boolean'],
    ]);

    $path = $request->file('file')->store('notes/'.date('Y/m'), 'public');
    $coverPath = $request->file('cover')?->store('covers/'.date('Y/m'), 'public');
    $ext = strtolower($request->file('file')->getClientOriginalExtension());
    $size = $request->file('file')->getSize();

    $note = Note::create([
        'user_id'     => Auth::id(),
        'faculty_id'  => $data['faculty_id'] ?? null,
        'semester_id' => $data['semester_id'] ?? null,
        'subject_id'  => $data['subject_id'] ?? null,
        'title'       => $data['title'],
        'slug'        => Note::makeSlug($data['title']),
        'description' => $data['description'] ?? null,
        'file_path'   => $path,
        'cover_path'  => $coverPath,
        'file_ext'    => $ext,
        'file_size'   => $size,
        'is_public'   => $request->boolean('is_public', true),
        'status'      => 'pending',   // 👈 moderation
        'published_at'=> null,        // 👈 will be set when admin approves
    ]);

    if(!empty($data['tags'])){
        $tags = collect(explode(',', $data['tags']))
            ->map(fn($t)=>trim($t))
            ->filter()
            ->map(fn($name)=>['name'=>$name, 'slug'=>str($name)->slug()]);

        $tagIds = [];
        foreach($tags as $t){
            $tagIds[] = Tag::firstOrCreate(['slug'=>$t['slug']], ['name'=>$t['name']])->id;
        }
        $note->tags()->sync($tagIds);
    }

    return redirect()
        ->route('public.notes.index', $note)
        ->with('success','Your note was uploaded and is awaiting admin approval.');
}

}
