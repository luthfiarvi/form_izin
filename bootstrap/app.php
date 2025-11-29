<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SecureHeaders::class,
            \App\Http\Middleware\DevTunnelUrlFix::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'approved' => \App\Http\Middleware\EnsureUserIsApproved::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Jalankan reset poin setiap awal kuartal (1 Jan, 1 Apr, 1 Jul, 1 Okt pukul 00:00)
        $schedule->command('user:reset-points')
            ->cron('0 0 1 1,4,7,10 *');
    })
    ->create();
