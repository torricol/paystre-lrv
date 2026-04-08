<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <input wire:model.live="search" type="text" placeholder="Buscar cliente..." class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm text-sm">
        <button wire:click="create" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Nuevo Cliente</button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Editar' : 'Nuevo' }} Cliente</h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 text-sm" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefono (WhatsApp)</label>
                <input wire:model="phone" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="59171234567">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telegram Chat ID</label>
                <input wire:model="telegram_chat_id" type="text" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telegram Username</label>
                <input wire:model="telegram_username" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="@usuario">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Canal preferido</label>
                <select wire:model="preferred_channel" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telegram">Telegram</option>
                    <option value="both">Ambos</option>
                </select>
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2">
                    <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700">Activo</span>
                </label>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
            </div>
            <div class="sm:col-span-2 flex gap-3">
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Contacto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Canal</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Suscripciones</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($clients as $client)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $client->name }}</div>
                        <div class="text-xs text-gray-500 sm:hidden">{{ $client->phone ?? $client->telegram_username ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 hidden sm:table-cell">
                        <div>{{ $client->phone ?? '-' }}</div>
                        @if($client->telegram_username)
                            <div class="text-xs text-gray-400">{{ $client->telegram_username }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $client->preferred_channel === 'whatsapp' ? 'bg-green-100 text-green-800' :
                               ($client->preferred_channel === 'telegram' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ ucfirst($client->preferred_channel) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm">{{ $client->active_subscriptions_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <button wire:click="edit({{ $client->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button wire:click="delete({{ $client->id }})" wire:confirm="Eliminar {{ $client->name }}?" class="text-xs text-red-600 hover:text-red-800 ml-2">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
