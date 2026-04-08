<?php

namespace App\Livewire;

use App\Models\StreamingService;
use Livewire\Component;

class StreamingServiceManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $icon = 'tv';
    public string $color = '#6366F1';
    public int $max_slots = 5;
    public string $website_url = '';
    public bool $is_active = true;
    public string $search = '';

    protected $rules = [
        'name' => 'required|string|max:100',
        'icon' => 'nullable|string|max:255',
        'color' => 'nullable|string|max:7',
        'max_slots' => 'required|integer|min:1|max:20',
        'website_url' => 'nullable|url|max:255',
    ];

    public function render()
    {
        $services = StreamingService::query()
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.streaming-service-manager', compact('services'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $service = StreamingService::findOrFail($id);
        $this->editingId = $service->id;
        $this->name = $service->name;
        $this->icon = $service->icon ?? 'tv';
        $this->color = $service->color ?? '#6366F1';
        $this->max_slots = $service->max_slots;
        $this->website_url = $service->website_url ?? '';
        $this->is_active = $service->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'icon' => $this->icon,
            'color' => $this->color,
            'max_slots' => $this->max_slots,
            'website_url' => $this->website_url ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            StreamingService::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Servicio actualizado.');
        } else {
            StreamingService::create($data);
            session()->flash('message', 'Servicio creado.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        StreamingService::findOrFail($id)->delete();
        session()->flash('message', 'Servicio eliminado.');
    }

    public function toggleActive(int $id): void
    {
        $service = StreamingService::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->name = '';
        $this->icon = 'tv';
        $this->color = '#6366F1';
        $this->max_slots = 5;
        $this->website_url = '';
        $this->is_active = true;
    }
}
