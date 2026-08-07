<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'demo.user@zivo.com',
            ],
            [
                'name' => 'Zivo Demo User',
                'password' => Hash::make('ZivoUser123!'),
                'role' => 'customer',
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'demo.admin@zivo.com',
            ],
            [
                'name' => 'Zivo Demo Admin',
                'password' => Hash::make('ZivoAdmin123!'),
                'role' => 'admin',
            ]
        );
    }
}
