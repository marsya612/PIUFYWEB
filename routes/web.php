

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


// Route::get('home', [PiutangController::class, 'dashboard']);
Route::resource('piutang', PiutangController::class);
Route::get('laporan', [PiutangController::class, 'laporan']);
Route::get('/laporan-piutang-data', [PiutangController::class, 'data']);
// Route::get('/profile', [PiutangController::class, 'profile'])->name('profile');
// Route::get('/profile/edit', [PiutangController::class, 'editProfile'])->name('profile.edit');
// Route::put('/profile/update', [PiutangController::class, 'updateProfile'])->name('profile.update');
Route::patch('/piutang/{id}/lunas', [PiutangController::class, 'markLunas'])->name('piutang.lunas');
Route::get('/laporan-piutang-pdf', [PiutangController::class, 'exportPdf']);
Route::get('/notifikasi', [PiutangController::class, 'notifikasi'])->name('notifikasi');




/*
|--------------------------------------------------------------------------
| GUEST (BELUM LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {

    // LOGIN
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // REGISTER
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION (WAJIB)
|--------------------------------------------------------------------------
*/

// halaman info "cek email"
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// klik link dari email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/login')->with('success', 'Email berhasil diverifikasi, silakan login');
})->middleware(['signed'])->name('verification.verify');

// kirim ulang email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'Link verifikasi dikirim ulang');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| SUDAH LOGIN + SUDAH VERIFIKASI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // DASHBOARD / HALAMAN UTAMA
    // Route::get('/', [PiutangController::class, 'index'])->name('piutang.index');
    Route::get('home', [PiutangController::class, 'dashboard'])->name('home');

    // PROFILE
    Route::get('/profile', [PiutangController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [PiutangController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [PiutangController::class, 'updateProfile'])->name('profile.update');
});

