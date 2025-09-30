<?php

namespace Database\Seeders;

use App\Models\Seting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seting::create([
            'name' => 'STTAL Surabaya',
            'address' => 'Jl. Merdeka No.123, morokrembangan',
            'latitude' => -7.561055191392689,
            'longitude' => 112.70188092552503,
            'radius_meters' => 150,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'working_days' => json_encode([
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday',
            ]),
            'is_active' => true,
        ]);
    }
}
