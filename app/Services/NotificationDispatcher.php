<?php

namespace App\Services;

use App\Models\AccountClient;
use App\Models\Client;
use App\Models\MessageTemplate;
use App\Models\NotificationLog;
use App\Services\Channels\ChannelInterface;
use App\Services\Channels\TelegramSender;
use App\Services\Channels\WhatsAppSender;

class NotificationDispatcher
{
    public function __construct(
        private MessageRenderer $renderer,
        private TelegramSender $telegram,
        private WhatsAppSender $whatsapp,
    ) {}

    public function sendFromTemplate(
        Client $client,
        MessageTemplate $template,
        ?AccountClient $subscription = null
    ): array {
        $variables = $subscription
            ? $this->renderer->buildVariables($subscription)
            : ['client_name' => $client->name];

        $message = $this->renderer->render($template->body, $variables);
        $channels = $this->resolveChannels($client);
        $results = [];

        foreach ($channels as $channelName) {
            $channel = $this->getChannel($channelName);
            $recipient = $this->getRecipient($client, $channelName);

            if (!$recipient) {
                $results[] = $this->logNotification($client, $subscription, $template, $channelName, '', $message, 'failed', "Sin {$channelName} configurado para el cliente");
                continue;
            }

            $success = $channel->send($recipient, $message);
            $results[] = $this->logNotification(
                $client,
                $subscription,
                $template,
                $channelName,
                $recipient,
                $message,
                $success ? 'sent' : 'failed',
                $success ? null : $channel->getError()
            );
        }

        return $results;
    }

    public function sendDirect(Client $client, string $message, ?string $channelOverride = null): array
    {
        $channels = $channelOverride ? [$channelOverride] : $this->resolveChannels($client);
        $results = [];

        foreach ($channels as $channelName) {
            $channel = $this->getChannel($channelName);
            $recipient = $this->getRecipient($client, $channelName);

            if (!$recipient) {
                $results[] = $this->logNotification($client, null, null, $channelName, '', $message, 'failed', "Sin {$channelName} configurado para el cliente");
                continue;
            }

            $success = $channel->send($recipient, $message);
            $results[] = $this->logNotification(
                $client,
                null,
                null,
                $channelName,
                $recipient,
                $message,
                $success ? 'sent' : 'failed',
                $success ? null : $channel->getError()
            );
        }

        return $results;
    }

    private function resolveChannels(Client $client): array
    {
        return match ($client->preferred_channel) {
            'whatsapp' => ['whatsapp'],
            'telegram' => ['telegram'],
            'both' => ['whatsapp', 'telegram'],
            default => ['whatsapp'],
        };
    }

    private function getChannel(string $name): ChannelInterface
    {
        return match ($name) {
            'telegram' => $this->telegram,
            'whatsapp' => $this->whatsapp,
            default => $this->whatsapp,
        };
    }

    private function getRecipient(Client $client, string $channel): ?string
    {
        return match ($channel) {
            'telegram' => $client->telegram_chat_id,
            'whatsapp' => $client->phone,
            default => null,
        };
    }

    private function logNotification(
        Client $client,
        ?AccountClient $subscription,
        ?MessageTemplate $template,
        string $channel,
        string $recipient,
        string $body,
        string $status,
        ?string $error = null,
    ): NotificationLog {
        return NotificationLog::create([
            'client_id' => $client->id,
            'account_client_id' => $subscription?->id,
            'message_template_id' => $template?->id,
            'channel' => $channel,
            'recipient' => $recipient,
            'body' => $body,
            'status' => $status,
            'error_message' => $error,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }
}
