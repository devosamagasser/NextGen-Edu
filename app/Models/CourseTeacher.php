<?php

namespace App\Models;

use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use App\Modules\Teachers\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    use HasFactory;

    protected $guarded = ['teacher_id', 'course_details_id'];

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class, 'course_details_id')
            ->with(['course', 'semester', 'department']);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

}
