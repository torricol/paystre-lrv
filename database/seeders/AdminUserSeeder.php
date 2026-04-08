<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'lmtf2505@gmail.com'],
            [
                'name' => 'LMTF',
                'password' => Hash::make('lmtf'),
            ]
        );
    }
}
