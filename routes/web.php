<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::resource('books', BookController::class);

// AI Search Routes
Route::post('/ai/chat', [BookController::class, 'search'])->name('ai.chat');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
