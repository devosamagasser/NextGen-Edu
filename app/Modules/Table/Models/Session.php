<?php

namespace App\Modules\Table\Models;

use App\Models\Semester;
use App\Modules\Halls\Hall;
use App\Modules\Courses\Course;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'department_id',
        'semester_id',
        'course_id',
        'hall_id',
        'attendance',
        'day',
        'date',
        'from',
        'to',
        'week',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    
    public function hall()
    {
        return $this->belongsTo(Hall::class)->with('building');
    }

    public function scopeStudent($query)
    {
        $user = auth()->user();
        if ($user->type == 'Student') {
            $query->where('semester_id', $user->students->semester_id)
                ->where('department_id', $user->students->department_id);
        }
    }

    public function scopeTeacher($query)
    {
        $user = request()->user();
        if ($user->hasRole('Teacher')) {
            $departments = $user->teachers->courseDetails->pluck('department_id')->unique();
            $semesters = $user->teachers->courseDetails->pluck('semester_id')->unique();
            $query->whereIn('department_id', $departments)
               ->whereIn('semester_id', $semesters);
        }   
    }

    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null,function ($builder,$value){
            $builder->where('department_id',$value);
        });
        $builder->when($filterBy['semester'] ?? null,function ($builder,$value){
            $builder->where('semester_id',$value);
        });
    }


    public function scopeOrderByDepartmentAndSemester($query)
    {
        return $query->join('departments', 'department_id', '=', 'departments.id')
            ->join('semesters', 'semester_id', '=', 'semesters.id')
            ->orderBy('departments.id')
            ->orderBy('semesters.id')
            ->select('sessions.*');
    }
    
    public function postponed()
    {
        return $this->hasOne(\App\Modules\Table\Models\PostponedSession::class, 'session_id');
    }
}
