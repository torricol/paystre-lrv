<div>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Cuentas Activas</div>
            <div class="text-2xl font-bold text-gray-800">{{ $activeAccounts }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Clientes Activos</div>
            <div class="text-2xl font-bold text-gray-800">{{ $activeClients }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Costo Mensual</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format($totalCost, 2) }} BOB</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Ingresos este Mes</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($revenueThisMonth, 2) }} BOB</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Pagos Vencidos --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Pagos Vencidos ({{ $overdue->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($overdue as $sub)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800">{{ $sub->client->name }}</span>
                            <span class="text-sm text-gray-500">- {{ $sub->account->streamingService->name }}</span>
                        </div>
                        <span class="text-sm font-semibold text-red-600">{{ number_format($sub->client_price, 2) }} {{ $sub->currency }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin pagos vencidos.</p>
                @endforelse
            </div>
        </div>

        {{-- Proximos Pagos --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Proximos 7 dias ({{ $upcoming->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($upcoming as $sub)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800">{{ $sub->client->name }}</span>
                            <span class="text-sm text-gray-500">- {{ $sub->account->streamingService->name }}</span>
                        </div>
                        <span class="text-sm text-gray-600">Dia {{ $sub->payment_day }} - {{ number_format($sub->client_price, 2) }} {{ $sub->currency }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin pagos proximos.</p>
                @endforelse
            </div>
        </div>

        {{-- Notificaciones Recientes --}}
        <div class="bg-white rounded-lg shadow lg:col-span-2">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Notificaciones Recientes</h3>
            </div>
            <div class="p-4">
                @forelse($recentNotifications as $log)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->status === 'sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $log->status === 'sent' ? 'Enviado' : 'Fallido' }}
                            </span>
                            <span class="text-sm text-gray-800">{{ $log->client->name ?? 'N/A' }}</span>
                            <span class="text-xs text-gray-500">via {{ $log->channel }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin notificaciones recientes.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
