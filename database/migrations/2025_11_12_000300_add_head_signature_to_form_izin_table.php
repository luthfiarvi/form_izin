<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_izin', function (Blueprint $table) {
            if (!Schema::hasColumn('form_izin', 'head_signature_path')) {
                $table->string('head_signature_path')->nullable()->after('izin_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('form_izin', function (Blueprint $table) {
            if (Schema::hasColumn('form_izin', 'head_signature_path')) {
                $table->dropColumn('head_signature_path');
            }
        });
    }
};

