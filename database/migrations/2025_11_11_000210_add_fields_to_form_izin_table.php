<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_izin', function (Blueprint $table) {
            if (!Schema::hasColumn('form_izin', 'date')) {
                $table->date('date')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('form_izin', 'in_time')) {
                $table->time('in_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('form_izin', 'out_time')) {
                $table->time('out_time')->nullable()->after('in_time');
            }
            if (!Schema::hasColumn('form_izin', 'purpose')) {
                $table->text('purpose')->nullable()->after('out_time');
            }
            if (!Schema::hasColumn('form_izin', 'izin_type')) {
                $table->string('izin_type')->nullable()->after('purpose');
            }
        });
    }

    public function down(): void
    {
        Schema::table('form_izin', function (Blueprint $table) {
            foreach (['date','in_time','out_time','purpose','izin_type'] as $col) {
                if (Schema::hasColumn('form_izin', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

