<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle the chatbot request and proxy it to Open WebUI.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,assistant,system',
            'messages.*.content' => 'required|string',
        ]);

        $apiKey = env('OPEN_WEBUI_API_KEY');
        $baseUrl = env('OPEN_WEBUI_BASE_URL', 'http://localhost:3000/api');
        $model = env('OPEN_WEBUI_MODEL', 'llama3.1');

        if (!$apiKey) {
            return response()->json([
                'error' => 'Chatbot API key not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $request->messages,
            ]);

            if ($response->failed()) {
                Log::error('Open WebUI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'error' => 'Error communicating with the chatbot service.'
                ], $response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Chatbot exception', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'An unexpected error occurred.'
            ], 500);
        }
    }
}
