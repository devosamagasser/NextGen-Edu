<?php

namespace App\Modules\Quizzes\Models;


use App\Models\Semester;
use App\Models\CourseDetail;
use App\Modules\Courses\Course;
use App\Modules\Teachers\Teacher;
use App\Modules\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Questions\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [        
        'teacher_id',
        'course_detail_id',
        'title',
        'description',
        'total_degree',
        'date', 
        'start_time', 
        'duration',
        'status'
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_questions', 'quiz_id', 'question_id')
        ->with('answers')
        ->withPivot('degree');
    }

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

    public function scopeFilter($query)
    {
        $query->when(request()->course, function($q, $value){
            return $q->where('course_detail_id',$value);
        })->when(request()->status, function($q){
            $q->where('status',request()->status);
        });
    }
}
