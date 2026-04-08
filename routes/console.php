<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-reminders')->dailyAt('09:00');
Schedule::command('app:update-billing-dates')->dailyAt('00:30');
