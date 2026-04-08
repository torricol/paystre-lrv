<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;

class ClientManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $phone = '';
    public string $telegram_chat_id = '';
    public string $telegram_username = '';
    public string $preferred_channel = 'whatsapp';
    public bool $is_active = true;
    public string $notes = '';
    public string $search = '';

    protected $rules = [
        'name' => 'required|string|max:150',
        'phone' => 'nullable|string|max:20',
        'telegram_chat_id' => 'nullable|string|max:50',
        'telegram_username' => 'nullable|string|max:100',
        'preferred_channel' => 'required|in:whatsapp,telegram,both',
    ];

    public function render()
    {
        $clients = Client::query()
            ->withCount('activeSubscriptions')
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', "%{$this->search}%")
                ->orWhere('phone', 'ilike', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.client-manager', compact('clients'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $client = Client::findOrFail($id);
        $this->editingId = $client->id;
        $this->name = $client->name;
        $this->phone = $client->phone ?? '';
        $this->telegram_chat_id = $client->telegram_chat_id ?? '';
        $this->telegram_username = $client->telegram_username ?? '';
        $this->preferred_channel = $client->preferred_channel;
        $this->is_active = $client->is_active;
        $this->notes = $client->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'telegram_chat_id' => $this->telegram_chat_id ?: null,
            'telegram_username' => $this->telegram_username ?: null,
            'preferred_channel' => $this->preferred_channel,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Client::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Cliente actualizado.');
        } else {
            Client::create($data);
            session()->flash('message', 'Cliente creado.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Client::findOrFail($id)->delete();
        session()->flash('message', 'Cliente eliminado.');
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->name = '';
        $this->phone = '';
        $this->telegram_chat_id = '';
        $this->telegram_username = '';
        $this->preferred_channel = 'whatsapp';
        $this->is_active = true;
        $this->notes = '';
    }
}
