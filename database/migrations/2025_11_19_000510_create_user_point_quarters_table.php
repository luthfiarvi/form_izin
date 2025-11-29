<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_point_quarters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter'); // 1-4
            $table->integer('starting_points')->default(100);
            $table->integer('ending_points')->default(0);
            $table->integer('total_deduction')->default(0);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'quarter'], 'user_year_quarter_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_point_quarters');
    }
};

