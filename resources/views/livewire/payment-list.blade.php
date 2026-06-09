<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <input wire:model.live="search" type="text" placeholder="Buscar cliente o servicio..."
            class="flex-1 min-w-[180px] rounded-lg border-gray-300 shadow-sm text-sm">
        <select wire:model.live="filterYear" class="rounded-lg border-gray-300 text-sm">
            <option value="0">Todos los años</option>
            @for($y = now()->year + 1; $y >= 2024; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <select wire:model.live="filterMonth" class="rounded-lg border-gray-300 text-sm">
            <option value="0">Todos los meses</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
            @endfor
        </select>
        <select wire:model.live="filterMethod" class="rounded-lg border-gray-300 text-sm">
            <option value="">Todos los métodos</option>
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
            <option value="QR">QR</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    {{-- Formulario de edición --}}
    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-indigo-500">
        <h3 class="text-lg font-semibold mb-4">Editar Pago</h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                <input wire:model="amount" type="number" step="0.01" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                <select wire:model="period_month" class="w-full rounded-lg border-gray-300 text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
                @error('period_month') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <input wire:model="period_year" type="number" min="2020" max="2030" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de pago</label>
                <input wire:model="paid_at" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                <select wire:model="payment_method" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Seleccionar...</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="QR">QR</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <input wire:model="notes" type="text" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-3 flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Actualizar Pago</button>
                <button type="button" wire:click="cancelForm" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Tabla de pagos --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Servicio</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Período</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden xl:table-cell">Notas</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr class="{{ $editingPaymentId === $payment->id ? 'bg-indigo-50' : 'hover:bg-gray-50' }}">
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                        {{ $payment->paid_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 text-sm">{{ $payment->accountClient->client->name }}</div>
                        <div class="text-xs text-gray-500 md:hidden">{{ $payment->accountClient->account->streamingService->name }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 hidden md:table-cell">
                        <span class="inline-block w-2 h-2 rounded-full mr-1" style="background-color: {{ $payment->accountClient->account->streamingService->color ?? '#6366F1' }}"></span>
                        {{ $payment->accountClient->account->streamingService->name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 hidden sm:table-cell whitespace-nowrap">
                        {{ \Carbon\Carbon::create($payment->period_year, $payment->period_month)->translatedFormat('M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-semibold whitespace-nowrap">
                        {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        @if($payment->payment_method)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($payment->payment_method) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 hidden xl:table-cell max-w-xs truncate">
                        {{ $payment->notes ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex gap-2 justify-end">
                            <button wire:click="editPayment({{ $payment->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">Editar</button>
                            <button wire:click="deletePayment({{ $payment->id }})" wire:confirm="¿Eliminar este pago?" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 text-sm">No se encontraron pagos con los filtros seleccionados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Total y paginación --}}
    <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="text-sm text-gray-600">
            Total mostrado: <span class="font-semibold text-gray-800">{{ number_format($total, 2) }} BOB</span>
            <span class="text-gray-400">({{ $payments->total() }} pagos)</span>
        </div>
        <div>
            {{ $payments->links() }}
        </div>
    </div>
</div>
