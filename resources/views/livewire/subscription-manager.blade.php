<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <input wire:model.live="search" type="text" placeholder="Buscar por cliente o cuenta..." class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm text-sm">
        <button wire:click="create" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Nueva Suscripcion</button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Editar' : 'Nueva' }} Suscripcion</h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta</label>
                <select wire:model="account_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="">Seleccionar cuenta...</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->streamingService->name }} - {{ $acc->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <select wire:model="client_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="">Seleccionar cliente...</option>
                    @foreach($clients as $cli)
                        <option value="{{ $cli->id }}">{{ $cli->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slot / Perfil</label>
                <input wire:model="slot_label" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Perfil 2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PIN</label>
                <input wire:model="pin" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="1234">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio mensual</label>
                <input wire:model="client_price" type="number" step="0.01" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dia de pago</label>
                <input wire:model="payment_day" type="number" min="1" max="31" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                <input wire:model="started_at" type="date" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div class="sm:col-span-2 lg:col-span-3 flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                <button type="button" wire:click="$set('showForm', false)" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio / Cuenta</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Slot</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Dia Pago</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($subscriptions as $sub)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $sub->client->name }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-block w-2 h-2 rounded-full mr-1" style="background-color: {{ $sub->account->streamingService->color ?? '#6366F1' }}"></span>
                        {{ $sub->account->streamingService->name }} - {{ $sub->account->label }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 hidden sm:table-cell">{{ $sub->slot_label ?? '-' }}</td>
                    <td class="px-4 py-3 text-right text-sm font-semibold">{{ number_format($sub->client_price, 2) }} {{ $sub->currency }}</td>
                    <td class="px-4 py-3 text-center text-sm hidden md:table-cell">{{ $sub->payment_day }}</td>
                    <td class="px-4 py-3 text-right">
                        <button wire:click="edit({{ $sub->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button wire:click="endSubscription({{ $sub->id }})" wire:confirm="Finalizar suscripcion de {{ $sub->client->name }}?" class="text-xs text-red-600 hover:text-red-800 ml-2">Finalizar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
