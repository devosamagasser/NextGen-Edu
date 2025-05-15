<?php

namespace App\Modules\ChatBot;

use App\Modules\Courses\Course;
use App\Http\Controllers\Controller;
use App\Modules\Table\TableServices;
use Illuminate\Support\Facades\Http;
use App\Modules\Table\Models\Session;

class ChatBotService extends Controller
{

    public $tokens = [
        'grok' => [
            'url' => 'https://api.groq.com/openai/v1/chat/completions',
            'token' => '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'
        ],
        'deepSeek' => [
            'url' => 'https://api.deepseek.com/openai/v1/chat/completions',
            'token' => 'sk-ea2e0570e8cd4b8d91c1f3fe40e97c69'
        ],
    ];


    public function sendPrompet($provider, $model, $prompt, $timeout = 120)
    {
        if (!isset($this->tokens[$provider])) {
            return response()->json(['error' => 'Invalid provider.']);
        }

        return Http::timeout($timeout)->withHeaders([
            "Content-Type" => "application/json",
            'Authorization' => 'Bearer ' . $this->tokens[$provider]['token'],
        ])->post($this->tokens[$provider]['url'], [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
        ]);
    }


    public function chatResponse($reply, $student)
    {
        $parts = explode(' ', $reply);

        $responseData = [
            1 => fn($student) => $this->getTable($student),
            7 => fn($student) => $this->getMycourses($student),
        ];

        $code = $parts[0] ?? '0';

        if (isset($responseData[$code])) {
            return $responseData[$code]($student);
        }

        return ['reply' => $reply,'code' => 0];
    }


    public function getMycourses($student)
    {
        $semester_id = $student->semester_id;
        $department_id = $student->department_id;
        $courses =  Course::with('semesters','departments','courseDetails')
            ->whereHas('courseDetails',function($q)use($semester_id, $department_id){
                $q->where('semester_id', $semester_id);
                $q->where('department_id', $department_id);
            })->get();

        return [
            'reply' => $courses,
            'code' => 7
        ];
    }

    public function getTable($student)
    {
        $sessions = Session::with(
            'course',
            'semester',
            'department',
            'hall.building'
        )
        ->where('semester_id', $student->semester_id)
        ->where('department_id', $student->department_id)
        ->get();
        return [
            'reply' => (new TableServices())->tableFormat($sessions), 
            'code' => 1
        ];

    }
}
