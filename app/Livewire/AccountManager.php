<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\StreamingService;
use Livewire\Component;

class AccountManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public int $streaming_service_id = 0;
    public string $label = '';
    public string $email = '';
    public string $password = '';
    public string $plan_name = '';
    public string $cost = '';
    public string $currency = 'BOB';
    public int $billing_day = 1;
    public string $next_billing_date = '';
    public int $max_slots = 5;
    public string $status = 'active';
    public string $notes = '';
    public string $search = '';
    public int $filterService = 0;
    public array $showPassword = [];

    protected function rules(): array
    {
        return [
            'streaming_service_id' => 'required|exists:streaming_services,id',
            'label' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|string',
            'plan_name' => 'nullable|string|max:100',
            'cost' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_day' => 'required|integer|min:1|max:31',
            'next_billing_date' => 'required|date',
            'max_slots' => 'required|integer|min:1|max:20',
            'status' => 'required|in:active,suspended,cancelled',
        ];
    }

    public function render()
    {
        $accounts = Account::with(['streamingService', 'activeSubscriptions.client'])
            ->when($this->search, fn ($q) => $q->where('label', 'ilike', "%{$this->search}%")
                ->orWhere('email', 'ilike', "%{$this->search}%"))
            ->when($this->filterService, fn ($q) => $q->where('streaming_service_id', $this->filterService))
            ->orderBy('streaming_service_id')
            ->orderBy('label')
            ->get();

        $services = StreamingService::where('is_active', true)->orderBy('name')->get();

        return view('livewire.account-manager', compact('accounts', 'services'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->next_billing_date = now()->addMonth()->format('Y-m-d');
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $account = Account::findOrFail($id);
        $this->editingId = $account->id;
        $this->streaming_service_id = $account->streaming_service_id;
        $this->label = $account->label;
        $this->email = $account->email;
        $this->password = $account->password;
        $this->plan_name = $account->plan_name ?? '';
        $this->cost = $account->cost;
        $this->currency = $account->currency;
        $this->billing_day = $account->billing_day;
        $this->next_billing_date = $account->next_billing_date->format('Y-m-d');
        $this->max_slots = $account->max_slots;
        $this->status = $account->status;
        $this->notes = $account->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'streaming_service_id' => $this->streaming_service_id,
            'label' => $this->label,
            'email' => $this->email,
            'password' => $this->password,
            'plan_name' => $this->plan_name ?: null,
            'cost' => $this->cost,
            'currency' => $this->currency,
            'billing_day' => $this->billing_day,
            'next_billing_date' => $this->next_billing_date,
            'max_slots' => $this->max_slots,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Account::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Cuenta actualizada.');
        } else {
            Account::create($data);
            session()->flash('message', 'Cuenta creada.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Account::findOrFail($id)->delete();
        session()->flash('message', 'Cuenta eliminada.');
    }

    public function togglePassword(int $id): void
    {
        if (in_array($id, $this->showPassword)) {
            $this->showPassword = array_diff($this->showPassword, [$id]);
        } else {
            $this->showPassword[] = $id;
        }
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->streaming_service_id = 0;
        $this->label = '';
        $this->email = '';
        $this->password = '';
        $this->plan_name = '';
        $this->cost = '';
        $this->currency = 'BOB';
        $this->billing_day = 1;
        $this->next_billing_date = '';
        $this->max_slots = 5;
        $this->status = 'active';
        $this->notes = '';
    }
}
