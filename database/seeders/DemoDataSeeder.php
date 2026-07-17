<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Rate;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 5 cabang tersebar di kota besar Indonesia.
        $jakarta = Branch::create(['name' => 'Cabang Jakarta Pusat', 'city' => 'Jakarta', 'address' => 'Jl. Sudirman No. 1', 'phone' => '021-5551234']);
        $bandung = Branch::create(['name' => 'Cabang Bandung', 'city' => 'Bandung', 'address' => 'Jl. Asia Afrika No. 10', 'phone' => '022-5559876']);
        $surabaya = Branch::create(['name' => 'Cabang Surabaya', 'city' => 'Surabaya', 'address' => 'Jl. Tunjungan No. 22', 'phone' => '031-5553456']);
        $medan = Branch::create(['name' => 'Cabang Medan', 'city' => 'Medan', 'address' => 'Jl. Gatot Subroto No. 45', 'phone' => '061-5557890']);
        $makassar = Branch::create(['name' => 'Cabang Makassar', 'city' => 'Makassar', 'address' => 'Jl. Ahmad Yani No. 12', 'phone' => '0411-555321']);

        $branches = [$jakarta, $bandung, $surabaya, $medan, $makassar];

        // Tarif antar-cabang (2 arah, biar dropdown "Kirim Paket" selalu ada rute-nya).
        $routePrices = [
            ['Jakarta', 'Bandung', 9000, 2],
            ['Jakarta', 'Surabaya', 15000, 3],
            ['Jakarta', 'Medan', 22000, 4],
            ['Jakarta', 'Makassar', 25000, 5],
            ['Bandung', 'Surabaya', 17000, 3],
            ['Bandung', 'Medan', 24000, 4],
            ['Bandung', 'Makassar', 26000, 5],
            ['Surabaya', 'Medan', 26000, 4],
            ['Surabaya', 'Makassar', 20000, 3],
            ['Medan', 'Makassar', 28000, 5],
        ];

        foreach ($routePrices as [$origin, $destination, $price, $days]) {
            Rate::create(['origin_city' => $origin, 'destination_city' => $destination, 'price_per_kg' => $price, 'estimated_days' => $days]);
            Rate::create(['origin_city' => $destination, 'destination_city' => $origin, 'price_per_kg' => $price, 'estimated_days' => $days]);
        }

        // Demo staf per role (semua di cabang Jakarta).
        $admin = User::create(['name' => 'Admin drgEkspedisi', 'email' => 'admin@drgekspedisi.id', 'password' => Hash::make('Password123'), 'role' => 'admin', 'branch_id' => $jakarta->id, 'email_verified_at' => now()]);
        $manager = User::create(['name' => 'Manager Jakarta', 'email' => 'manager@drgekspedisi.id', 'password' => Hash::make('Password123'), 'role' => 'manager', 'branch_id' => $jakarta->id, 'email_verified_at' => now()]);
        $kasir = User::create(['name' => 'Kasir Jakarta', 'email' => 'cashier@drgekspedisi.id', 'password' => Hash::make('Password123'), 'role' => 'cashier', 'branch_id' => $jakarta->id, 'email_verified_at' => now()]);
        $kurir = User::create(['name' => 'Kurir Jakarta', 'email' => 'courier@drgekspedisi.id', 'password' => Hash::make('Password123'), 'role' => 'courier', 'branch_id' => $jakarta->id, 'email_verified_at' => now()]);

        Vehicle::create(['plate_number' => 'B 1234 ABC', 'type' => 'motor', 'courier_id' => $kurir->id]);

        // Demo customer (sender & receiver) — akun percobaan buat login,
        // TANPA shipment/payment dummy (biar dashboard-nya kosong/bersih
        // sampai customer beneran kirim paket sendiri).
        $sender = Customer::create([
            'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'password' => Hash::make('Password123'),
            'address' => 'Jl. Melati No. 5', 'city' => 'Jakarta', 'phone' => '081234567890', 'email_verified_at' => now(),
        ]);

        Customer::create([
            'name' => 'Siti Aminah', 'email' => 'siti@example.com', 'password' => Hash::make('Password123'),
            'address' => 'Jl. Merdeka No. 8', 'city' => 'Bandung', 'phone' => '081298765432', 'email_verified_at' => now(),
        ]);
    }
}
