<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_log', function (Blueprint $table) {
            if (! Schema::hasColumn('policy_log', 'policy_key')) {
                $table->string('policy_key')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('policy_log', 'reasons')) {
                $table->json('reasons')->nullable()->after('allowed');
            }
            if (! Schema::hasColumn('policy_log', 'context')) {
                $table->json('context')->nullable()->after('reasons');
            }
            if (! Schema::hasColumn('policy_log', 'evaluated_at')) {
                $table->timestamp('evaluated_at')->nullable()->after('context');
            }
        });
    }

    public function down(): void
    {
        Schema::table('policy_log', function (Blueprint $table) {
            if (Schema::hasColumn('policy_log', 'policy_key')) {
                $table->dropColumn('policy_key');
            }
            if (Schema::hasColumn('policy_log', 'reasons')) {
                $table->dropColumn('reasons');
            }
            if (Schema::hasColumn('policy_log', 'context')) {
                $table->dropColumn('context');
            }
            if (Schema::hasColumn('policy_log', 'evaluated_at')) {
                $table->dropColumn('evaluated_at');
            }
        });
    }
};
