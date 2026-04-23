<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
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
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login')->with('success', 'Email berhasil diverifikasi');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Link verifikasi dikirim ulang');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| AUTH + VERIFIED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ✅ FIX DI SINI (bukan piutang.index)
    Route::get('/', [PiutangController::class, 'index'])->name('home');

    // RESOURCE
    Route::resource('piutang', PiutangController::class);

    // TAMBAHAN
    Route::patch('/piutang/{id}/lunas', [PiutangController::class, 'markLunas'])->name('piutang.lunas');

    Route::get('/laporan', [PiutangController::class, 'laporan'])->name('laporan');
    Route::get('/laporan-piutang-data', [PiutangController::class, 'data'])->name('laporan.data');
    Route::get('/laporan-piutang-pdf', [PiutangController::class, 'exportPdf'])->name('laporan.pdf');

    Route::get('/notifikasi', [PiutangController::class, 'notifikasi'])->name('notifikasi');

    Route::get('/profile', [PiutangController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [PiutangController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [PiutangController::class, 'updateProfile'])->name('profile.update');
});