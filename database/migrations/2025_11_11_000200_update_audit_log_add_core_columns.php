<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_log', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('audit_log', 'action')) {
                $table->string('action')->nullable();
            }
            if (!Schema::hasColumn('audit_log', 'model_type')) {
                $table->string('model_type')->nullable();
            }
            if (!Schema::hasColumn('audit_log', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable();
            }
            if (!Schema::hasColumn('audit_log', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            if (Schema::hasColumn('audit_log', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            foreach (['action','model_type','model_id','meta'] as $col) {
                if (Schema::hasColumn('audit_log', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

