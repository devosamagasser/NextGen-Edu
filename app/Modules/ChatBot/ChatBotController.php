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
        $response = Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer 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',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'deepseek/deepseek-chat-v3-0324:free', 
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
