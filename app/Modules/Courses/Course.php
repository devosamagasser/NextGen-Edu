<?php

namespace App\Modules\Courses;

use App\Models\Semester;
use App\Modules\Departments\Department;
use App\Modules\Teachers\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];
    /**
     * Define relationship with Semester
     */
    public function semesters()
    {
        return $this->belongsToMany(
            Semester::class,
            'course_details',
            'course_id',
            'semester_id'
        );
    }

    /**
     * Define relationship with Department
     */
    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'course_details',
            'course_id',
            'department_id'
        )->withPivot('semester_id','teacher_id');
    }

    /**
     * Define relationship with Teacher
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'course_details', 'course_id', 'teacher_id');
    }


    /**
     * @param Builder $builder
     * @param $filterBy
     * @return void
     */
    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null,function ($builder,$value){
            $builder->whereHas('departments',function ($query) use($value){
                $query->where('department_id',$value);
            });
        });
        $builder->when($filterBy['semester'] ?? null,function ($builder,$value){
            $builder->whereHas('semesters',function ($query) use($value){
                $query->where('semester_id',$value);
            });
        });
        $builder->when($filterBy['teacher'] ?? null,function ($builder,$value){
            $builder->whereHas('teachers',function ($query) use($value){
                $query->where('teacher_id',$value);
            });
        });
    }
}
