<?php
// app/Policies/GroupPolicy.php
namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function view(User $user, Group $group): bool
    {
        return $group->members()->where('users.id', $user->id)->exists();
    }

    public function message(User $user, Group $group): bool
    {
        return $this->view($user, $group);
    }
}
