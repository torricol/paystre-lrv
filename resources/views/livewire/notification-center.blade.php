<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Historial de Notificaciones</h3>
        <button wire:click="openSendForm" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Enviar Mensaje</button>
    </div>

    @if($showSendForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Enviar Notificacion</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <select wire:model.live="selectedClientId" class="w-full rounded-lg border-gray-300 text-sm" required>
                    <option value="0">Seleccionar cliente...</option>
                    @foreach($clients as $cli)
                        <option value="{{ $cli->id }}">{{ $cli->name }} ({{ $cli->preferred_channel }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Suscripci&oacute;n</label>
                <select wire:model.live="selectedSubscriptionId" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="0">Seleccionar suscripci&oacute;n...</option>
                    @foreach($this->subscriptions as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->account->streamingService->name }} - {{ $sub->account->label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="flex items-center gap-2 mb-2">
                    <input wire:model.live="useCustom" type="checkbox" class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700">Mensaje personalizado</span>
                </label>

                @if(!$useCustom)
                    <select wire:model="selectedTemplateId" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="0">Seleccionar plantilla...</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">[{{ ucfirst($tpl->category) }}] {{ $tpl->name }}</option>
                        @endforeach
                    </select>
                @else
                    <textarea wire:model="customMessage" rows="3" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Escribe tu mensaje..."></textarea>
                @endif
            </div>

            <div class="sm:col-span-2 flex gap-3">
                <button wire:click="send" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">Enviar</button>
                <button wire:click="$set('showSendForm', false)" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Cancelar</button>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Canal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Plantilla</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($logs as $log)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $log->created_at->format('d/m H:i') }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $log->client->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm hidden sm:table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->channel === 'whatsapp' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($log->channel) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $log->messageTemplate->name ?? 'Directo' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->status === 'sent' ? 'bg-green-100 text-green-800' : ($log->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $log->status === 'sent' ? 'Enviado' : ($log->status === 'failed' ? 'Fallido' : 'Pendiente') }}
                        </span>
                        @if($log->error_message)
                            <div class="text-xs text-red-500 mt-1">{{ \Illuminate\Support\Str::limit($log->error_message, 50) }}</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
