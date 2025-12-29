<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LetterController;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/sign-in', [AuthController::class, 'sign_in'])->name('admin.signin');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/general-settings', [DashboardController::class, 'general_settings'])->name('admin.settings');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
    Route::get('/change-password', [DashboardController::class, 'change_password'])->name('admin.change.password');

    Route::resource('clients', ClientController::class);
    Route::get('/load-clients', [ClientController::class, 'load'])->name('admin.client.load');
    Route::post('/import-clients', [ClientController::class, 'import'])->name('admin.client.import');

    Route::resource('letters', LetterController::class);
    Route::get('/load-letters', [LetterController::class, 'load'])->name('admin.letter.load');
    Route::get('/letters/generate/{letter_id}', [LetterController::class, 'generate_letter'])->name('admin.letter.generate');
    Route::get('/load-generated-letters', [LetterController::class, 'load_generated_letters'])->name('admin.generated_letter.load');
    Route::get('/download-letter/{letter_id}', [LetterController::class, 'download'])->name('admin.letter.download');

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});