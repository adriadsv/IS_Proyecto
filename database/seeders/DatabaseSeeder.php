<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admins = [
            'michaellopez2230@hotmail.com',
            'carloindemini@gmail.com',
            'adriadsv@gmail.com',
        ];

        foreach ($admins as $email) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Administrador',
                    'password' => '12345',
                ]
            );
        }
    }
}
