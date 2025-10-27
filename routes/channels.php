<?php

use App\Models\Group;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('presence-group.{groupId}', function ($user, $groupId) {
    $isMember = Group::whereKey($groupId)
        ->whereHas('members', fn($q) => $q->where('users.id', $user->id))
        ->exists();

    if (! $isMember) return false;

    return [
        'id'    => $user->id,
        'name'  => $user->name,
        'avatar'=> method_exists($user, 'getProfilePhotoUrlAttribute') ? $user->profile_photo_url : null,
    ];
});
