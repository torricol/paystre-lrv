<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\AccountClient;
use App\Models\Client;
use Livewire\Component;

class SubscriptionManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public int $account_id = 0;
    public int $client_id = 0;
    public string $slot_label = '';
    public string $pin = '';
    public string $client_price = '';
    public string $currency = 'BOB';
    public int $payment_day = 1;
    public string $started_at = '';
    public string $status = 'active';
    public string $search = '';

    protected function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'client_id' => 'required|exists:clients,id',
            'slot_label' => 'nullable|string|max:50',
            'pin' => 'nullable|string|max:20',
            'client_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_day' => 'required|integer|min:1|max:31',
            'started_at' => 'required|date',
        ];
    }

    public function render()
    {
        $subscriptions = AccountClient::with(['account.streamingService', 'client'])
            ->active()
            ->when($this->search, function ($q) {
                $q->whereHas('client', fn ($c) => $c->where('name', 'ilike', "%{$this->search}%"))
                  ->orWhereHas('account', fn ($a) => $a->where('label', 'ilike', "%{$this->search}%"));
            })
            ->orderBy('account_id')
            ->get();

        $accounts = Account::with('streamingService')->where('status', 'active')->orderBy('label')->get();
        $clients = Client::where('is_active', true)->orderBy('name')->get();

        return view('livewire.subscription-manager', compact('subscriptions', 'accounts', 'clients'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->started_at = now()->format('Y-m-d');
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $sub = AccountClient::findOrFail($id);
        $this->editingId = $sub->id;
        $this->account_id = $sub->account_id;
        $this->client_id = $sub->client_id;
        $this->slot_label = $sub->slot_label ?? '';
        $this->pin = $sub->pin ?? '';
        $this->client_price = $sub->client_price;
        $this->currency = $sub->currency;
        $this->payment_day = $sub->payment_day;
        $this->started_at = $sub->started_at->format('Y-m-d');
        $this->status = $sub->status;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'account_id' => $this->account_id,
            'client_id' => $this->client_id,
            'slot_label' => $this->slot_label ?: null,
            'pin' => $this->pin ?: null,
            'client_price' => $this->client_price,
            'currency' => $this->currency,
            'payment_day' => $this->payment_day,
            'started_at' => $this->started_at,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            AccountClient::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Suscripcion actualizada.');
        } else {
            AccountClient::create($data);
            session()->flash('message', 'Suscripcion creada.');
        }

        $this->resetForm();
    }

    public function endSubscription(int $id): void
    {
        AccountClient::findOrFail($id)->update([
            'ended_at' => now()->format('Y-m-d'),
            'status' => 'suspended',
        ]);
        session()->flash('message', 'Suscripcion finalizada.');
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->account_id = 0;
        $this->client_id = 0;
        $this->slot_label = '';
        $this->pin = '';
        $this->client_price = '';
        $this->currency = 'BOB';
        $this->payment_day = 1;
        $this->started_at = '';
        $this->status = 'active';
    }
}
