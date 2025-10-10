<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;



class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'faculty_id',
        'semester_id',
        'subject_id',
        'title',
        'slug',
        'description',
        'file_path',
        'cover_path',
        'file_ext',
        'file_size',
        'is_public',
        'published_at',
        'status',
        'reject_reason', // 👈 add these so create() doesn’t drop them
    ];

    protected $casts = [
        'is_public'    => 'bool',
        'published_at' => 'datetime',
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // scopes
    public function scopePublished(Builder $q): Builder
    {
        // Only show approved + public + published
        return $q->where('status', 'approved')
            ->where('is_public', true)
            ->whereNotNull('published_at');
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') return $q;

        $tokens = collect(preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY));

        return $q->where(function ($outer) use ($tokens) {
            $tokens->each(function ($t) use ($outer) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $t) . '%';
                $outer->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('faculty',  fn($f) => $f->where('name', 'like', $like))
                        ->orWhereHas('semester', fn($s) => $s->where('name', 'like', $like))
                        ->orWhereHas('subject',  fn($s) => $s->where('name', 'like', $like))
                        ->orWhereHas('tags',     fn($t) => $t->where('name', 'like', $like)->orWhere('slug', 'like', $like));
                });
            });
        });
    }

    public function scopeFilter($q, array $f)
    {
        return $q
            ->when($f['faculty_id'] ?? null, fn($x, $v) => $x->where('faculty_id', $v))
            ->when($f['semester_id'] ?? null, fn($x, $v) => $x->where('semester_id', $v))
            ->when($f['subject_id'] ?? null, fn($x, $v) => $x->where('subject_id', $v))
            ->when($f['tag'] ?? null, fn($x, $slug) => $x->whereHas('tags', fn($qq) => $qq->where('slug', $slug)))
            ->when($f['author'] ?? null, fn($x, $id) => $x->where('user_id', $id));
    }

    // accessors

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return Storage::disk('public')->url($this->file_path);   // => /storage/...
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_path) return null;
        return Storage::disk('public')->url($this->cover_path);
    }

    public function getFileExtAttribute(): ?string
    {
        if ($this->attributes['file_ext'] ?? false) return strtolower($this->attributes['file_ext']);
        if (!$this->file_path) return null;
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function makeSlug(string $title): string
    {
        $slug = Str::slug($title);
        $base = $slug;
        $i = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }

public function isApproved(): bool
{
    return $this->status === 'approved'
        && $this->is_public
        && !is_null($this->published_at);
}

}
