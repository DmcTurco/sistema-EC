<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear admin principal
        Admin::create([
            'name' => 'Administrador Principal',
            'email' => 'admin@tienda.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Crear más admins si necesitas
        Admin::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@tienda.com',
            'password' => Hash::make('juan123'),
            'email_verified_at' => now(),
        ]);
    }
}
