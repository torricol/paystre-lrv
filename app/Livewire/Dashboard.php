<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\AccountClient;
use App\Models\Client;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Services\PaymentStatusService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $paymentService = app(PaymentStatusService::class);

        $activeAccounts = Account::where('status', 'active')->count();
        $activeClients = Client::where('is_active', true)->count();
        $totalCost = Account::where('status', 'active')->sum('cost');

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $revenueThisMonth = Payment::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->sum('amount');

        $subscriptions = AccountClient::active()
            ->with(['account.streamingService', 'client'])
            ->get();

        $overdue = $subscriptions->filter(fn ($s) => in_array($paymentService->getStatus($s), ['due', 'overdue']));
        $upcoming = $paymentService->getUpcomingPayments(7);

        $recentNotifications = NotificationLog::with('client')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', compact(
            'activeAccounts', 'activeClients', 'totalCost', 'revenueThisMonth',
            'overdue', 'upcoming', 'recentNotifications'
        ));
    }
}
