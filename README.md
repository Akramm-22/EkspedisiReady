# EkspedisiReady Akram
# drgEkspedisi

Backend Laravel 11 untuk platform ekspedisi, disesuaikan dengan **Physical
Data Model (PDM)** yang dibuat system analyst — bukan lagi versi asumsi
awal. Dokumen ini menggantikan README versi sebelumnya yang sudah tidak
akurat.

## ⚠️ Status: disesuaikan dari PDM halaman 1/3

PDM yang diberikan baru halaman 1 dari 3. Semua yang di bawah ini sudah
cocok dengan 9 tabel yang terlihat di halaman itu. Kalau halaman 2-3
punya tabel/kolom tambahan (kemungkinan besar ada — misalnya modul
laporan, notifikasi, atau detail lain), backend ini perlu di-adjust lagi
begitu halaman itu di-upload.

## Perubahan besar dari versi sebelumnya

1. **Customer bukan lagi bagian dari `users`.** Sesuai PDM, `customers`
   punya `password` & `email_verified_at` sendiri — jadi ini entitas
   auth terpisah dengan guard `customer` (lihat `config/auth.php`).
   Staf internal (admin/manager/cashier/courier) tetap di `users` dengan
   guard `web`.
2. **Role jadi kolom enum sederhana** (`users.role`), bukan Spatie
   Permission. Package `spatie/laravel-permission` sudah dihapus dari
   `composer.json`. Pengecekan role pakai `$user->hasRole('admin|manager')`
   (helper custom di model) atau middleware `role:admin,manager`.
3. **Semua tabel yang tidak ada di PDM sudah dihapus**: `services`,
   `banners`, `faqs`, `testimonials`, `company_profile`,
   `branch_schedules`, `contact_messages`, `audit_logs`,
   `notification_logs`, `push_subscriptions`, `offline_sync_queue`,
   `courier_locations`, `vehicle_logs`, `rate_history`, `invoices`.
   Fitur yang bergantung pada tabel-tabel itu (GPS realtime kurir, PWA
   offline sync, push notification, laporan customer, bulk import tarif,
   dst.) **ikut dihapus** — bisa dibangun ulang kalau memang muncul di
   halaman PDM selanjutnya.
4. **Payment disederhanakan total.** PDM tidak punya kolom gateway
   (Midtrans/Xendit token, dsb.) — `payments` cuma
   `payment_method enum('cash','transfer','e-wallet')` dan
   `payment_status enum('pending','paid','failed')`. Jadi pembayaran
   sekarang murni dicatat manual, dikonfirmasi kasir.
5. **Shipment pakai `sender_id`/`receiver_id`** yang dua-duanya FK ke
   `customers` — bukan data pengirim/penerima inline. **Asumsi ini
   belum dikonfirmasi** karena belum ada tabel lain yang cocok jadi
   FK target di halaman 1; kalau di halaman 2-3 ada tabel khusus
   penerima, ini perlu diubah.
6. **Struktur folder & guard:**
   - `guard: web` (tabel `users`) → staf, dashboard Blade di `/admin`,
     kurir di `/courier`.
   - `guard: customer` (tabel `customers`) → login/register di
     `/customer/login`, `/customer/register`, halaman pembayaran.
   - Sanctum tetap dipakai untuk API mobile (`routes/auth.php`,
     `routes/admin.php`, `routes/payment.php`), token bisa dimiliki
     `User` maupun `Customer` (polymorphic, bawaan Sanctum).

## Struktur tabel final (9 tabel, sesuai PDM)

`branches`, `users`, `customers`, `vehicles`, `rates`, `shipments`,
`shipment_items`, `shipment_trackings`, `payments`.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate

# Sanctum butuh migration tokennya sendiri:
php artisan install:api

php artisan migrate
php artisan db:seed   # DemoDataSeeder — 1 shipment contoh + akun demo

npm install
npm run dev
php artisan serve
```

**Akun demo staf** (password semua `Password123`):
| Role | Email |
|---|---|
| admin | admin@drgekspedisi.id |
| manager | manager@drgekspedisi.id |
| cashier | cashier@drgekspedisi.id |
| courier | courier@drgekspedisi.id |

**Akun demo customer:** budi@example.com (sender), siti@example.com
(receiver), password sama.

## Yang masih perlu ditunggu dari PDM halaman 2-3

- Konfirmasi target FK `sender_id`/`receiver_id` di `shipments`.
- Kemungkinan ada tabel laporan/dashboard, notifikasi, atau modul lain
  yang belum kelihatan.
- Kalau ada tabel baru, kabari saya — saya sesuaikan lagi migration,
  model, controller, dan view yang relevan.

## Belum diverifikasi jalan beneran

Semua perubahan ini ditulis tanpa akses PHP/MySQL di sandbox tempat saya
kerja — belum pernah benar-benar di-`migrate` & dites end-to-end. Setelah
kamu jalankan di lokal, kemungkinan ada error kecil (nama relasi meleset,
validasi kurang pas, dll.) yang baru ketahuan saat runtime. Kabari kalau
ketemu error, saya bantu perbaiki.
