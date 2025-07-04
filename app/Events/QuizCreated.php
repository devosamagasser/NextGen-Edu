<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Modules\Quizzes\Models\Quiz;

class QuizCreated
{
    use Dispatchable, SerializesModels;

    public $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }
} 