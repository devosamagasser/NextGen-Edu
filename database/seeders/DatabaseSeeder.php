<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//         \App\Models\User::factory(1)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

       $this->call(RoleSeeder::class);
       $this->call(SuperAdminSeeder::class);
       $this->call(AdminSeeder::class);
       $this->call(StudentSeeder::class);
       $this->call(TeacherSeeder::class);
       $this->call(SemesterSeeder::class); 
    }
}
