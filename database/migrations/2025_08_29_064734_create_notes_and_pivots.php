<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('file_path');          // storage path: public/...
            $table->string('cover_path')->nullable();
            $table->string('file_ext', 16)->index();
            $table->unsignedBigInteger('file_size');

            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('downloads')->default(0);
            $table->boolean('is_public')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['faculty_id','semester_id','subject_id']);
            $table->fullText(['title','description']);
        });

        Schema::create('note_tag', function (Blueprint $table) {
            $table->foreignId('note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['note_id','tag_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('note_tag');
        Schema::dropIfExists('notes');
    }
};
