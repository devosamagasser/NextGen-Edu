<?php

namespace App\Modules\Teachers;

use App\Models\CourseDetail;
use App\Models\User;
use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function courseDetails()
    {
        return $this->belongsToMany(
            CourseDetail::class,
            'course_teachers',
            'teacher_id',
            'course_details_id'
        );
    }

    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            CourseDetail::class,
            'id', // foreign key on course_details table
            'id', // foreign key on courses table
            'id', // local key on teachers table
            'course_id' // local key on course_details table
        );
    }

    public function semesters()
    {
        return $this->courseDetails()->with('semester')->get()->pluck('semester')->unique('id');
    }

    public function departments()
    {
        return $this->courseDetails()->with('department')->get()->pluck('department')->unique('id');
    }

    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null, function ($builder, $value) {
                $builder->where('department_id', $value);
        });

    }
}
