<?php

namespace App\Services;

use App\Models\AccountClient;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentStatusService
{
    public function getStatus(AccountClient $subscription, ?int $month = null, ?int $year = null): string
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $paid = Payment::where('account_client_id', $subscription->id)
            ->forPeriod($month, $year)
            ->exists();

        if ($paid) {
            return 'paid';
        }

        $dueDate = Carbon::create($year, $month, min($subscription->payment_day, Carbon::create($year, $month)->daysInMonth));
        $today = now()->startOfDay();

        if ($today->lt($dueDate)) {
            return 'upcoming';
        }

        $daysOverdue = $today->diffInDays($dueDate);

        if ($daysOverdue <= 3) {
            return 'due';
        }

        return 'overdue';
    }

    public function getOverdueSubscriptions(): \Illuminate\Support\Collection
    {
        return AccountClient::active()
            ->with(['account.streamingService', 'client'])
            ->get()
            ->filter(fn ($sub) => in_array($this->getStatus($sub), ['due', 'overdue']));
    }

    public function getUpcomingPayments(int $daysAhead = 7): \Illuminate\Support\Collection
    {
        $today = now();
        $targetDay = $today->copy()->addDays($daysAhead)->day;

        return AccountClient::active()
            ->with(['account.streamingService', 'client'])
            ->get()
            ->filter(function ($sub) use ($today, $daysAhead) {
                $dueDate = Carbon::create($today->year, $today->month, min($sub->payment_day, $today->daysInMonth));
                if ($dueDate->lt($today)) {
                    $dueDate->addMonth();
                }
                return $dueDate->diffInDays($today) <= $daysAhead && $this->getStatus($sub) === 'upcoming';
            });
    }
}
