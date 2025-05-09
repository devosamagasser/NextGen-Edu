<?php

namespace App\Models;

use App\Modules\Courses\Course;
use App\Modules\Students\Student;
use App\Modules\Teachers\Teacher;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function course()
    {
       return $this->belongsTo(Course::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(
            Teacher::class,
            'course_teachers',
            'course_details_id',
            'teacher_id'
        );
    }

}
