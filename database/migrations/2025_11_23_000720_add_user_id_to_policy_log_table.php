<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_log', function (Blueprint $table) {
            if (! Schema::hasColumn('policy_log', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('policy_log', function (Blueprint $table) {
            if (Schema::hasColumn('policy_log', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};

