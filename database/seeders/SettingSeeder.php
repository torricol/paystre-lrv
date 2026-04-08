<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'admin_name' => 'Admin',
            'telegram_bot_token' => '',
            'whatsapp_api_url' => 'http://localhost:3000',
            'whatsapp_session' => 'default',
            'reminder_days_before' => '3',
            'overdue_days_warning' => '3',
            'overdue_days_critical' => '7',
            'default_currency' => 'BOB',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
