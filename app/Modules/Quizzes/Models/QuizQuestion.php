<?php

namespace App\Modules\Quizzes\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Questions\Models\Answer;
use App\Modules\Questions\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'question_id', 'degree'];

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
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

}
