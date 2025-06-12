<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role superadmin jika belum ada
        $role = Role::firstOrCreate(['name' => 'superadmin']);

        // Buat user superAdmin
        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'), // Ganti dengan password yang aman
            ]
        );

        // Assign role ke user
        $user->assignRole($role);

        // Buat detail user
        UserDetail::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'pangkat' => 'Laksamana',
            'korps' => 'TNI AL',
            'nrp' => '12345678',
            'gender' => 'L',
            'image' => null,
            'address' => 'Jakarta',
            'phone' => '08123456789',
            'birth_date' => '1980-01-01',
            'join_date' => now(),
            'departement_id' => 1,
            'role_id' => $role->id,
            'status' => 'active',
            'salary' => 10000000,
        ]);

    }
}
