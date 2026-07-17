<?php

use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Web\Admin\RateController;
use App\Http\Controllers\Web\Admin\ReportController;
use App\Http\Controllers\Web\Admin\ShipmentController;
use App\Http\Controllers\Web\Admin\VehicleController;
use App\Http\Controllers\Web\Auth\CustomerAuthController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Courier\CourierController;
use App\Http\Controllers\Web\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Modul Landing Page + Tracking (Publik, via Inertia)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/lacak', [PageController::class, 'track'])->name('track');
Route::get('/tentang', [PageController::class, 'about'])->name('about');

/*
|--------------------------------------------------------------------------
| Web Routes — Autentikasi Terpadu (unified login, guard `web` + `customer`)
|--------------------------------------------------------------------------
| Satu halaman `/login` untuk SEMUA role: Admin, Manager, Kasir, Kurir
| (guard `web`, tabel `users`) maupun Customer (guard `customer`, tabel
| `customers`). LoginController::store() otomatis mendeteksi guard mana
| yang cocok dengan kredensial, lalu redirect ke dashboard yang sesuai
| (lihat LoginController::dashboardFor()). Tidak ada lagi halaman login
| terpisah per role.
|
| `guest:web,customer` menolak akses ke /login kalau salah satu guard
| sudah authenticated (lihat RedirectIfAuthenticated::redirectUsing()
| di AppServiceProvider untuk logika redirect-nya).
*/
Route::middleware('guest:web,customer')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware(['throttle:10,1']);
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:web')->name('logout');

// Kompatibilitas mundur: link/bookmark lama ke /customer/login tetap
// berfungsi, cuma diarahkan ke halaman login terpadu.
Route::redirect('/customer/login', '/login', 301)->name('customer.login');

/*
|--------------------------------------------------------------------------
| Web Routes — Registrasi Customer (guard `customer`, tabel `customers`)
|--------------------------------------------------------------------------
| Staf (admin/manager/cashier/courier) tidak mendaftar sendiri — akun
| dibuat oleh Admin, jadi tidak ada route register untuk guard `web`.
*/
Route::middleware('guest:customer')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->middleware(['throttle:5,1', 'recaptcha']);
});
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('auth:customer')->name('customer.logout');

/*
|--------------------------------------------------------------------------
| Web Routes — Email Verification (Customer)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:customer')->prefix('customer/email')->group(function () {
    Route::get('/verify', [\App\Http\Controllers\Web\Auth\EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify/{id}/{hash}', [\App\Http\Controllers\Web\Auth\EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/verification-notification', [\App\Http\Controllers\Web\Auth\EmailVerificationController::class, 'resend'])
        ->middleware('throttle:5,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Web Routes — Dashboard & Kirim Paket Customer (guard `customer`)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:customer', 'verified.customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Web\Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kirim', [\App\Http\Controllers\Web\Customer\CreateShipmentController::class, 'create'])->name('kirim');
    Route::post('/kirim', [\App\Http\Controllers\Web\Customer\CreateShipmentController::class, 'store'])->name('kirim.store');
});

/*
|--------------------------------------------------------------------------
| Web Routes — Pembayaran Customer (guard `customer`)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:customer', 'verified.customer'])->group(function () {
    Route::get('/shipments/{shipment}/payment', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/shipments/{shipment}/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::post('/shipments/{shipment}/payment/sync', [PaymentController::class, 'sync'])->name('payment.sync');
});
Route::get('/pembayaran/{midtrans_order_id}/selesai', fn () => view('payment.finished'))->name('payment.finished');

/*
|--------------------------------------------------------------------------
| Web Routes — Kurir (guard `web`, role `courier`)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'role:courier'])->prefix('courier')->name('courier.')->group(function () {
    Route::get('/', [CourierController::class, 'index'])->name('index');
    Route::get('/shipments/{shipment}', [CourierController::class, 'show'])->name('shipments.show');
    Route::patch('/shipments/{shipment}/status', [CourierController::class, 'updateStatus'])->name('shipments.update-status');
});

/*
|--------------------------------------------------------------------------
| Web Routes — Dashboard Admin/Manager/Cashier (guard `web`, Blade + Alpine)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'role:admin,manager,cashier'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard: semua role manajerial boleh lihat ringkasan (read-only stats).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Operasional harian (input shipment, assign kurir) — TUGAS Admin & Kasir.
    // Manager sengaja TIDAK dimasukkan di sini: dia cuma pemantau/pelapor,
    // bukan yang mengoperasikan shipment sehari-hari.
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
        Route::get('/shipments/search-customers', [ShipmentController::class, 'searchCustomers'])->name('shipments.search-customers');
        Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
        Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');
        Route::patch('/shipments/{shipment}/assign-courier', [ShipmentController::class, 'assignCourier'])->name('shipments.assign-courier');
    });

    // Verifikasi pembayaran (cash) — TUGAS Admin & Kasir.
    // Menampilkan daftar pembayaran pending dan tombol konfirmasi.
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::patch('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    });

    // Master data (tarif & kendaraan) — TUGAS Admin saja. Kasir & Manager
    // tidak boleh ubah tarif/armada, cuma pakai data yang sudah ada.
    Route::middleware('role:admin')->group(function () {
        Route::get('/rates', [RateController::class, 'index'])->name('rates.index');
        Route::post('/rates', [RateController::class, 'store'])->name('rates.store');
        Route::put('/rates/{rate}', [RateController::class, 'update'])->name('rates.update');
        Route::delete('/rates/{rate}', [RateController::class, 'destroy'])->name('rates.destroy');

        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');

        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    });

    // Laporan & grafik — TUGAS UTAMA Manager (view + export Excel/PDF),
    // Admin juga boleh akses untuk oversight.
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
});
