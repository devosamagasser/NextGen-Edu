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
        'course_detail_id',
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

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class, 'course_detail_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query)
    {
        $query->when(request()->course,function($q, $value){
            return $q->where('course_detail_id',$value);
        });
        $query->when(request()->from, function($q, $value){
            $fromDate = now()->subDays($value)->toDateString();
            return $q->where('created_at', '>=', $fromDate);
        });
    }

}
