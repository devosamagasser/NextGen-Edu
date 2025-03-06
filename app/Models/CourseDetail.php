<?php

namespace App\Models;

use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use App\Modules\Teachers\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function semester()
    {
       return $this->belongsTo(Semester::class);
    }

    public function department()
    {
       return $this->belongsTo(Department::class);
    }

    public function Course()
    {
       return $this->belongsTo(Course::class);
    }
    public function teacher()
    {
       return $this->belongsTo(Teacher::class);
    }

}
