<?php

namespace App\Providers;

use App\Models\Note;
use App\Policies\NotePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Note::class => NotePolicy::class,
        \App\Models\Group::class => \App\Policies\GroupPolicy::class,

    ];

    public function boot(): void
    {
        //
    }
}
