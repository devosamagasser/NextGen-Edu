<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'levels' => 1,
                'name' => 'فرقة اغدادية',
            ],
            [
                'levels' => 2,
                'name' => 'فرقة اغدادية',
            ],
            [
                'levels' => 3,
                'name' => 'فرقة اولى',
            ],
            [
                'levels' => 4,
                'name' => 'فرقة اولى',
            ],
            [
                'levels' => 5,
                'name' => 'فرقة ثانية',
            ],
            [
                'levels' => 6,
                'name' => 'فرقة ثانية',
            ],
            [
                'levels' => 7,
                'name' => 'فرقة ثالثة',
            ],
            [
                'levels' => 8,
                'name' => 'فرقة ثالثة',
            ],
            [
                'levels' => 9,
                'name' => 'فرقة رابعة',
            ],
            [
                'levels' => 10,
                'name' => 'فرقة رابعة',
            ],
        ];
        Semester::insert($data);
    }
}
