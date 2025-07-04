<?php

namespace App\Modules\Courses;

use App\Models\Semester;
use App\Models\CourseDetail;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\CourseMaterials\CourseMaterial;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    protected $casts = [
        'name' => 'string',
        'code' => 'string',
        'description' => 'string',
    ];

    /**
     * Define relationship with CourseDetails
     */
    public function courseDetails()
    {
        return $this->hasMany(CourseDetail::class);
    }

    /**
     * Define relationship with CourseMaterials
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    /**
     * Get all semesters through courseDetails
     */
    public function semesters()
    {
        return $this->hasManyThrough(
            Semester::class,
            CourseDetail::class,
            'course_id',  // Foreign key on CourseDetail
            'id',         // Primary key on Semester
            'id',         // Local key on Course
            'semester_id' // Foreign key on CourseDetail
        )->distinct();
    }

    /**
     * Get all departments through courseDetails
     */
    public function departments()
    {
        return $this->hasManyThrough(
            Department::class,
            CourseDetail::class,
            'course_id',
            'id',
            'id',
            'department_id'
        )->distinct();
    }

    /**
     * Get all teachers through courseDetails->teachers()
     */
    // public function teachers()
    // {
    //     return $this->hasManyThrough(
    //         Teacher::class,
    //         CourseTeacher::class,
    //         'course_details_id', // Foreign key on CourseTeacher
    //         'id',                // Primary key on Teacher
    //         'id',                // Local key on Course
    //         'teacher_id'         // Foreign key on CourseTeacher
    //     )->distinct()
    //      ->whereHas('courseDetails', function ($query) {
    //         $query->whereColumn('courses.id', 'course_details.course_id');
    //      });
    // }

    /**
     * Scope to filter courses by department, semester, or teacher
     */
    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null, function ($builder, $value) {
            $builder->whereHas('courseDetails', function ($query) use ($value) {
                $query->where('department_id', $value);
            });
        });

        $builder->when($filterBy['semester'] ?? null, function ($builder, $value) {
            $builder->whereHas('courseDetails', function ($query) use ($value) {
                $query->where('semester_id', $value);
            });
        });

        // $builder->when($filterBy['teacher'] ?? null, function ($builder, $value) {
        //     $builder->whereHas('courseDetails.teachers', function ($query) use ($value) {
        //         $query->where('teacher_id', $value);
        //     });
        // });
    }
}
