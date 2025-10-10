<?php

namespace App\Http\Controllers;

use App\Models\{Note, Faculty, Semester, Subject, Tag, Download};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicNoteController extends Controller
{
    public function home(Request $request)
    {
        $faculties = Faculty::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $subjects  = Subject::orderBy('name')->get();

        $latestNotes = Note::published()
            ->latest('published_at')
            ->with(['faculty', 'semester', 'subject'])
            ->take(6)
            ->get();

        // Build recommended (or empty collection if not logged in / service missing)
        $recommended = collect();
        if (auth()->check() && class_exists(\App\Services\RecommendService::class)) {
            $recommended = app(\App\Services\RecommendService::class)->forUser(auth()->id(), 6);
        }

        return view('public.home', compact(
            'faculties',
            'semesters',
            'subjects',
            'latestNotes',
            'recommended'
        ));
    }


    public function index(Request $request)
    {
        $filters = $request->only(['faculty_id', 'semester_id', 'subject_id', 'search', 'tag', 'author', 'sort']);

 // ✅ accept short params too (?faculty=, ?semester=, ?subject=)
    $filters['faculty_id']  = $filters['faculty_id']  ?? (int) $request->input('faculty');
    $filters['semester_id'] = $filters['semester_id'] ?? (int) $request->input('semester');
    $filters['subject_id']  = $filters['subject_id']  ?? (int) $request->input('subject');



        $query = Note::published()
            ->with(['faculty', 'semester', 'subject', 'user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->filter($filters)
            ->search($filters['search'] ?? null);

        $sort = $filters['sort'] ?? 'new';
        $query = match ($sort) {
            'popular' => $query->orderByDesc('views'),
            'downloaded' => $query->orderByDesc('downloads'),
            'discussed' => $query->orderByDesc('comments_count'),
            default => $query->orderByDesc('published_at'),
        };

        $notes = $query->paginate(12)->withQueryString();

        $faculties = Faculty::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $subjects  = Subject::orderBy('name')->get();
        $tags      = Tag::orderBy('name')->take(30)->get();

        return view('public.notes.index', compact('notes', 'faculties', 'semesters', 'subjects', 'tags'));
    }

   // app/Http/Controllers/PublicNoteController.php

public function show(Note $note)
{
    // Only owner/admin can see unapproved; everyone else gets 404 (no signal)
    if (Gate::forUser(auth()->user())->denies('view', $note)) {
        abort(404);
    }

    $approved = $note->isApproved();

    if ($approved) {
        $note->increment('views');
    }

    // Preload counts for UI
    $note->loadCount(['likes', 'bookmarks', 'comments']);

    // Only compute personal states when approved (no actions when read-only)
    $liked = false;
    $bookmarked = false;
    if ($approved && auth()->check()) {
        $uid = auth()->id();
        $liked = $note->likes()->where('user_id', $uid)->exists();
        $bookmarked = $note->bookmarks()->where('user_id', $uid)->exists();
    }

    $related = Note::published()
        ->where('id', '!=', $note->id)
        ->when($note->subject_id, fn($q) => $q->where('subject_id', $note->subject_id))
        ->when(!$note->subject_id && $note->faculty_id, fn($q) => $q->where('faculty_id', $note->faculty_id))
        ->latest('published_at')
        ->take(6)
        ->get();

    // <— tell the Blade whether it must disable ALL interactions
    $readonly = !$approved;

    return view('public.notes.show', compact('note', 'related', 'liked', 'bookmarked', 'readonly'));
}

public function download(Request $request, Note $note)
{
    // Downloads are NEVER allowed unless the note is approved (even for owner)
    if (!$note->isApproved()) {
        abort(404);
    }

    if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($note->file_path)) {
        return back()->with('error', 'File not found on server.');
    }

    $note->increment('downloads');

    \App\Models\Download::create([
        'note_id' => $note->id,
        'user_id' => optional($request->user())->id,
        'ip'      => $request->ip(),
    ]);

    $filename = \Illuminate\Support\Str::slug($note->title) . '.' . $note->file_ext;
    return \Illuminate\Support\Facades\Storage::disk('public')->download($note->file_path, $filename);
}

}
