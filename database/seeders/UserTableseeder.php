<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_id' => 'U0001',
            'name' => 'Developer',
            'role_id' => 'R0001',
            'username' => 'dev',
            'password' => bcrypt('dev123'),
        ]);
        User::create([
            'user_id' => 'U0002',
            'name' => 'Admin',
            'role_id' => 'R0002',
            'username' => 'admin',
            'password' => bcrypt('admin'),
        ]);
        User::create([
            'user_id' => 'U0003',
            'name' => 'Pengguna',
            'role_id' => 'R0003',
            'username' => 'pengguna',
            'password' => bcrypt('pengguna'),
        ]);
    }
}
