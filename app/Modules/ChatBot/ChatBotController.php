<?php

namespace App\Modules\ChatBot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatBotController extends Controller
{

    public $tokens = [
        'grok' => [
            'url' => 'https://api.groq.com/openai/v1/chat/completions',
            'token' => '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'
        ],
        'deepSeek' => [
            'url' => 'https://api.deepseek.com/openai/v1/chat/completions', // ✅ عدلت URL
            'token' => 'sk-ea2e0570e8cd4b8d91c1f3fe40e97c69'
        ],
    ];

    public function index()
    {
        return view('chat');
    }

    public function send(Request $request)
    {
        $userMessage = $request->input('message');

        $prompt = "حلل الرسالة التالية: ($userMessage)
                    إذا كانت تتعلق ب الجدول . أجب بـ 1
                    إذا كانت تسأل عن مادة معينة، أجب بـ 2 واسم المادة.
                    إذا كانت تسأل عن مكان المحاضرة ، أجب بـ 3 واسم المادة.
                    إذا كانت تسأل عن مكان السكشن، أجب بـ 4 واسم المادة.
                    إذا كانت تسأل عن مكان المعمل، أجب بـ 5 واسم المادة.
                    إذا كانت تسأل عن مكان قاعة، أجب بـ 6 واسم او كود القاعة.
                    إذا لم تكن أي من هذه، أجب بـ 0.
                    اديني الطلب بالشكل المطلوب من غير أي شرح، مش عايزك ترد عليا غير بالشكل المطلوب فقط بدون أي كلام إضافي.";

        // ✅ استخدم موديل صح مع Groq
        $response = $this->chatModel('grok', 'llama-3.3-70b-versatile', $prompt);

        $data = $response->json();

        // احصل على رد الـ AI
        $reply = $data['choices'][0]['message']['content'] ?? 'عذرًا، حدث خطأ ما.';

        return response()->json([
            'reply' => trim($reply),
        ]);
    }

    public function chatModel($provider, $model, $prompt, $timeout = 120)
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
}
