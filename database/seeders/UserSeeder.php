<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Hamlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initial hamlets for registration options
        $hamlets = [
            ['name' => 'Dusun I Gelam', 'head_name' => 'Sutrisno'],
            ['name' => 'Dusun II Sukamaju', 'head_name' => 'Bambang Irawan'],
            ['name' => 'Dusun III Mekarjaya', 'head_name' => 'Suparman'],
        ];

        foreach ($hamlets as $hamlet) {
            Hamlet::firstOrCreate(
                ['name' => $hamlet['name']],
                ['head_name' => $hamlet['head_name']]
            );
        }

        // 1. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@gelamjaya.test'],
            [
                'name' => 'Administrator Desa',
                'password' => Hash::make('password'),
                'role' => Role::ADMIN,
                'is_active' => true,
                'resident_id' => null,
            ]
        );

        // 2. Akun Staf
        User::updateOrCreate(
            ['email' => 'staf@gelamjaya.test'],
            [
                'name' => 'Staf Pelayanan Desa',
                'password' => Hash::make('password'),
                'role' => Role::STAF,
                'is_active' => true,
                'resident_id' => null,
            ]
        );
    }
}

