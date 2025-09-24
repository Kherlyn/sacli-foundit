<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule admin notification commands
Schedule::command('admin:check-pending-alerts')->hourly();
Schedule::command('admin:send-weekly-report')->weekly()->mondays()->at('09:00');
