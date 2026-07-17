<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminder harian kendaraan (STNK/servis jatuh tempo) — lihat
// App\Console\Commands\SendVehicleMaintenanceReminders (Modul 6).
Schedule::command('drg:vehicle-reminders')->dailyAt('07:00');
