<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Carlo Indemini',
                'email' => 'carloindemini@gmail.com',
                'password' => '12345',
            ],
            [
                'name' => 'Adrian Sanchez',
                'email' => 'adriansanchez@gmail.com',
                'password' => '12345',
            ],
            [
                'name' => 'Michael Lopez',
                'email' => 'michaellopez@gmail.com',
                'password' => '12345',
            ],
        ];

        foreach ($admins as $admin) {
            \App\Models\User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => bcrypt($admin['password']),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✓ Usuarios administradores creados exitosamente');
    }
}
