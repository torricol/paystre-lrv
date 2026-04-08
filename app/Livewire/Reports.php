<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\AccountClient;
use App\Models\Payment;
use Livewire\Component;

class Reports extends Component
{
    public int $month;
    public int $year;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function render()
    {
        $totalCost = Account::where('status', 'active')->sum('cost');
        $totalRevenue = Payment::where('period_month', $this->month)
            ->where('period_year', $this->year)
            ->sum('amount');

        $activeAccounts = Account::where('status', 'active')->count();
        $activeClients = AccountClient::active()->distinct('client_id')->count('client_id');

        $expectedRevenue = AccountClient::active()->sum('client_price');

        $serviceStats = Account::where('status', 'active')
            ->with('streamingService')
            ->get()
            ->groupBy('streaming_service_id')
            ->map(function ($accounts) {
                $service = $accounts->first()->streamingService;
                $cost = $accounts->sum('cost');
                $subscriptions = AccountClient::active()
                    ->whereIn('account_id', $accounts->pluck('id'))
                    ->get();
                $revenue = $subscriptions->sum('client_price');

                return [
                    'name' => $service->name,
                    'color' => $service->color,
                    'accounts' => $accounts->count(),
                    'clients' => $subscriptions->count(),
                    'cost' => $cost,
                    'revenue' => $revenue,
                    'profit' => $revenue - $cost,
                ];
            })
            ->sortByDesc('profit')
            ->values();

        return view('livewire.reports', compact(
            'totalCost', 'totalRevenue', 'activeAccounts', 'activeClients',
            'expectedRevenue', 'serviceStats'
        ));
    }

    public function prevMonth(): void
    {
        if ($this->month === 1) { $this->month = 12; $this->year--; }
        else { $this->month--; }
    }

    public function nextMonth(): void
    {
        if ($this->month === 12) { $this->month = 1; $this->year++; }
        else { $this->month++; }
    }
}
