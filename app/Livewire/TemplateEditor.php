<?php

namespace App\Livewire;

use App\Models\MessageTemplate;
use Livewire\Component;

class TemplateEditor extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $slug = '';
    public string $name = '';
    public string $category = 'reminder';
    public string $body = '';
    public string $channel = 'any';
    public bool $is_active = true;

    protected $rules = [
        'slug' => 'required|string|max:100',
        'name' => 'required|string|max:150',
        'category' => 'required|in:welcome,reminder,overdue,update,custom',
        'body' => 'required|string',
        'channel' => 'required|in:whatsapp,telegram,any',
    ];

    public function render()
    {
        $templates = MessageTemplate::orderBy('category')->orderBy('name')->get();
        return view('livewire.template-editor', compact('templates'));
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $template = MessageTemplate::findOrFail($id);
        $this->editingId = $template->id;
        $this->slug = $template->slug;
        $this->name = $template->name;
        $this->category = $template->category;
        $this->body = $template->body;
        $this->channel = $template->channel;
        $this->is_active = $template->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category,
            'body' => $this->body,
            'channel' => $this->channel,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            MessageTemplate::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Plantilla actualizada.');
        } else {
            MessageTemplate::create($data);
            session()->flash('message', 'Plantilla creada.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        MessageTemplate::findOrFail($id)->delete();
        session()->flash('message', 'Plantilla eliminada.');
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->slug = '';
        $this->name = '';
        $this->category = 'reminder';
        $this->body = '';
        $this->channel = 'any';
        $this->is_active = true;
    }
}
