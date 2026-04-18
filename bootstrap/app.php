<?php

use App\Http\Middleware\EnsureAccountNotSuspended;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('hosting:generate-renewal-invoices')->dailyAt('02:15');
        $schedule->command('hosting:mark-invoices-overdue')->dailyAt('00:10');
        $schedule->command('hosting:send-invoice-reminders')->dailyAt('08:00');
        $schedule->command('hosting:send-domain-expiry-alerts')->dailyAt('09:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [
            EnsureAccountNotSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
