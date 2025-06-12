<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departements = [
            [
                'name' => 'Pers',
                'description' => 'Departemen Manajemen dan Administrasi Personel',
                'status' => 'active',
            ],
            [
                'name' => 'Diploma',
                'description' => 'Departemen Diploma',
                'status' => 'active',
            ],
            [
                'name' => 'Sarjana',
                'description' => 'Departemen Sarjana',
                'status' => 'active',
            ],
        ];

        foreach ($departements as $dept) {
            Departement::firstOrCreate(['name' => $dept['name']], $dept);
        }
    }
}
