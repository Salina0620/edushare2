<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function hasIndex(string $table, string $index): bool
    {
        $rows = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$index]);
        return !empty($rows);
    }

    public function up(): void
    {
        if ($this->hasIndex('group_user', 'group_user_group_id_user_id_unique')) {
            return; // already there, skip
        }

        Schema::table('group_user', function (Blueprint $table) {
            $table->unique(['group_id', 'user_id'], 'group_user_group_id_user_id_unique');
        });
    }

    public function down(): void
    {
        if ($this->hasIndex('group_user', 'group_user_group_id_user_id_unique')) {
            Schema::table('group_user', function (Blueprint $table) {
                $table->dropUnique('group_user_group_id_user_id_unique');
            });
        }
    }
};
