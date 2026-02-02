<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Akun Administrator [cite: 317]
        DB::table('administrators')->insert([
            [
                'username' => 'admin1',
                'password' => Hash::make('hellouniverse1!'), // Password wajib di-hash
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'admin2',
                'password' => Hash::make('hellouniverse2!'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. Buat Akun Developer & Player (Masuk ke tabel users) [cite: 322, 328]
        DB::table('users')->insert([
            // Developer
            [
                'username' => 'dev1',
                'password' => Hash::make('hellobyte1!'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'dev2',
                'password' => Hash::make('hellobyte2!'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Player
            [
                'username' => 'player1',
                'password' => Hash::make('helloworld1!'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'player2',
                'password' => Hash::make('helloworld2!'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}