<?php

namespace Database\Seeders;

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
        $donorUsers = [
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dewi Kartika',
                'email' => 'dewi.kartika@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Fitriani Sari',
                'email' => 'fitriani.sari@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Gunawan Wijaya',
                'email' => 'gunawan.wijaya@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hesti Lestari',
                'email' => 'hesti.lestari@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Indra Kurniawan',
                'email' => 'indra.kurniawan@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jihan Maharani',
                'email' => 'jihan.maharani@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($donorUsers as $userData) {
            $user = User::create($userData);
            $user->assignRole(User::ROLE_DONOR);
        }

        $this->command->info('Created 10 donor users successfully');

        // Create Super Admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('example'),
            'email_verified_at' => now()
        ]);

        $user->assignRole(User::ROLE_SUPER_ADMIN);

        $this->command->info('Created Super Admin user successfully');
    }
}
