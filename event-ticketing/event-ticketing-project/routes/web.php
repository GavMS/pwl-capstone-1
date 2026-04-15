<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('user.explore');
    }

    $role = Auth::user()->role;

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'organizer') {
        return redirect()->route('organizer.dashboard');
    }

    return redirect()->route('user.dashboard');
})->name('home');

// Public browse pages (guest-friendly)
Route::get('/explore', [\App\Http\Controllers\UserController::class, 'explore'])->name('user.explore');
Route::get('/explore/creators', [\App\Http\Controllers\UserController::class, 'exploreCreators'])->name('user.explore.creators');
Route::get('/organizer/{id}', [\App\Http\Controllers\UserController::class, 'organizerProfile'])->name('user.organizer.profile')->where('id', '[0-9]+');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Event detail + purchase flow (login required)
    Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');

    // Queue & Waiting List
    Route::get('/queue/{event_id}/enter', [\App\Http\Controllers\QueueController::class, 'enter'])->name('queue.enter');
    Route::get('/queue/{event_id}/waiting-room', [\App\Http\Controllers\QueueController::class, 'waitingRoom'])->name('queue.waiting-room');
    Route::get('/api/queue/{event_id}/status', [\App\Http\Controllers\QueueController::class, 'status'])->name('queue.status');
    Route::post('/api/queue/{event_id}/release', [\App\Http\Controllers\QueueController::class, 'release'])->name('queue.release');
    Route::post('/api/queue/{event_id}/skip', [\App\Http\Controllers\QueueController::class, 'skipCategory'])->name('queue.skip');

    // Checkout (Protected by Queue)
    Route::middleware('check.queue')->group(function () {
        Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'confirm'])->name('checkout.confirm');
        Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    });

    Route::post('/voucher/apply', [\App\Http\Controllers\VoucherController::class, 'apply'])->name('voucher.apply');

    Route::get('/checkout/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/{order_id}/recreate', [\App\Http\Controllers\CheckoutController::class, 'recreate'])->name('checkout.recreate');
    Route::post('/checkout/{order_id}/mock', [\App\Http\Controllers\CheckoutController::class, 'mockSuccess'])->name('checkout.mock');

    // My Tickets
    Route::get('/my-tickets', [\App\Http\Controllers\UserTicketController::class, 'index'])->name('user.my-tickets');
    Route::get('/my-tickets/{uniqueCode}', [\App\Http\Controllers\UserTicketController::class, 'show'])->name('user.ticket.detail');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/financials', [\App\Http\Controllers\AdminController::class, 'financials'])->name('admin.financials');
    Route::get('/admin/financials/export/excel', [\App\Http\Controllers\AdminController::class, 'exportFinancialsExcel'])->name('admin.financials.export.excel');
    Route::get('/admin/financials/export/pdf', [\App\Http\Controllers\AdminController::class, 'exportFinancialsPDF'])->name('admin.financials.export.pdf');


    // Event CRUD — Admin
    Route::get('/admin/events', [EventController::class, 'index'])->name('admin.events.index');
    Route::get('/admin/events/create', [EventController::class, 'create'])->name('admin.events.create');
    Route::post('/admin/events', [EventController::class, 'store'])->name('admin.events.store');
    Route::get('/admin/events/{event}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/admin/events/{event}', [EventController::class, 'update'])->name('admin.events.update');
    Route::delete('/admin/events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');

    // User Management
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Event Categories
    Route::resource('/admin/categories', \App\Http\Controllers\Admin\EventCategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    // Ticket Types
    Route::resource('/admin/ticket-types', \App\Http\Controllers\TicketTypeController::class)->names([
        'index' => 'admin.ticket-types.index',
        'create' => 'admin.ticket-types.create',
        'store' => 'admin.ticket-types.store',
        'edit' => 'admin.ticket-types.edit',
        'update' => 'admin.ticket-types.update',
        'destroy' => 'admin.ticket-types.destroy',
    ]);

    // Vouchers
    Route::resource('/admin/vouchers', \App\Http\Controllers\Admin\VoucherController::class)->names([
        'index' => 'admin.vouchers.index',
        'create' => 'admin.vouchers.create',
        'store' => 'admin.vouchers.store',
        'edit' => 'admin.vouchers.edit',
        'update' => 'admin.vouchers.update',
        'destroy' => 'admin.vouchers.destroy',
    ]);
    Route::patch('/admin/vouchers/{voucher}/toggle', [\App\Http\Controllers\Admin\VoucherController::class, 'toggleActive'])->name('admin.vouchers.toggle');

});

Route::middleware(['auth', 'role:organizer'])->group(function () {
    Route::get('/organizer/dashboard', [\App\Http\Controllers\OrganizerController::class, 'dashboard'])->name('organizer.dashboard');

    // Event View Only — Organizer
    Route::get('/organizer/events', [EventController::class, 'index'])->name('organizer.events.index');
    
    // Sales & Payouts — Organizer
    Route::get('/organizer/sales', [\App\Http\Controllers\OrganizerController::class, 'sales'])->name('organizer.sales');
    Route::get('/organizer/sales/export/excel', [\App\Http\Controllers\OrganizerController::class, 'exportSalesExcel'])->name('organizer.sales.export.excel');
    Route::get('/organizer/sales/export/pdf', [\App\Http\Controllers\OrganizerController::class, 'exportSalesPDF'])->name('organizer.sales.export.pdf');
    
    // Scan Tiket — Organizer
    Route::get('/organizer/scan', [\App\Http\Controllers\TicketScanController::class, 'index'])->name('organizer.scan');
    Route::post('/organizer/scan', [\App\Http\Controllers\TicketScanController::class, 'scan'])->name('organizer.scan.process');
    Route::get('/organizer/scan/history/{event}', [\App\Http\Controllers\TicketScanController::class, 'history'])->name('organizer.scan.history');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [\App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
});

// Midtrans Webhook (Sengaja diluar auth middleware)
Route::post('/midtrans/callback', [\App\Http\Controllers\PaymentCallbackController::class, 'handle'])->name('midtrans.callback');

require __DIR__ . '/auth.php';
