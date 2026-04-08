<?php

namespace App\Services\Channels;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class TelegramSender implements ChannelInterface
{
    private ?string $error = null;

    public function send(string $recipient, string $message): bool
    {
        $this->error = null;
        $token = Setting::get('telegram_bot_token');

        if (!$token) {
            $this->error = 'Token de bot de Telegram no configurado';
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $recipient,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if ($response->successful() && $response->json('ok')) {
            return true;
        }

        $this->error = $response->json('description', 'Error desconocido de Telegram');
        return false;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
