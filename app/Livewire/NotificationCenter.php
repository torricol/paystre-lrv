<?php

namespace App\Livewire;

use App\Models\AccountClient;
use App\Models\Client;
use App\Models\MessageTemplate;
use App\Models\NotificationLog;
use App\Services\NotificationDispatcher;
use Livewire\Component;

class NotificationCenter extends Component
{
    public bool $showSendForm = false;
    public int $selectedClientId = 0;
    public int $selectedTemplateId = 0;
    public int $selectedSubscriptionId = 0;
    public string $customMessage = '';
    public bool $useCustom = false;

    public function render()
    {
        $logs = NotificationLog::with(['client', 'messageTemplate'])
            ->latest('created_at')
            ->limit(50)
            ->get();

        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $templates = MessageTemplate::active()->orderBy('category')->orderBy('name')->get();

        return view('livewire.notification-center', compact('logs', 'clients', 'templates'));
    }

    public function openSendForm(int $clientId = 0): void
    {
        $this->selectedClientId = $clientId;
        $this->selectedTemplateId = 0;
        $this->selectedSubscriptionId = 0;
        $this->customMessage = '';
        $this->useCustom = false;
        $this->showSendForm = true;
    }

    public function send(): void
    {
        $client = Client::findOrFail($this->selectedClientId);
        $dispatcher = app(NotificationDispatcher::class);

        if ($this->useCustom && $this->customMessage) {
            $results = $dispatcher->sendDirect($client, $this->customMessage);
        } elseif ($this->selectedTemplateId) {
            $template = MessageTemplate::findOrFail($this->selectedTemplateId);
            $subscription = $this->selectedSubscriptionId
                ? AccountClient::find($this->selectedSubscriptionId)
                : null;
            $results = $dispatcher->sendFromTemplate($client, $template, $subscription);
        } else {
            session()->flash('error', 'Selecciona una plantilla o escribe un mensaje.');
            return;
        }

        $sent = collect($results)->where('status', 'sent')->count();
        $failed = collect($results)->where('status', 'failed')->count();

        if ($sent > 0) {
            session()->flash('message', "Mensaje enviado ({$sent} exitoso, {$failed} fallido).");
        } else {
            session()->flash('error', 'No se pudo enviar el mensaje. Revisa la configuracion.');
        }

        $this->showSendForm = false;
    }
}
