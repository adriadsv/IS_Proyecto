<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuarios administradores del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creando usuarios administradores...');
        $this->newLine();

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
            $user = User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => bcrypt($admin['password']),
                    'email_verified_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->info("✓ Usuario creado: {$admin['name']} ({$admin['email']})");
            } else {
                $this->warn("⟳ Usuario actualizado: {$admin['name']} ({$admin['email']})");
            }
        }

        $this->newLine();
        $this->info('✓ Proceso completado exitosamente');
        $this->newLine();
        $this->comment('Credenciales:');
        $this->comment('  Email: carloindemini@gmail.com | adriansanchez@gmail.com | michaellopez@gmail.com');
        $this->comment('  Password: 12345');

        return 0;
    }
}
