<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'super admin',
            'email' => 'superadmin@gmail.com',
            'type' => 'Super admin',
            'password' => Hash::make('admin123'),
            'remember_token' => Str::random(10),
        ])->assignRole('Super admin');
    }
}
