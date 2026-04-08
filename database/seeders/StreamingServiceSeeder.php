<?php

namespace Database\Seeders;

use App\Models\StreamingService;
use Illuminate\Database\Seeder;

class StreamingServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Netflix', 'icon' => 'tv', 'color' => '#E50914', 'max_slots' => 5, 'website_url' => 'https://netflix.com'],
            ['name' => 'Spotify', 'icon' => 'music', 'color' => '#1DB954', 'max_slots' => 6, 'website_url' => 'https://spotify.com'],
            ['name' => 'Disney+', 'icon' => 'film', 'color' => '#113CCF', 'max_slots' => 4, 'website_url' => 'https://disneyplus.com'],
            ['name' => 'HBO Max', 'icon' => 'tv', 'color' => '#B535F6', 'max_slots' => 5, 'website_url' => 'https://max.com'],
            ['name' => 'Amazon Prime Video', 'icon' => 'video', 'color' => '#00A8E1', 'max_slots' => 6, 'website_url' => 'https://primevideo.com'],
            ['name' => 'Apple TV+', 'icon' => 'monitor', 'color' => '#555555', 'max_slots' => 6, 'website_url' => 'https://tv.apple.com'],
            ['name' => 'YouTube Premium', 'icon' => 'youtube', 'color' => '#FF0000', 'max_slots' => 6, 'website_url' => 'https://youtube.com/premium'],
            ['name' => 'Crunchyroll', 'icon' => 'play-circle', 'color' => '#F47521', 'max_slots' => 4, 'website_url' => 'https://crunchyroll.com'],
            ['name' => 'Paramount+', 'icon' => 'tv', 'color' => '#0064FF', 'max_slots' => 6, 'website_url' => 'https://paramountplus.com'],
            ['name' => 'Star+', 'icon' => 'star', 'color' => '#FF0055', 'max_slots' => 4, 'website_url' => 'https://starplus.com'],
        ];

        foreach ($services as $service) {
            StreamingService::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($service['name'])],
                $service
            );
        }
    }
}
