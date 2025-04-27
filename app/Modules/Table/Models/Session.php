<?php

namespace App\Modules\Table\Models;

use App\Modules\Halls\Hall;
use App\Models\CourseDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'course_detail_id',
        'hall_id',
        'attendance',
        'day',
        'date',
        'from',
        'to',
        'week',
    ];


    public function details()
    {
        return $this->belongsTo(
            CourseDetail::class,
            'course_detail_id'
        )->with('course', 'teacher.user', 'semester','department');
    }
    
    public function hall()
    {
        return $this->belongsTo(Hall::class)->with('building');
    }

    public function scopeStudent($query)
    {
        $user = auth()->user();
        $query->whereHas('details', function ($q) use ($user) {
            $q->where('semester_id', $user->students->semester_id);
            $q->where('department_id', $user->students->department_id);
        });
    }

    public function scopeTeacher($query)
    {
        $user = auth()->user()->load(['teachers','teachers.departments','teachers.semesters']);
        $query->whereHas('details', function ($q) use ($user) {
            $q->whereIn('department_id', $user->teachers->departments->pluck('id'));
            $q->whereIn('semester_id', $user->teachers->semesters->pluck('id'));
        });
    }

    public function scopeFilter(Builder $builder, $filterBy)
    {
        $builder->when($filterBy['department'] ?? null,function ($builder,$value){
            $builder->whereHas('details',function ($query) use($value){
                $query->where('department_id',$value);
            });
        });
        $builder->when($filterBy['semester'] ?? null,function ($builder,$value){
            $builder->whereHas('details',function ($query) use($value){
                $query->where('semester_id',$value);
            });
        });
    }


    public function scopeOrderByDepartmentAndSemester($query)
    {
        return $query->join('course_details', 'sessions.course_detail_id', '=', 'course_details.id')
            ->join('departments', 'course_details.department_id', '=', 'departments.id')
            ->join('semesters', 'course_details.semester_id', '=', 'semesters.id')
            ->orderBy('departments.id')
            ->orderBy('semesters.id')
            ->select('sessions.*');
    }
    

}
