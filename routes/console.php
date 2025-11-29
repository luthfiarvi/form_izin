<?php

use App\Models\User;
use App\Models\UserPointQuarter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:reset-points', function () {
    $this->info('Memulai reset poin pelanggaran per kuartal...');

    $now = now();
    $year = (int) $now->year;
    $quarter = (int) ceil($now->month / 3);

    $users = User::all();

    foreach ($users as $user) {
        $currentPoints = (int) ($user->points ?? 100);
        $currentPoints = max(0, $currentPoints);
        $totalDeduction = max(0, 100 - $currentPoints);

        UserPointQuarter::updateOrCreate(
            [
                'user_id' => $user->id,
                'year' => $year,
                'quarter' => $quarter,
            ],
            [
                'starting_points' => 100,
                'ending_points' => $currentPoints,
                'total_deduction' => $totalDeduction,
                'closed_at' => now(),
            ]
        );

        $user->points = 100;
        $user->save();
    }

    $this->info('Reset poin pelanggaran selesai.');
})->purpose('Reset poin pelanggaran pengguna tiap 3 bulan dan simpan rekap per kuartal');
