<?php

namespace App\Modules\Assignments\Models;

use App\Models\User;
use App\Models\Semester;
use App\Models\CourseDetail;
use App\Modules\Courses\Course;
use App\Modules\Teachers\Teacher;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_detail_id',
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


    public function semester()
    {
        return $this->hasOneThrough(
            Semester::class, 
            CourseDetail::class, 
            'id',
            'id', 
            'course_detail_id',
            'semester_id' 
        );
    }

    public function department()
    {
        return $this->hasOneThrough(
            Department::class, 
            CourseDetail::class, 
            'id', 
            'id', 
            'course_detail_id',
            'department_id' 
        );
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class, 
            CourseDetail::class, 
            'id', 
            'id', 
            'course_detail_id',
            'course_id' 
        );
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->with('user');
    }

    public function answers()
    {
        return $this->hasMany(AssignmentAnswer::class);
    }

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class);
    }

    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file);
    }

    public function scopeFilter($query)
    {
        $query->when(request()->course,function($q, $value){
            return $q->where('course_detail_id',$value);
        });

        $query->when(request()->status,function($q, $value){
            $q->where('status',$value);
        });
        
        $query->when(request()->from, function($q, $value){
            $fromDate = now()->subDays($value)->toDateString();
            $q->where('created_at', '>=', $fromDate);
        });
    }


    
}
