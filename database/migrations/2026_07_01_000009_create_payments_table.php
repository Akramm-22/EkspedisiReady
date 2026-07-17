<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'e-wallet']);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->date('payment_date')->nullable();

            // Kolom tambahan untuk integrasi Midtrans (dipakai kalau
            // payment_method = transfer/e-wallet; kosong untuk cash).
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->text('midtrans_snap_token')->nullable();
            $table->string('midtrans_transaction_status')->nullable();
            $table->json('midtrans_raw_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
