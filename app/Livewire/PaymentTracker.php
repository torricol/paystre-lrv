<?php

namespace App\Livewire;

use App\Models\AccountClient;
use App\Models\Payment;
use App\Services\PaymentStatusService;
use Carbon\Carbon;
use Livewire\Component;

class PaymentTracker extends Component
{
    public bool $showForm = false;
    public bool $isAdvancePayment = false;
    public bool $isFreeForm = false;
    public int $account_client_id = 0;
    public string $amount = '';
    public string $currency = 'BOB';
    public int $period_month;
    public int $period_year;
    public int $advance_months = 1;
    public string $paid_at = '';
    public string $payment_method = '';
    public string $notes = '';
    public string $search = '';
    public int $viewMonth;
    public int $viewYear;

    public function mount(): void
    {
        $this->viewMonth = now()->month;
        $this->viewYear = now()->year;
        $this->period_month = now()->month;
        $this->period_year = now()->year;
    }

    public function render()
    {
        $paymentService = app(PaymentStatusService::class);

        $subscriptions = AccountClient::with(['account.streamingService', 'client', 'payments'])
            ->active()
            ->when($this->search, function ($q) {
                $q->whereHas('client', fn ($c) => $c->where('name', 'ilike', "%{$this->search}%"))
                  ->orWhereHas('account.streamingService', fn ($s) => $s->where('name', 'ilike', "%{$this->search}%"));
            })
            ->get()
            ->map(function ($sub) use ($paymentService) {
                $sub->payment_status = $paymentService->getStatus($sub, $this->viewMonth, $this->viewYear);
                $sub->current_payment = $sub->payments
                    ->where('period_month', $this->viewMonth)
                    ->where('period_year', $this->viewYear)
                    ->first();

                // Calcular meses pagados por adelantado
                $sub->advance_paid_until = $this->getAdvancePaidUntil($sub);

                return $sub;
            });

        $allSubscriptions = AccountClient::with(['account.streamingService', 'client'])
            ->active()
            ->get();

        return view('livewire.payment-tracker', compact('subscriptions', 'allSubscriptions'));
    }

    public function prevMonth(): void
    {
        if ($this->viewMonth === 1) {
            $this->viewMonth = 12;
            $this->viewYear--;
        } else {
            $this->viewMonth--;
        }
    }

    public function nextMonth(): void
    {
        if ($this->viewMonth === 12) {
            $this->viewMonth = 1;
            $this->viewYear++;
        } else {
            $this->viewMonth++;
        }
    }

    public function newPayment(int $subscriptionId = 0): void
    {
        $this->account_client_id = $subscriptionId;
        $this->amount = '';
        $this->currency = 'BOB';
        $this->period_month = $this->viewMonth;
        $this->period_year = $this->viewYear;
        $this->paid_at = now()->format('Y-m-d\TH:i');
        $this->payment_method = '';
        $this->notes = '';
        $this->isAdvancePayment = false;
        $this->isFreeForm = true;

        if ($subscriptionId) {
            $sub = AccountClient::findOrFail($subscriptionId);
            $this->amount = $sub->client_price;
            $this->currency = $sub->currency;
        }

        $this->showForm = true;
    }

    public function recordPayment(int $subscriptionId): void
    {
        $sub = AccountClient::findOrFail($subscriptionId);
        $this->account_client_id = $subscriptionId;
        $this->amount = $sub->client_price;
        $this->currency = $sub->currency;
        $this->period_month = $this->viewMonth;
        $this->period_year = $this->viewYear;
        $this->paid_at = now()->format('Y-m-d\TH:i');
        $this->payment_method = '';
        $this->notes = '';
        $this->isAdvancePayment = false;
        $this->isFreeForm = false;
        $this->advance_months = 1;
        $this->showForm = true;
    }

    public function recordAdvancePayment(int $subscriptionId): void
    {
        $sub = AccountClient::findOrFail($subscriptionId);
        $this->account_client_id = $subscriptionId;
        $this->currency = $sub->currency;
        $this->paid_at = now()->format('Y-m-d\TH:i');
        $this->payment_method = '';
        $this->notes = '';
        $this->isAdvancePayment = true;
        $this->isFreeForm = false;
        $this->advance_months = 2;

        // Encontrar el primer mes sin pagar desde el mes actual
        $nextUnpaid = $this->findNextUnpaidMonth($sub);
        $this->period_month = $nextUnpaid['month'];
        $this->period_year = $nextUnpaid['year'];
        $this->amount = (string) ($sub->client_price * $this->advance_months);

        $this->showForm = true;
    }

    public function updatedAccountClientId(): void
    {
        if ($this->isFreeForm && $this->account_client_id) {
            $sub = AccountClient::find($this->account_client_id);
            if ($sub) {
                $this->amount = $sub->client_price;
                $this->currency = $sub->currency;
            }
        }
    }

    public function updatedAdvanceMonths(): void
    {
        if ($this->isAdvancePayment && $this->account_client_id) {
            $sub = AccountClient::find($this->account_client_id);
            if ($sub) {
                $this->amount = (string) ($sub->client_price * $this->advance_months);
            }
        }
    }

    public function save(): void
    {
        $this->validate([
            'account_client_id' => 'required|exists:account_client,id',
            'amount' => 'required|numeric|min:0',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020',
            'paid_at' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
        ]);

        if ($this->isAdvancePayment && $this->advance_months > 1) {
            $sub = AccountClient::findOrFail($this->account_client_id);
            $pricePerMonth = $sub->client_price;
            $date = Carbon::create($this->period_year, $this->period_month, 1);
            $created = 0;

            for ($i = 0; $i < $this->advance_months; $i++) {
                $exists = Payment::where('account_client_id', $this->account_client_id)
                    ->where('period_month', $date->month)
                    ->where('period_year', $date->year)
                    ->exists();

                if (!$exists) {
                    Payment::create([
                        'account_client_id' => $this->account_client_id,
                        'amount' => $pricePerMonth,
                        'currency' => $this->currency,
                        'period_month' => $date->month,
                        'period_year' => $date->year,
                        'paid_at' => $this->paid_at,
                        'payment_method' => $this->payment_method ?: null,
                        'notes' => ($this->notes ? $this->notes . ' | ' : '') . "Pago adelantado ({$this->advance_months} meses)",
                    ]);
                    $created++;
                }

                $date->addMonth();
            }

            session()->flash('message', "Pago adelantado registrado: {$created} meses.");
        } else {
            Payment::create([
                'account_client_id' => $this->account_client_id,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'period_month' => $this->period_month,
                'period_year' => $this->period_year,
                'paid_at' => $this->paid_at,
                'payment_method' => $this->payment_method ?: null,
                'notes' => $this->notes ?: null,
            ]);

            session()->flash('message', 'Pago registrado.');
        }

        $this->showForm = false;
    }

    public function deletePayment(int $paymentId): void
    {
        Payment::findOrFail($paymentId)->delete();
        session()->flash('message', 'Pago eliminado.');
    }

    private function findNextUnpaidMonth(AccountClient $sub): array
    {
        $date = Carbon::create($this->viewYear, $this->viewMonth, 1);

        for ($i = 0; $i < 24; $i++) {
            $exists = Payment::where('account_client_id', $sub->id)
                ->where('period_month', $date->month)
                ->where('period_year', $date->year)
                ->exists();

            if (!$exists) {
                return ['month' => $date->month, 'year' => $date->year];
            }

            $date->addMonth();
        }

        return ['month' => now()->month, 'year' => now()->year];
    }

    private function getAdvancePaidUntil(AccountClient $sub): ?string
    {
        $date = Carbon::create(now()->year, now()->month, 1)->addMonth();

        $lastPaid = null;
        for ($i = 0; $i < 12; $i++) {
            $exists = $sub->payments
                ->where('period_month', $date->month)
                ->where('period_year', $date->year)
                ->count() > 0;

            if ($exists) {
                $lastPaid = $date->translatedFormat('M Y');
            } else {
                break;
            }

            $date->addMonth();
        }

        return $lastPaid;
    }
}
