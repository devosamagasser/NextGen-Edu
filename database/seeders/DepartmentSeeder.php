<?php

namespace Database\Seeders;

use App\Modules\Departments\Department;
use Illuminate\Database\Seeder;


class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'عام'],
            ['name' => 'هندسة الحاسبات والنظم'],
            ['name' => 'الهندسة المدنية'],
            ['name' => 'الهندسة الميكانيكية'],
            ['name' => 'الهندسة الكهربية'],
            ['name' => 'هندسة الإلكترونيات والاتصالات الكهربية'],
            ['name' => 'هندسة القوى والآلات الكهربية'],
            ['name' => 'هندسة القوى والآلات الميكانيكية'],
            ['name' => 'الهندسة المعمارية'],
            ['name' => 'هندسة الإنتاج والتصميم الميكانيكي'],
            ['name' => 'الهندسة الصناعية'],
        ];
        
        Department::insert($data);
    }
}
