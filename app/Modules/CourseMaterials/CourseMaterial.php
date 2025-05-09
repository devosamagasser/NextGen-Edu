<?php

namespace App\Modules\CourseMaterials;

use App\Models\Semester;
use App\Models\CourseDetail;
use App\Modules\Courses\Course;
use App\Modules\Teachers\Teacher;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_detail_id',
        'title',
        'material',
        'week',
        'type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getMaterialUrlAttribute()
    {
        return Storage::disk('public')->url($this->material);
    }

    public function scopeFilter($query)
    {
        $query->when(request()->has('type'), function ($query,$type) {
            return $query->where('type', $type);
        })->when(request()->has('week'), function ($query,$week) {
            return $query->where('week', $week);
        });
    }
}
