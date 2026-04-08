<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    {{-- Month selector + New Payment button --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <button wire:click="prevMonth" class="p-2 text-gray-500 hover:text-gray-800 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h3 class="text-lg font-semibold text-gray-800">
                {{ \Carbon\Carbon::create($viewYear, $viewMonth)->translatedFormat('F Y') }}
            </h3>
            <button wire:click="nextMonth" class="p-2 text-gray-500 hover:text-gray-800 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input wire:model.live="search" type="text" placeholder="Buscar..." class="w-full sm:w-48 rounded-lg border-gray-300 shadow-sm text-sm">
            <button wire:click="newPayment" class="whitespace-nowrap bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">+ Nuevo Pago</button>
        </div>
    </div>

    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">
            {{ $isAdvancePayment ? 'Pago Adelantado' : ($isFreeForm ? 'Nuevo Pago' : 'Registrar Pago') }}
        </h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Selector de suscripcion (visible en formulario libre o cuando no hay suscripcion seleccionada) --}}
            @if($isFreeForm)
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Suscripcion</label>
                <select wire:model.live="account_client_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="0">Seleccionar cliente / servicio...</option>
                    @foreach($allSubscriptions as $s)
                        <option value="{{ $s->id }}">{{ $s->client->name }} - {{ $s->account->streamingService->name }} ({{ $s->account->label }}) | {{ number_format($s->client_price, 2) }} {{ $s->currency }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($isAdvancePayment)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad de meses</label>
                <input wire:model.live="advance_months" type="number" min="2" max="12" class="w-full rounded-lg border-gray-300 text-sm" required>
                <p class="text-xs text-gray-500 mt-1">
                    Desde {{ \Carbon\Carbon::create($period_year, $period_month)->translatedFormat('M Y') }}
                    hasta {{ \Carbon\Carbon::create($period_year, $period_month)->addMonths($advance_months - 1)->translatedFormat('M Y') }}
                </p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monto {{ $isAdvancePayment ? 'total' : '' }}</label>
                <input wire:model="amount" type="number" step="0.01" class="w-full rounded-lg border-gray-300 text-sm" required>
                @if($isAdvancePayment)
                    <p class="text-xs text-gray-500 mt-1">Se dividira en {{ $advance_months }} pagos iguales</p>
                @endif
            </div>

            {{-- Mes y Ano (siempre visible excepto en adelantado) --}}
            @if(!$isAdvancePayment)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                <select wire:model="period_month" class="w-full rounded-lg border-gray-300 text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                <input wire:model="period_year" type="number" min="2020" max="2030" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de pago</label>
                <input wire:model="paid_at" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metodo</label>
                <select wire:model="payment_method" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Seleccionar...</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="QR">QR</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <input wire:model="notes" type="text" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-3 flex gap-3">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                    {{ $isAdvancePayment ? 'Registrar Pago Adelantado' : 'Registrar Pago' }}
                </button>
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Servicio</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($subscriptions as $sub)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $sub->client->name }}</div>
                        <div class="text-xs text-gray-500 sm:hidden">{{ $sub->account->streamingService->name }}</div>
                        @if($sub->advance_paid_until)
                            <div class="text-xs text-indigo-600">Adelantado hasta {{ $sub->advance_paid_until }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm hidden sm:table-cell">
                        <span class="inline-block w-2 h-2 rounded-full mr-1" style="background-color: {{ $sub->account->streamingService->color ?? '#6366F1' }}"></span>
                        {{ $sub->account->streamingService->name }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-semibold">{{ number_format($sub->client_price, 2) }} {{ $sub->currency }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'paid' => 'bg-green-100 text-green-800',
                                'upcoming' => 'bg-blue-100 text-blue-800',
                                'due' => 'bg-yellow-100 text-yellow-800',
                                'overdue' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = ['paid' => 'Pagado', 'upcoming' => 'Pendiente', 'due' => 'Vence hoy', 'overdue' => 'Vencido'];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$sub->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$sub->payment_status] ?? $sub->payment_status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex gap-1 justify-end items-center">
                            @if($sub->payment_status === 'paid' && $sub->current_payment)
                                <span class="text-xs text-gray-500">{{ $sub->current_payment->paid_at->format('d/m') }}</span>
                                <button wire:click="deletePayment({{ $sub->current_payment->id }})" wire:confirm="Eliminar este pago?" class="text-xs text-red-600 hover:text-red-800">Borrar</button>
                                <span class="text-gray-300">|</span>
                            @else
                                <button wire:click="recordPayment({{ $sub->id }})" class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Cobrar</button>
                            @endif
                            <button wire:click="newPayment({{ $sub->id }})" class="text-xs bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600" title="Pago libre (otro mes)">Otro mes</button>
                            <button wire:click="recordAdvancePayment({{ $sub->id }})" class="text-xs bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700" title="Pago adelantado">Adelantar</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
