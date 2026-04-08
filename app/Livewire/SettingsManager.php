<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class SettingsManager extends Component
{
    public string $admin_name = '';
    public string $telegram_bot_token = '';
    public string $whatsapp_api_url = '';
    public string $whatsapp_session = '';
    public string $reminder_days_before = '';
    public string $overdue_days_warning = '';
    public string $overdue_days_critical = '';
    public string $default_currency = '';

    public function mount(): void
    {
        $this->admin_name = Setting::get('admin_name', '');
        $this->telegram_bot_token = Setting::get('telegram_bot_token', '');
        $this->whatsapp_api_url = Setting::get('whatsapp_api_url', '');
        $this->whatsapp_session = Setting::get('whatsapp_session', 'default');
        $this->reminder_days_before = Setting::get('reminder_days_before', '3');
        $this->overdue_days_warning = Setting::get('overdue_days_warning', '3');
        $this->overdue_days_critical = Setting::get('overdue_days_critical', '7');
        $this->default_currency = Setting::get('default_currency', 'BOB');
    }

    public function render()
    {
        return view('livewire.settings-manager');
    }

    public function save(): void
    {
        Setting::set('admin_name', $this->admin_name);
        Setting::set('telegram_bot_token', $this->telegram_bot_token);
        Setting::set('whatsapp_api_url', $this->whatsapp_api_url);
        Setting::set('whatsapp_session', $this->whatsapp_session);
        Setting::set('reminder_days_before', $this->reminder_days_before);
        Setting::set('overdue_days_warning', $this->overdue_days_warning);
        Setting::set('overdue_days_critical', $this->overdue_days_critical);
        Setting::set('default_currency', $this->default_currency);

        session()->flash('message', 'Configuracion guardada.');
    }
}
