<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Customer extends Authenticatable (Laravel base), yang butuh kolom
     * remember_token untuk fitur "remember me" saat login. Kolom ini
     * hilang dari migration awal customers table, menyebabkan error:
     * SQLSTATE[42S22]: Column not found: 1054 Unknown column 'remember_token'.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
