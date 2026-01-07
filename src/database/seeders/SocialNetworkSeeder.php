<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialNetworks = [
            [
                'name' => 'Facebook',
                'slug' => 'facebook',
                'icon' => 'fab fa-facebook',
                'color' => '#1877F2',
                'status' => 1,
                'order' => 1
            ],
            [
                'name' => 'Instagram',
                'slug' => 'instagram',
                'icon' => 'fab fa-instagram',
                'color' => '#E4405F',
                'status' => 1,
                'order' => 2
            ],
            [
                'name' => 'X (Twitter)',
                'slug' => 'x',
                'icon' => 'fab fa-x-twitter',
                'color' => '#000000',
                'status' => 1,
                'order' => 3
            ],
            [
                'name' => 'LinkedIn',
                'slug' => 'linkedin',
                'icon' => 'fab fa-linkedin',
                'color' => '#0A66C2',
                'status' => 1,
                'order' => 4
            ],
            [
                'name' => 'WhatsApp',
                'slug' => 'whatsapp',
                'icon' => 'fab fa-whatsapp',
                'color' => '#25D366',
                'status' => 1,
                'order' => 5
            ],
            [
                'name' => 'YouTube',
                'slug' => 'youtube',
                'icon' => 'fab fa-youtube',
                'color' => '#FF0000',
                'status' => 1,
                'order' => 6
            ],
            [
                'name' => 'TikTok',
                'slug' => 'tiktok',
                'icon' => 'fab fa-tiktok',
                'color' => '#000000',
                'status' => 1,
                'order' => 7
            ],
            [
                'name' => 'Telegram',
                'slug' => 'telegram',
                'icon' => 'fab fa-telegram',
                'color' => '#26A5E4',
                'status' => 1,
                'order' => 8
            ],
            [
                'name' => 'Pinterest',
                'slug' => 'pinterest',
                'icon' => 'fab fa-pinterest',
                'color' => '#BD081C',
                'status' => 1,
                'order' => 9
            ],
            [
                'name' => 'Snapchat',
                'slug' => 'snapchat',
                'icon' => 'fab fa-snapchat',
                'color' => '#FFFC00',
                'status' => 1,
                'order' => 10
            ],
            [
                'name' => 'Discord',
                'slug' => 'discord',
                'icon' => 'fab fa-discord',
                'color' => '#5865F2',
                'status' => 1,
                'order' => 11
            ],
            [
                'name' => 'Twitch',
                'slug' => 'twitch',
                'icon' => 'fab fa-twitch',
                'color' => '#9146FF',
                'status' => 1,
                'order' => 12
            ],
            [
                'name' => 'Reddit',
                'slug' => 'reddit',
                'icon' => 'fab fa-reddit',
                'color' => '#FF4500',
                'status' => 1,
                'order' => 13
            ],
            [
                'name' => 'GitHub',
                'slug' => 'github',
                'icon' => 'fab fa-github',
                'color' => '#181717',
                'status' => 1,
                'order' => 14
            ],
            [
                'name' => 'Website',
                'slug' => 'website',
                'icon' => 'fas fa-globe',
                'color' => '#0066CC',
                'status' => 1,
                'order' => 15
            ],
        ];

        foreach ($socialNetworks as $network) {
            \App\Models\SocialNetwork::updateOrCreate(
                ['slug' => $network['slug']],
                $network
            );
        }
    }
}
