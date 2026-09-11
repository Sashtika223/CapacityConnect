<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TraineeAIAssistantController extends Controller
{
    public function __construct(
        protected AIService $aiService
    ) {}

    public function index(): View
    {
        return view('trainee.ai.assistant');
    }

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $query = $request->input('message');
        $answer = $this->aiService->askAssistant($query, Auth::user());

        return response()->json([
            'answer' => $answer,
            'conversation_id' => Auth::user()->aiConversations()->latest()->value('id'),
            'timestamp' => now()->format('H:i'),
        ]);
    }
}
