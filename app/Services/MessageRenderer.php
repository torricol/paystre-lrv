<?php

namespace App\Services;

use App\Models\AccountClient;
use App\Models\Client;
use App\Models\Setting;
use Carbon\Carbon;

class MessageRenderer
{
    public function render(string $template, array $variables = []): string
    {
        $replacements = [];
        foreach ($variables as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public function buildVariables(AccountClient $subscription): array
    {
        $account = $subscription->account;
        $client = $subscription->client;
        $service = $account->streamingService;

        $dueDate = Carbon::create(now()->year, now()->month, min($subscription->payment_day, now()->daysInMonth));
        if ($dueDate->lt(now())) {
            $dueDate->addMonth();
        }

        return [
            'client_name' => $client->name,
            'service_name' => $service->name,
            'account_label' => $account->label,
            'amount' => number_format($subscription->client_price, 2) . ' ' . $subscription->currency,
            'due_date' => $dueDate->format('d/m/Y'),
            'due_day' => $subscription->payment_day,
            'credentials' => $this->formatCredentials($account, $subscription),
            'month' => ucfirst($dueDate->translatedFormat('F Y')),
            'admin_name' => Setting::get('admin_name', 'Admin'),
        ];
    }

    private function formatCredentials($account, ?AccountClient $subscription = null): string
    {
        $parts = ["Correo: {$account->email}", "Clave: {$account->password}"];

        if ($subscription?->pin) {
            $parts[] = "PIN: {$subscription->pin}";
        }

        return implode("\n", $parts);
    }
}
