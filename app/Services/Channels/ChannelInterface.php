<?php

namespace App\Services\Channels;

interface ChannelInterface
{
    public function send(string $recipient, string $message): bool;

    public function getError(): ?string;
}
