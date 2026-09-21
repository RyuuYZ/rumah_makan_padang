<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\AppDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SystemLogController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/orders', [ApiOrderController::class, 'store'])->name('orders.store');
Route::post('/reservation', [HomeController::class, 'storeReservation'])->name('reservation.store')->middleware('throttle:5,1');
Route::post('/reviews/check-order', [HomeController::class, 'checkOrderForReview'])->name('reviews.check-order')->middleware('throttle:10,1');
Route::post('/reviews', [HomeController::class, 'storeReview'])->name('reviews.store')->middleware('throttle:3,1');

// Health Check
Route::get('/up', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
})->name('health.check');

// Halaman form cari pesanan pelanggan
Route::get('/cek-pesanan', [ApiOrderController::class, 'showSearch'])->name('order.search.form');
Route::post('/cek-pesanan', [ApiOrderController::class, 'processSearch'])->name('order.search.submit');

// Halaman status order customer — tampilkan QR Order dan status pesanan (BRD CUS-08)
Route::get('/pesanan/{token}', [ApiOrderController::class, 'orderStatus'])->name('order.status');

// Unduh Aplikasi Mobile APK & Halaman Download Resmi
Route::get('/download/apk', [AppDownloadController::class, 'downloadApk'])->name('app.download.apk');
Route::get('/download/qr', [AppDownloadController::class, 'qrCode'])->name('app.download.qr');
Route::get('/unduh-aplikasi', [AppDownloadController::class, 'unduhPage'])->name('app.download.page');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes (Guest only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit')->middleware('throttle:5,1');
    Route::post('/login/qr', [AuthController::class, 'qrLogin'])->name('admin.login.qr')->middleware('throttle:5,1');
    Route::get('/login/2fa', [AuthController::class, 'show2faVerify'])->name('admin.login.2fa');
    Route::post('/login/2fa', [AuthController::class, 'verify2fa'])->name('admin.login.2fa.submit');
    Route::post('/login/2fa/cancel', [AuthController::class, 'cancel2fa'])->name('admin.login.2fa.cancel');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Panel Routes (Auth Required)
|--------------------------------------------------------------------------
*/
// Kasir POS (Dedicated Interface)
Route::middleware('auth')->group(function () {
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/orders/{id}/status', [ApiOrderController::class, 'updateStatus'])->name('kasir.orders.updateStatus');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', function () {
        $user = Auth::user();
        if (empty($user->login_token)) {
            $user->login_token = Str::random(60);
            $user->save();
        }

        return view('admin.profile.index');
    })->name('profile');
    Route::post('/profile/photo', [AdminController::class, 'updatePhoto'])->name('profile.photo');

    // 2FA Management Routes
    Route::get('/profile/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/profile/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post('/profile/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    // Orders Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // POS Cashier Scanner
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/find-order', [PosController::class, 'findOrder'])->name('pos.findOrder');

    // Menu Management
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
    Route::post('/menu/{id}/toggle', [MenuController::class, 'toggleActive'])->name('menu.toggleActive');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

    // System Logs, Guide, & Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');
    Route::view('/guide', 'admin.guide.index')->name('guide.index');

    // Menu Categories
    Route::resource('menu-categories', MenuCategoryController::class)->except(['show']);

    // Tables Management
    Route::resource('tables', TableController::class)->except(['create', 'show', 'edit']);

    // Branches Management
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');
    Route::post('/branches/{id}/toggle', [BranchController::class, 'toggleActive'])->name('branches.toggleActive');
    Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');

    // Reviews Moderation
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/toggle-approve', [ReviewController::class, 'toggleApprove'])->name('reviews.toggleApprove');
    Route::post('/reviews/{id}/toggle-pin', [ReviewController::class, 'togglePin'])->name('reviews.togglePin');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Reservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{id}/status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});
