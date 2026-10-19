<?php

use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Auth\Register;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Participant\MyTickets;
use App\Livewire\Pages\WorkshopDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Portal Routes
Route::get('/', Home::class)->name('home');
Route::get('/workshop/{slug}', WorkshopDetail::class)->name('workshop.detail');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Guest Auth Routes
Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// Participant Portal Protected Routes
Route::middleware('auth')->group(function (): void {
    Route::get('/participant/my-tickets', MyTickets::class)->name('participant.tickets');
});

// Midtrans Webhook Notification Handlers
Route::post('/api/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);
