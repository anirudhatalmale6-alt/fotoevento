<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador (el fotógrafo). Cambia la clave tras el primer ingreso.
        User::updateOrCreate(
            ['email' => 'joel@fotoevento.com'],
            ['name' => 'Joel Garate', 'password' => Hash::make('fotoevento2025')],
        );
    }
}
