<?php

namespace App\Policies;

use App\Models\{Note,User};

class NotePolicy
{
public function view(?User $user, Note $note): bool
{
    // Public view only if approved + public
    if ($note->status === 'approved' && $note->is_public && $note->published_at) {
        return true;
    }

    // Owner or admin can see their own notes even if pending/rejected
    if ($user && ($user->id === $note->user_id || $user->is_admin)) {
        return true;
    }

    return false;
}
    public function create(User $user): bool {
        return in_array($user->role ?? 'user', ['admin','user','moderator']);
    }
    public function update(User $user, Note $note): bool {
        return $user->id === $note->user_id || $user->role === 'admin';
    }
    public function delete(User $user, Note $note): bool {
        return $user->id === $note->user_id || $user->role === 'admin';
    }
}
