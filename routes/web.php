<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicNoteController;
use App\Http\Controllers\NoteUploadController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Http\Request;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SavedController;



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



});

//public notes
Route::name('public.')->group(function () {
    Route::get('/notes', [PublicNoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/{note:slug}', [PublicNoteController::class, 'show'])->name('notes.show');
    Route::post('/notes/{note:slug}/download', [PublicNoteController::class, 'download'])->name('notes.download');
});


Route::redirect('/dashboard', '/')->name('dashboard');   // or '/notes'
