<?php
// database/migrations/xxxx_xx_xx_xxxxxx_rebuild_group_user_pivot.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('group_user');

        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->timestamps();

            $table->unique(['group_id', 'user_id']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_user');
    }
};
