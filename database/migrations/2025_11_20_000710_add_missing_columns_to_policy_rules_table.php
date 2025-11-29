<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('policy_rules', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('policy_rules', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('policy_rules', 'enabled')) {
                $table->boolean('enabled')->default(true);
            }
            if (! Schema::hasColumn('policy_rules', 'config')) {
                $table->json('config')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('policy_rules', function (Blueprint $table) {
            if (Schema::hasColumn('policy_rules', 'config')) {
                $table->dropColumn('config');
            }
            if (Schema::hasColumn('policy_rules', 'enabled')) {
                $table->dropColumn('enabled');
            }
            if (Schema::hasColumn('policy_rules', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('policy_rules', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};

