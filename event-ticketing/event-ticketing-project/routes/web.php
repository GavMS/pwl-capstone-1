<?php

use App\Http\Controllers\ProfileController;
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

    // User Management
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleActive'])->name('admin.users.toggle');
});

Route::middleware(['auth', 'role:organizer'])->group(function () {
    Route::get('/organizer/dashboard', [\App\Http\Controllers\OrganizerController::class, 'dashboard'])->name('organizer.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [\App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
});

require __DIR__.'/auth.php';
