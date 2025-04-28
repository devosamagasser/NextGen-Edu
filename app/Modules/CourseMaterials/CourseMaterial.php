<?php

namespace App\Modules\CourseMaterials;

use App\Models\Semester;
use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'department_id',
        'semester_id',
        'course_details_id',
        'title',
        'material',
        'week',
        'type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function getMaterialUrlAttribute()
    {
        return config('filesystems.images_url') . $this->material;
    }

    public function scopeFilter()
    {
        return $this->when(request()->has('type'), function ($query,$type) {
            return $query->where('type', $type);
        })->when(request()->has('week'), function ($query,$week) {
            return $query->where('week', $week);
        });
    }
}
