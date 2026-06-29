<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TankNotificationController extends Controller
{
    public function send(Request $request, Tank $tank)
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $chatId = $request->user()->telegram_chat_id;
        $token = config('services.telegram.token');

        if (!$chatId || !$token) {
            return response()->json(['status' => 'skipped']);
        }

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $data['message'],
        ]);

        return response()->json(['status' => 'sent']);
    }
}
