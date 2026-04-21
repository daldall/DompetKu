<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\Admin\AdminAiUsageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDefaultCategoryController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('landing');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('is_admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/ai-usage', [AdminAiUsageController::class, 'index'])->name('ai-usage.index');

        Route::get('/default-kategori', [AdminDefaultCategoryController::class, 'index'])->name('default-kategori.index');
        Route::get('/default-kategori/create', [AdminDefaultCategoryController::class, 'create'])->name('default-kategori.create');
        Route::post('/default-kategori', [AdminDefaultCategoryController::class, 'store'])->name('default-kategori.store');
        Route::delete('/default-kategori/{defaultKategori}', [AdminDefaultCategoryController::class, 'destroy'])->name('default-kategori.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::resource('kategori', CategoryController::class)->except(['show']);
    Route::resource('transaksi', TransactionController::class);
    Route::post('/transaksi/scan-struk', [TransactionController::class, 'scanStruk'])->name('transaksi.scanStruk');
    Route::post('/transaksi/upload-struk', [TransactionController::class, 'uploadStruk'])->name('transaksi.uploadStruk');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/export', [RiwayatController::class, 'export'])->name('riwayat.export');


    Route::resource('target', TargetController::class)->except(['show']);
    Route::post('/target/{target}/nabung', [TargetController::class, 'nabung'])->name('target.nabung');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/offline', function () {
        return view('modules.laravelpwa.offline');
    });
});
