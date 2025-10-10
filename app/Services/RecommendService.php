<?php

namespace App\Services;

use App\Models\{Note, Tag, Like, Bookmark};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendService
{
    /**
     * Hybrid: content-based (tags/subject/faculty) + collaborative (similar users' likes/bookmarks)
     */
    public function forUser(int $userId, int $limit = 12): Collection
    {
        // 1) User signals
        $likedIds = Like::where('user_id', $userId)->pluck('note_id');
        $bookmarkedIds = Bookmark::where('user_id', $userId)->pluck('note_id');
        $seedIds = $likedIds->merge($bookmarkedIds)->unique()->take(50); // top 50 signals

        // If no interactions yet -> fallback to popular
        if ($seedIds->isEmpty()) {
            return Note::published()
                ->with(['faculty','semester','subject','tags'])
                ->orderByDesc('views')
                ->orderByDesc('downloads')
                ->latest('published_at')
                ->limit($limit)
                ->get();
        }

        // 2) Content-based candidates by tag / subject / faculty
        $seedNotes = Note::whereIn('id', $seedIds)->with('tags')->get();
        $tagIds = $seedNotes->flatMap(fn($n) => $n->tags->pluck('id'))->unique();
        $subjectIds = $seedNotes->pluck('subject_id')->filter()->unique();
        $facultyIds = $seedNotes->pluck('faculty_id')->filter()->unique();

        $contentCandidates = Note::published()
            ->whereNotIn('id', $seedIds) // don’t recommend what user already liked/bookmarked
            ->where(function($q) use ($tagIds, $subjectIds, $facultyIds) {
                $q->when($tagIds->isNotEmpty(), fn($qq) => $qq->whereHas('tags', fn($t) => $t->whereIn('tags.id', $tagIds)))
                  ->orWhereIn('subject_id', $subjectIds)
                  ->orWhereIn('faculty_id', $facultyIds);
            })
            ->with(['tags','faculty','semester','subject'])
            ->take(200) // pool
            ->get();

        // 3) Simple collaborative: users who liked the same notes also liked X
        $similarUserIds = DB::table('likes')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->whereIn('note_id', $seedIds)
            ->where('user_id', '<>', $userId)
            ->groupBy('user_id')
            ->having('c', '>=', 1) // can tweak
            ->orderByDesc('c')
            ->limit(100)
            ->pluck('user_id');

        $collabNoteIds = Like::whereIn('user_id', $similarUserIds)
            ->whereNotIn('note_id', $seedIds)
            ->pluck('note_id');

        $collabCandidates = Note::published()
            ->whereIn('id', $collabNoteIds)
            ->with(['tags','faculty','semester','subject'])
            ->take(200)
            ->get();

        // 4) Scoring (very simple) + recency boost
        $now = now();
        $score = [];

        $add = function(Note $n, float $s) use (&$score, $now) {
            // recency bonus: last 180 days
            $days = max(1, $now->diffInDays($n->published_at ?? $n->created_at));
            $recencyBoost = 1.0 + (180 - min($days, 180)) / 180.0 * 0.3; // up to +30%
            $popBoost = 1.0 + min(($n->views + $n->downloads) / 1000.0, 1.0) * 0.2; // up to +20%
            $score[$n->id] = ($score[$n->id] ?? 0) + $s * $recencyBoost * $popBoost;
        };

        // content-based scoring
        foreach ($contentCandidates as $n) {
            $s = 0;
            if ($subjectIds->contains($n->subject_id)) $s += 2.0;
            if ($facultyIds->contains($n->faculty_id)) $s += 1.0;
            if ($tagIds->isNotEmpty()) {
                $overlap = $n->tags->pluck('id')->intersect($tagIds)->count();
                $s += min($overlap, 5) * 0.6; // tag overlap up to 3.0
            }
            if ($s > 0) $add($n, $s);
        }

        // collaborative scoring
        foreach ($collabCandidates as $n) {
            $add($n, 2.5);
        }

        // 5) Pick top K
        arsort($score);
        $ids = array_slice(array_keys($score), 0, $limit);

        // Fallback if empty
        if (empty($ids)) {
            return Note::published()
                ->with(['faculty','semester','subject','tags'])
                ->orderByDesc('views')
                ->orderByDesc('downloads')
                ->latest('published_at')
                ->limit($limit)
                ->get();
        }

        // Keep the order of $ids
        $notes = Note::with(['faculty','semester','subject','tags'])->whereIn('id',$ids)->get()->keyBy('id');
        return collect($ids)->map(fn($id) => $notes[$id])->values();
    }
}
