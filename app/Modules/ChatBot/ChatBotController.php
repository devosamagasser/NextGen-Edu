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
        // $reply = $data['choices'][0]['message']['content'] ?? 'عذرًا، حدث خطأ ما.';
        $reply = $data;

        return response()->json([
            'reply' => $reply,
        ]);
    }
}
