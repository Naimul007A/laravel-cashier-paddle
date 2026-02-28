<?php

use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('billing/plans', [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
});

require __DIR__ . '/settings.php';
