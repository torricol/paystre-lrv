<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- General --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">General</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Admin</label>
                    <input wire:model="admin_name" type="text" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Moneda por defecto</label>
                    <input wire:model="default_currency" type="text" class="w-full rounded-lg border-gray-300 text-sm" maxlength="3">
                </div>
            </div>
        </div>

        {{-- Telegram --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Telegram</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bot Token</label>
                <input wire:model="telegram_bot_token" type="text" class="w-full rounded-lg border-gray-300 text-sm font-mono" placeholder="123456:ABC-DEF...">
                <p class="text-xs text-gray-500 mt-1">Obtener desde @BotFather en Telegram</p>
            </div>
        </div>

        {{-- WhatsApp --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">WhatsApp (WAHA)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL de API</label>
                    <input wire:model="whatsapp_api_url" type="url" class="w-full rounded-lg border-gray-300 text-sm" placeholder="http://localhost:3000">
                    <p class="text-xs text-gray-500 mt-1">URL donde corre WAHA (Docker)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sesion</label>
                    <input wire:model="whatsapp_session" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="default">
                </div>
            </div>
        </div>

        {{-- Recordatorios --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recordatorios Automaticos</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dias antes del vencimiento</label>
                    <input wire:model="reminder_days_before" type="number" min="1" max="15" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alerta mora (dias)</label>
                    <input wire:model="overdue_days_warning" type="number" min="1" max="30" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mora critica (dias)</label>
                    <input wire:model="overdue_days_critical" type="number" min="1" max="30" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar Configuracion</button>
    </form>
</div>
