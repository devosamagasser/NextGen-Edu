<?php

namespace App\Modules\ChatBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatBotController extends Controller
{

    public function index()
    {
        return view('chat');
    }

    public function send(Request $request)
    {
        $userMessage = $request->input('message');

        // كود استدعاء OpenRouter API
        $response = Http::timeout(120)->withHeaders([
            "Content-Type: application/json",
            'Authorization' => 'Bearer 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',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'meta-llama/llama-4-maverick:free', 
            'messages' => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);


        $data = $response->json();

        // احصل على رد الـ AI
        $reply = $data['choices'][0]['message']['content'] ?? 'عذرًا، حدث خطأ ما.';
        // $reply = $data;

        return response()->json([
            'reply' => $reply,
        ]);
    }
}
