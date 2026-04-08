<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <input wire:model.live="search" type="text" placeholder="Buscar cuenta..." class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm text-sm">
            <select wire:model.live="filterService" class="rounded-lg border-gray-300 shadow-sm text-sm">
                <option value="0">Todos los servicios</option>
                @foreach($services as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <button wire:click="create" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Nueva Cuenta</button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Editar' : 'Nueva' }} Cuenta</h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Servicio</label>
                <select wire:model="streaming_service_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="">Seleccionar...</option>
                    @foreach($services as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('streaming_service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Etiqueta</label>
                <input wire:model="label" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Netflix Cuenta 1" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                <input wire:model="plan_name" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Premium">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrasena</label>
                <input wire:model="password" type="text" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Costo mensual</label>
                <input wire:model="cost" type="number" step="0.01" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dia de cobro</label>
                <input wire:model="billing_day" type="number" min="1" max="31" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proximo cobro</label>
                <input wire:model="next_billing_date" type="date" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Slots</label>
                <input wire:model="max_slots" type="number" min="1" max="20" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model="status" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="active">Activa</option>
                    <option value="suspended">Suspendida</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <input wire:model="notes" type="text" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-3 flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                <button type="button" wire:click="$set('showForm', false)" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    <div class="space-y-4">
        @foreach($accounts as $account)
        <div class="bg-white rounded-lg shadow p-4" x-data="{ expanded: false }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $account->streamingService->color ?? '#6366F1' }}"></div>
                    <div>
                        <span class="font-semibold text-gray-800">{{ $account->label }}</span>
                        <span class="text-sm text-gray-500">| {{ $account->streamingService->name }} - {{ $account->plan_name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $account->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $account->status }}
                    </span>
                    <span class="text-gray-600">{{ $account->activeSubscriptions->count() }}/{{ $account->max_slots }} slots</span>
                    <span class="font-semibold text-gray-800">{{ number_format($account->cost, 2) }} {{ $account->currency }}</span>
                    <button @click="expanded = !expanded" class="text-indigo-600 hover:text-indigo-800">
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-xs text-gray-500">Email:</span>
                        <p class="text-sm font-mono">{{ $account->email }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Contrasena:</span>
                        <p class="text-sm font-mono" x-data="{ show: false }">
                            <span x-show="!show">********</span>
                            <span x-show="show">{{ $account->password }}</span>
                            <button @click="show = !show" class="ml-2 text-xs text-indigo-600" x-text="show ? 'Ocultar' : 'Mostrar'"></button>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Dia cobro / Proximo:</span>
                        <p class="text-sm">Dia {{ $account->billing_day }} | {{ $account->next_billing_date->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if($account->activeSubscriptions->count() > 0)
                <div class="mb-3">
                    <span class="text-xs text-gray-500 font-semibold">Clientes:</span>
                    <div class="mt-1 space-y-1">
                        @foreach($account->activeSubscriptions as $sub)
                        <div class="flex items-center justify-between text-sm bg-gray-50 rounded px-3 py-1">
                            <span>{{ $sub->client->name }} <span class="text-gray-400">({{ $sub->slot_label ?? 'Sin slot' }}{{ $sub->pin ? ' | PIN: '.$sub->pin : '' }})</span></span>
                            <span class="font-medium">{{ number_format($sub->client_price, 2) }} {{ $sub->currency }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex gap-2">
                    <button wire:click="edit({{ $account->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">Editar</button>
                    <button wire:click="delete({{ $account->id }})" wire:confirm="Eliminar {{ $account->label }}?" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
