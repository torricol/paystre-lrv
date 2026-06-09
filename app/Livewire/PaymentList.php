<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterMethod = '';
    public int $filterMonth = 0;
    public int $filterYear = 0;

    public bool $showForm = false;
    public ?int $editingPaymentId = null;
    public string $amount = '';
    public string $currency = 'BOB';
    public int $period_month = 1;
    public int $period_year = 2025;
    public string $paid_at = '';
    public string $payment_method = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->filterYear = now()->year;
        $this->period_month = now()->month;
        $this->period_year = now()->year;
    }

    public function render()
    {
        $payments = Payment::with(['accountClient.client', 'accountClient.account.streamingService'])
            ->when($this->search, fn ($q) => $q->where(fn ($inner) =>
                $inner->whereHas('accountClient.client', fn ($c) =>
                    $c->where('name', 'ilike', "%{$this->search}%"))
                ->orWhereHas('accountClient.account.streamingService', fn ($s) =>
                    $s->where('name', 'ilike', "%{$this->search}%"))
            ))
            ->when($this->filterMethod, fn ($q) => $q->where('payment_method', $this->filterMethod))
            ->when($this->filterMonth > 0, fn ($q) => $q->where('period_month', $this->filterMonth))
            ->when($this->filterYear > 0, fn ($q) => $q->where('period_year', $this->filterYear))
            ->orderBy('paid_at', 'desc')
            ->paginate(25);

        $total = Payment::when($this->search, fn ($q) => $q->where(fn ($inner) =>
                $inner->whereHas('accountClient.client', fn ($c) =>
                    $c->where('name', 'ilike', "%{$this->search}%"))
                ->orWhereHas('accountClient.account.streamingService', fn ($s) =>
                    $s->where('name', 'ilike', "%{$this->search}%"))
            ))
            ->when($this->filterMethod, fn ($q) => $q->where('payment_method', $this->filterMethod))
            ->when($this->filterMonth > 0, fn ($q) => $q->where('period_month', $this->filterMonth))
            ->when($this->filterYear > 0, fn ($q) => $q->where('period_year', $this->filterYear))
            ->sum('amount');

        return view('livewire.payment-list', compact('payments', 'total'));
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterMethod(): void { $this->resetPage(); }
    public function updatedFilterMonth(): void { $this->resetPage(); }
    public function updatedFilterYear(): void { $this->resetPage(); }

    public function editPayment(int $paymentId): void
    {
        $payment = Payment::findOrFail($paymentId);
        $this->editingPaymentId = $paymentId;
        $this->amount = $payment->amount;
        $this->currency = $payment->currency;
        $this->period_month = $payment->period_month;
        $this->period_year = $payment->period_year;
        $this->paid_at = $payment->paid_at->format('Y-m-d\TH:i');
        $this->payment_method = $payment->payment_method ?? '';
        $this->notes = $payment->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:0',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020',
            'paid_at' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $payment = Payment::findOrFail($this->editingPaymentId);

        $conflict = Payment::where('account_client_id', $payment->account_client_id)
            ->where('period_month', $this->period_month)
            ->where('period_year', $this->period_year)
            ->where('id', '!=', $this->editingPaymentId)
            ->exists();

        if ($conflict) {
            $this->addError('period_month', 'Ya existe un pago registrado para este período.');
            return;
        }

        $payment->update([
            'amount' => $this->amount,
            'currency' => $this->currency,
            'period_month' => $this->period_month,
            'period_year' => $this->period_year,
            'paid_at' => $this->paid_at,
            'payment_method' => $this->payment_method ?: null,
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('message', 'Pago actualizado.');
        $this->showForm = false;
        $this->editingPaymentId = null;
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->editingPaymentId = null;
    }

    public function deletePayment(int $paymentId): void
    {
        Payment::findOrFail($paymentId)->delete();
        session()->flash('message', 'Pago eliminado.');
    }
}
