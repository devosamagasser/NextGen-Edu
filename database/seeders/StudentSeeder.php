<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'student',
            'email' => 'student@gmail.com',
            'type' => 'Student',
            'password' => Hash::make('student123'),
            'remember_token' => Str::random(10),
        ])->assignRole('Student');
    }
}
