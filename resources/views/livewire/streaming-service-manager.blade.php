<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <input wire:model.live="search" type="text" placeholder="Buscar servicio..." class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm text-sm">
        <button wire:click="create" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Nuevo Servicio</button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Editar' : 'Nuevo' }} Servicio</h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 text-sm" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <input wire:model="color" type="color" class="w-full h-10 rounded-lg border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Slots</label>
                <input wire:model="max_slots" type="number" min="1" max="20" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input wire:model="website_url" type="url" class="w-full rounded-lg border-gray-300 text-sm" placeholder="https://...">
            </div>
            <div class="sm:col-span-2 flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                <button type="button" wire:click="$set('showForm', false)" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($services as $service)
        <div class="bg-white rounded-lg shadow p-4 border-l-4" style="border-left-color: {{ $service->color ?? '#6366F1' }}">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-gray-800">{{ $service->name }}</h4>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mb-3">Max {{ $service->max_slots }} slots | {{ $service->accounts_count ?? $service->accounts->count() }} cuentas</p>
            <div class="flex gap-2">
                <button wire:click="edit({{ $service->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">Editar</button>
                <button wire:click="toggleActive({{ $service->id }})" class="text-xs text-yellow-600 hover:text-yellow-800">{{ $service->is_active ? 'Desactivar' : 'Activar' }}</button>
                <button wire:click="delete({{ $service->id }})" wire:confirm="Eliminar {{ $service->name }}?" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
            </div>
        </div>
        @endforeach
    </div>
</div>
