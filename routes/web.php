<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\PiutangController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/

// halaman notice verifikasi email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// klik link email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('home')
        ->with('success', 'Email berhasil diverifikasi');
})->middleware(['auth', 'signed'])->name('verification.verify');

// kirim ulang email verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'Link verifikasi dikirim ulang');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| AUTH + VERIFIED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------
    | LOGOUT
    |--------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------
    | DASHBOARD
    |--------------------------
    */
    Route::get('/', [PiutangController::class, 'dashboard'])->name('home');
    Route::get('/home', [PiutangController::class, 'dashboard']);

    /*
    |--------------------------
    | PIUTANG (CRUD)
    |--------------------------
    */
    Route::resource('piutang', PiutangController::class)->except(['show']);

    Route::patch('/piutang/{id}/lunas', [PiutangController::class, 'markLunas'])
        ->name('piutang.lunas');

    /*
    |--------------------------
    | LAPORAN
    |--------------------------
    */
    Route::get('/laporan', [PiutangController::class, 'laporan'])->name('laporan');
    Route::get('/laporan-data', [PiutangController::class, 'data']);
    Route::get('/laporan-pdf', [PiutangController::class, 'exportPdf']);

    /*
    |--------------------------
    | PROFILE
    |--------------------------
    */
    Route::get('/profile', [PiutangController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [PiutangController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [PiutangController::class, 'updateProfile'])->name('profile.update');

    /*
    |--------------------------
    | NOTIFIKASI
    |--------------------------
    */
    Route::get('/notifikasi', [PiutangController::class, 'notifikasi'])->name('notifikasi');
});