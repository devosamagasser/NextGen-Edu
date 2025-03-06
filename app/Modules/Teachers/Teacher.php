<?php

namespace App\Modules\Teachers;

use App\Models\Semester;
use App\Models\User;
use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'course_details',
            'teacher_id',
            'course_id'
        );
    }

    public function semesters()
    {
        return $this->belongsToMany(
            Semester::class,
            'course_details',
            'teacher_id',
            'semester_id'
        );
    }

    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null,function ($builder,$value){
            $builder->whereHas('department',function ($query) use($value){
                $query->where('department_id',$value);
            });
        });
        $builder->when($filterBy['semester'] ?? null,function ($builder,$value){
            $builder->whereHas('semesters',function ($query) use($value){
                $query->where('semester_id',$value);
            });
        });
        $builder->when($filterBy['course'] ?? null,function ($builder,$value){
            $builder->whereHas('courses',function ($query) use($value){
                $query->where('course_id',$value);
            });
        });
    }
}
