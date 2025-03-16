<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected $telegramService;
    
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    
    public function handleWebhook(Request $request)
    {
        $update = $request->all();
        
        Log::info('Telegram webhook received', ['update' => $update]);
        
        return $this->telegramService->handleUpdate($update);
    }
    
    public function setWebhook(Request $request)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $webhookUrl = url('/api/telegram/webhook');
        
        $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";
        $response = \Illuminate\Support\Facades\Http::post($apiUrl, [
            'url' => $webhookUrl,
        ]);
        
        return response()->json([
            'status' => $response->successful() ? 'success' : 'error',
            'webhook_url' => $webhookUrl,
            'response' => $response->json(),
        ]);
    }
} 