<?php

namespace App\Modules\Announcments;

use App\Models\User;
use App\Models\Semester;
use App\Models\CourseDetail;
use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'semester_id',
        'course_id',
        'course_details_id',
        'title',
        'body',
        'cover',
        'post_in',
    ];

    protected $casts = [
        'post_in' => 'datetime',
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query)
    {
        $query->when(request()->course,function($q, $value){
            $course = CourseDetail::find($value);
            return $q->where('course_id',$course);
        });
    }

}
