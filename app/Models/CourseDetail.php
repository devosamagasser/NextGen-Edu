<?php

namespace App\Models;

use App\Modules\Announcments\Announcement;
use App\Modules\Assignments\Models\Assignment;
use App\Modules\Courses\Course;
use App\Modules\Teachers\Teacher;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use App\Modules\CourseMaterials\CourseMaterial;
use App\Modules\Quizzes\Models\Quiz;
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

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'course_detail_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'course_detail_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_detail_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'course_detail_id');
    }

}
