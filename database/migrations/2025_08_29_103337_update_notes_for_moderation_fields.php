<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notes', function (Blueprint $table) {
            if (! Schema::hasColumn('notes', 'status')) {
                $table->string('status')->default('pending')->index()->after('description');
            }
            if (! Schema::hasColumn('notes', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('status');
            }
        });
    }

    public function down(): void {
        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'reject_reason')) {
                $table->dropColumn('reject_reason');
            }
            if (Schema::hasColumn('notes', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};