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

    // User Management
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleActive'])->name('admin.users.toggle');

    // Event Categories
    Route::resource('/admin/categories', \App\Http\Controllers\Admin\EventCategoryController::class)->names([
        'index'   => 'admin.categories.index',
        'create'  => 'admin.categories.create',
        'store'   => 'admin.categories.store',
        'edit'    => 'admin.categories.edit',
        'update'  => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    // Ticket Types
    Route::resource('/admin/ticket-types', \App\Http\Controllers\TicketTypeController::class)->names([
        'index'   => 'admin.ticket-types.index',
        'create'  => 'admin.ticket-types.create',
        'store'   => 'admin.ticket-types.store',
        'edit'    => 'admin.ticket-types.edit',
        'update'  => 'admin.ticket-types.update',
        'destroy' => 'admin.ticket-types.destroy',
    ]);

});

Route::middleware(['auth', 'role:organizer'])->group(function () {
    Route::get('/organizer/dashboard', [\App\Http\Controllers\OrganizerController::class, 'dashboard'])->name('organizer.dashboard');

    // Event View Only — Organizer
    Route::get('/organizer/events', [EventController::class, 'index'])->name('organizer.events.index');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [\App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
});

require __DIR__.'/auth.php';
