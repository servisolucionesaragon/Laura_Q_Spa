<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name'     => 'Administrador',
                'email'    => 'admin@estetica.local',
                'password' => 'admin123',
                'rol'      => 'admin',
                'telefono' => '+502 0000-1000',
            ],
            [
                'name'     => 'Recepción',
                'email'    => 'recepcion@estetica.local',
                'password' => 'recepcion123',
                'rol'      => 'recepcionista',
                'telefono' => '+502 0000-2000',
            ],
            [
                'name'     => 'María Fernanda',
                'email'    => 'maria@estetica.local',
                'password' => 'profesional123',
                'rol'      => 'profesional',
                'telefono' => '+502 0000-3000',
            ],
        ];

        foreach ($usuarios as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'     => $u['name'],
                    'password' => Hash::make($u['password']),
                    'rol'      => $u['rol'],
                    'telefono' => $u['telefono'],
                    'activo'   => true,
                ]
            );
        }
    }
}
