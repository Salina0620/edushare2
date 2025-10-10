<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Note;
use App\Policies\NotePolicy;
use Illuminate\Support\Facades\Route;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Map the Note model to its policy
            Route::redirect('/dashboard', '/');

    }
}
