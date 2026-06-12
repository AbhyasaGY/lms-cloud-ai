<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function generateSummary(Request $request)
    {
        $request->validate([
            'text_materi' => 'required|string'
        ]);

        $url = 'https://router.huggingface.co/hf-inference/models/facebook/bart-large-cnn';
        $apiKey = config('services.huggingface.key');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'inputs' => $request->text_materi,
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal terhubung ke AI'
        ], 500);
    }
}