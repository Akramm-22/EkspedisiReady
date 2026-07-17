<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix the payment_status ENUM to include 'expired' and 'refunded' values.
     *
     * MySQL doesn't support ALTER ENUM directly in a portable way, so we
     * use a raw DB statement. For SQLite (testing), we need a different approach.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'expired', 'refunded') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN, so we recreate the table
            DB::statement('PRAGMA foreign_keys=off');

            DB::statement('CREATE TABLE payments_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                shipment_id INTEGER NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                payment_method TEXT NOT NULL CHECK(payment_method IN (\'cash\', \'transfer\', \'e-wallet\')),
                payment_status TEXT NOT NULL DEFAULT \'pending\' CHECK(payment_status IN (\'pending\', \'paid\', \'failed\', \'expired\', \'refunded\')),
                payment_date DATE,
                midtrans_order_id TEXT UNIQUE,
                midtrans_transaction_id TEXT,
                midtrans_snap_token TEXT,
                midtrans_transaction_status TEXT,
                midtrans_raw_response TEXT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
            )');

            DB::statement('INSERT INTO payments_new SELECT * FROM payments');
            DB::statement('DROP TABLE payments');
            DB::statement('ALTER TABLE payments_new RENAME TO payments');
            DB::statement('PRAGMA foreign_keys=on');
        }
        // PostgreSQL and others: handled by the enum cast in the model
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
        }
        // SQLite down migration omitted for simplicity
    }
};