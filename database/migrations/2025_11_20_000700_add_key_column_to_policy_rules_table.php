<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('policy_rules', 'key')) {
                // Kolom untuk menyimpan identifier aturan, misalnya "gamification.discipline"
                $table->string('key')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('policy_rules', function (Blueprint $table) {
            if (Schema::hasColumn('policy_rules', 'key')) {
                $table->dropColumn('key');
            }
        });
    }
};

