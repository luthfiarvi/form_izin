<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_log', function (Blueprint $table) {
            if (! Schema::hasColumn('policy_log', 'allowed')) {
                $table->boolean('allowed')->default(false)->after('policy_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('policy_log', function (Blueprint $table) {
            if (Schema::hasColumn('policy_log', 'allowed')) {
                $table->dropColumn('allowed');
            }
        });
    }
};

