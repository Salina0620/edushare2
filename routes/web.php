<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicNoteController;
use App\Http\Controllers\NoteUploadController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Http\Request;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SavedController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupChatController;





Route::get('/', [PublicNoteController::class, 'home'])->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/upload', [NoteUploadController::class, 'create'])->name('notes.create');
    Route::post('/upload', [NoteUploadController::class, 'store'])->name('notes.store');

    Route::post('/i/{note:slug}/like', [InteractionController::class, 'like'])->name('i.like');
    Route::post('/i/{note:slug}/bookmark', [InteractionController::class, 'bookmark'])->name('i.bookmark');
    Route::post('/i/{note:slug}/comment', [InteractionController::class, 'comment'])->name('i.comment');
    Route::post('/i/{note:slug}/report', [InteractionController::class, 'report'])->name('i.report');
    Route::get('/recommended', [RecommendationController::class, 'index'])->name('notes.recommended');

    // My notes
    Route::get('/notes/mine', [NoteController::class, 'mine'])->name('notes.mine');
    Route::get('/notes/{note:slug}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::put('/notes/{note:slug}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note:slug}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Bookmarks
    Route::get('/bookmarks', [NoteController::class, 'bookmarks'])->name('notes.bookmarks');

    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');

    Route::get('/groups/{group}/chat', [GroupChatController::class, 'index'])->name('groups.chat');
    Route::post('/groups/{group}/chat', [GroupChatController::class, 'store'])->name('groups.chat.store');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])
        ->name('groups.leave');
});

//public notes
Route::name('public.')->group(function () {
    Route::get('/notes', [PublicNoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/{note:slug}', [PublicNoteController::class, 'show'])->name('notes.show');
    Route::post('/notes/{note:slug}/download', [PublicNoteController::class, 'download'])->name('notes.download');
});


Route::redirect('/dashboard', '/')->name('dashboard');   // or '/notes'
