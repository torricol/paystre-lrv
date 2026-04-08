<?php

namespace App\Services\Channels;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class WhatsAppSender implements ChannelInterface
{
    private ?string $error = null;

    public function send(string $recipient, string $message): bool
    {
        $this->error = null;
        $apiUrl = Setting::get('whatsapp_api_url');
        $session = Setting::get('whatsapp_session', 'default');

        if (!$apiUrl) {
            $this->error = 'URL de API de WhatsApp (WAHA) no configurada';
            return false;
        }

        $chatId = $this->formatChatId($recipient);

        $response = Http::post("{$apiUrl}/api/sendText", [
            'chatId' => $chatId,
            'text' => $message,
            'session' => $session,
        ]);

        if ($response->successful()) {
            return true;
        }

        $this->error = $response->json('message', 'Error desconocido de WhatsApp');
        return false;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    private function formatChatId(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!str_ends_with($phone, '@c.us')) {
            $phone .= '@c.us';
        }

        return $phone;
    }
}
