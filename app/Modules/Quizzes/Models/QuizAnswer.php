<?php

namespace App\Modules\Quizzes\Models;

use App\Modules\Students\Student;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Questions\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAnswer extends Model
{
    use HasFactory;
    protected $fillable = [ 'student_id', 'answer_id', 'quiz_id', 'question_id', 'degree'];

    public $timestamps = false;

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
