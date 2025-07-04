<?php

namespace App\Modules\Departments;

use App\Models\CourseDetail;
use App\Modules\Students\Student;
use App\Modules\Teachers\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function courses()
    {
        return $this->hasMany(CourseDetail::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
