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
                'term' => 1
            ],
            [
                'levels' => 2,
                'name' => 'فرقة اغدادية',
                'term' => 2
            ],
            [
                'levels' => 3,
                'name' => 'فرقة اولى',
                'term' => 1
            ],
            [
                'levels' => 4,
                'name' => 'فرقة اولى',
                'term' => 2
            ],
            [
                'levels' => 5,
                'name' => 'فرقة ثانية',
                'term' => 1
            ],
            [
                'levels' => 6,
                'name' => 'فرقة ثانية',
                'term' => 2
            ],
            [
                'levels' => 7,
                'name' => 'فرقة ثالثة',
                'term' => 1
            ],
            [
                'levels' => 8,
                'name' => 'فرقة ثالثة',
                'term' => 2
            ],
            [
                'levels' => 9,
                'name' => 'فرقة رابعة',
                'term' => 1
            ],
            [
                'levels' => 10,
                'name' => 'فرقة رابعة',
                'term' => 2
            ],
        ];
        Semester::insert($data);
    }
}
