<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('telescope:prune --hours=24')->daily();
Schedule::command('sanctum:prune-expired --hours=24')->daily();
Schedule::command('otp:clean')->daily();
Schedule::command('auth:clear-resets')->everyFifteenMinutes();