<?php

use App\Http\Controllers\HostingDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/hosting/dashboard', [HostingDashboardController::class, 'show'])->name('hosting.dashboard');
});
