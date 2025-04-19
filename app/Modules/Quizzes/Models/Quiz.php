<?php

namespace App\Modules\Quizzes\Models;


use App\Models\User;
use App\Models\CourseDetail;
use Illuminate\Support\Carbon;
use App\Modules\Teachers\Teacher;
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

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class)->with(['course','semester','department']);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }


    public function scopeFilter($query)
    {
        $query->when(request()->course,function($q, $value){
            $q->where('course_detail_id',$value);
        });
        $query->when(request()->status,function($q, $value){
            $q->where('status',$value);
        });
        $query->when(request()->from, function($q, $value){
            $fromDate = now()->subDays($value)->toDateString();
            $q->where('date', '>=', $fromDate);
        });
    }
}
