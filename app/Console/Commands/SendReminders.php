<?php

namespace App\Console\Commands;

use App\Models\AccountClient;
use App\Models\MessageTemplate;
use App\Models\Setting;
use App\Services\NotificationDispatcher;
use App\Services\PaymentStatusService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'app:send-reminders';
    protected $description = 'Enviar recordatorios de pago automaticos';

    public function handle(PaymentStatusService $paymentService, NotificationDispatcher $dispatcher): int
    {
        $daysBefore = (int) Setting::get('reminder_days_before', 3);
        $overdueWarning = (int) Setting::get('overdue_days_warning', 3);
        $overdueCritical = (int) Setting::get('overdue_days_critical', 7);

        $subscriptions = AccountClient::active()
            ->with(['account.streamingService', 'client'])
            ->get();

        $today = now()->day;
        $sent = 0;

        foreach ($subscriptions as $sub) {
            $status = $paymentService->getStatus($sub);
            $dueDate = Carbon::create(now()->year, now()->month, min($sub->payment_day, now()->daysInMonth));
            $daysUntilDue = now()->startOfDay()->diffInDays($dueDate, false);

            $template = null;

            if ($status === 'upcoming' && $daysUntilDue === $daysBefore) {
                $template = MessageTemplate::where('slug', 'reminder_3_days')->active()->first();
            } elseif ($status === 'due' && $daysUntilDue === 0) {
                $template = MessageTemplate::where('slug', 'reminder_due')->active()->first();
            } elseif ($status === 'overdue') {
                $daysOverdue = abs($daysUntilDue);
                if ($daysOverdue === $overdueWarning) {
                    $template = MessageTemplate::where('slug', 'overdue_3_days')->active()->first();
                } elseif ($daysOverdue === $overdueCritical) {
                    $template = MessageTemplate::where('slug', 'overdue_7_days')->active()->first();
                }
            }

            if ($template) {
                $results = $dispatcher->sendFromTemplate($sub->client, $template, $sub);
                $sentCount = collect($results)->where('status', 'sent')->count();
                $sent += $sentCount;
                $this->line("  -> {$sub->client->name} ({$sub->account->streamingService->name}): {$template->name} - {$sentCount} enviados");
            }
        }

        $this->info("Recordatorios enviados: {$sent}");

        return Command::SUCCESS;
    }
}
