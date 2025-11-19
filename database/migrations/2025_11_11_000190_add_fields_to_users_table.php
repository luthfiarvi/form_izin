<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'signature_path')) {
                $table->string('signature_path')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_kepala_kepegawaian')) {
                $table->boolean('is_kepala_kepegawaian')->default(false);
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->nullable();
            }
            if (!Schema::hasColumn('users', 'whatsapp_phone')) {
                $table->string('whatsapp_phone')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
            if (Schema::hasColumn('users', 'is_kepala_kepegawaian')) {
                $table->dropColumn('is_kepala_kepegawaian');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'whatsapp_phone')) {
                $table->dropColumn('whatsapp_phone');
            }
        });
    }
};

