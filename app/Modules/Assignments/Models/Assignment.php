<?php

namespace App\Modules\Assignments\Models;

use App\Models\User;
use App\Models\Semester;
use App\Models\CourseDetail;
use App\Modules\Courses\Course;
use App\Modules\Teachers\Teacher;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'department_id',
        'semester_id',
        'course_details_id',
        'title',
        'description',
        'file',
        'total_degree',
        'deadline',
        'status'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->with('user');
    }


    public function getFileUrlAttribute()
    {
        return config('filesystems.images_url') . $this->file ;
    }

    public function scopeFilter($query)
    {
        $query->when(request()->course,function($q, $value){
            $course = CourseDetail::where('id',$value)->first();
            return $q->where('course_id',$course->course_id);
        });
        $query->when(request()->status,function($q, $value){
            $q->where('status',$value);
        });
        $query->when(request()->from, function($q, $value){
            $fromDate = now()->subDays($value)->toDateString();
            $q->where('date', '>=', $fromDate);
        });
    }


    
}
