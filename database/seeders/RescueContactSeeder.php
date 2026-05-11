<?php

namespace Database\Seeders;

use App\Models\setting;
use Illuminate\Database\Seeder;

class RescueContactSeeder extends Seeder
{
    public function run(): void
    {
        setting::firstOrCreate(
            ['nama' => 'rescue_phone'],
            ['text1' => '+6281234567890', 'text2' => 'Tim SAR TNKS', 'canDelete' => false]
        );

        setting::firstOrCreate(
            ['nama' => 'emergency_phone'],
            ['text1' => '+6281298765432', 'text2' => 'Posko Kerinci', 'canDelete' => false]
        );

        $this->command->info('Rescue contact settings seeded.');
    }
}
