<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderReceiptService
{
    public function handlePaidOrder(Order $order): void
    {
        if ($order->status !== 'paid') {
            return;
        }

        $order->loadMissing('user');

        $shouldSave = false;

        if (!$order->paid_at) {
            $order->paid_at = now();
            $shouldSave = true;
        }

        if (!$order->receipt_url && $order->payment_intent_id) {
            $receiptUrl = $this->fetchReceiptUrl($order->payment_intent_id);
            if ($receiptUrl) {
                $order->receipt_url = $receiptUrl;
                $shouldSave = true;
            }
        }

        if ($shouldSave) {
            $order->save();
        }

        if ($order->receipt_sent_at || !$order->user || !$order->user->telegram_chat_id) {
            return;
        }

        $message = "Payment received for Order #{$order->id}.";
        if ($order->receipt_url) {
            $message .= " Receipt: {$order->receipt_url}";
        }

        if ($this->sendTelegramMessage($order->user->telegram_chat_id, $message)) {
            $order->receipt_sent_at = now();
            $order->save();
        }
    }

    private function fetchReceiptUrl(string $paymentIntentId): ?string
    {
        $secret = config('services.stripe.secret');
        if (!$secret) {
            return null;
        }

        $response = Http::withBasicAuth($secret, '')
            ->get("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}", [
                'expand' => ['charges.data'],
            ]);

        if ($response->ok()) {
            $charge = data_get($response->json(), 'charges.data.0');
            $receiptUrl = $charge['receipt_url'] ?? null;
            if ($receiptUrl) {
                return $receiptUrl;
            }

            $latestCharge = data_get($response->json(), 'latest_charge');
            if ($latestCharge) {
                return $this->fetchReceiptFromCharge($latestCharge, $secret);
            }
        }

        return null;
    }

    private function fetchReceiptFromCharge(string $chargeId, string $secret): ?string
    {
        $response = Http::withBasicAuth($secret, '')
            ->get("https://api.stripe.com/v1/charges/{$chargeId}");

        if (!$response->ok()) {
            return null;
        }

        return $response->json()['receipt_url'] ?? null;
    }

    private function sendTelegramMessage(string $chatId, string $text): bool
    {
        $token = config('services.telegram.token');
        if (!$token) {
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);

        return $response->ok();
    }
}
