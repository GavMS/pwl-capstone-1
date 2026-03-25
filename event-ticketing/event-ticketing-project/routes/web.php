<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    $role = Auth::user()->role;

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'organizer') {
        return redirect()->route('organizer.dashboard');
    }

    return redirect()->route('user.dashboard');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Event CRUD — Admin
    Route::get('/admin/events',                [EventController::class, 'index'])->name('admin.events.index');
    Route::get('/admin/events/create',         [EventController::class, 'create'])->name('admin.events.create');
    Route::post('/admin/events',               [EventController::class, 'store'])->name('admin.events.store');
    Route::get('/admin/events/{event}/edit',   [EventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/admin/events/{event}',        [EventController::class, 'update'])->name('admin.events.update');
    Route::delete('/admin/events/{event}',     [EventController::class, 'destroy'])->name('admin.events.destroy');
});

Route::middleware(['auth', 'role:organizer'])->group(function () {
    Route::get('/organizer/dashboard', [\App\Http\Controllers\OrganizerController::class, 'dashboard'])->name('organizer.dashboard');

    // Event CRUD — Organizer
    Route::get('/organizer/events',                [EventController::class, 'index'])->name('organizer.events.index');
    Route::get('/organizer/events/create',         [EventController::class, 'create'])->name('organizer.events.create');
    Route::post('/organizer/events',               [EventController::class, 'store'])->name('organizer.events.store');
    Route::get('/organizer/events/{event}/edit',   [EventController::class, 'edit'])->name('organizer.events.edit');
    Route::put('/organizer/events/{event}',        [EventController::class, 'update'])->name('organizer.events.update');
    Route::delete('/organizer/events/{event}',     [EventController::class, 'destroy'])->name('organizer.events.destroy');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [\App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
});

require __DIR__.'/auth.php';
