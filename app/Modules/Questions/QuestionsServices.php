<?php

namespace App\Modules\Questions;

use Carbon\Carbon;
use App\Models\User;
use App\Services\Service;
use App\Modules\Teachers\Teacher;
use Illuminate\Support\Facades\DB;
use App\Modules\Questions\Models\Question;

class QuestionsServices extends Service
{

    /**
     * Store a newly created resource in storage.
     */
    public static function addNewQuestion($request)
    {
        return DB::transaction(function () use($request) {

            $question =  Question::create([
                'course_detail_id' => $request['course_id'],
                'question' => $request['question'], 
            ]);

            $data = [];
            foreach ($request['answers'] as $answer) {
                $data[] = [
                    'answer' => $answer['answer'],
                    'is_correct' => $answer['is_correct'],
                ];
            }

            $question->answers()->createMany($data);
            return $question;
        });

    }

}
