<?php

namespace App\Exports;

use App\Modules\Students\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StudentsExport implements FromView
{
    public function view(): View
    {
        $students = Student::with('user','department','semester')->filter(request()->query())->get();
        return view('exports.students', compact('students'));
    }
}
