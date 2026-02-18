<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function handle(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('WhatsApp Webhook:', $request->all());

        // Basic validation - adjust based on your actual WhatsApp provider's payload
        // Example assumes a simple JSON payload: { "message": "...", "sender": "..." }
        // If using Twilio, the structure is different. I'll make it generic for now.

        $message = $request->input('message') ?? $request->input('body');
        $sender = $request->input('sender') ?? $request->input('from');

        if (!$message) {
            return response()->json(['status' => 'error', 'message' => 'No message found'], 400);
        }

        $responseMessage = $this->whatsAppService->processMessage($message, $sender);

        return response()->json([
            'status' => 'success',
            'reply' => $responseMessage
        ]);
    }
}
