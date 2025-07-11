<?php

namespace App\Exports;

use App\Modules\Teachers\Teacher;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TeachersExport implements FromView
{
    public function view(): View
    {
        $teachers = Teacher::with('user','department')->get();
        return view('exports.teacher', compact('teachers'));
    }
}
