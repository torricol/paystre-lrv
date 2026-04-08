<div>
    {{-- Month selector --}}
    <div class="flex items-center gap-4 mb-6">
        <button wire:click="prevMonth" class="p-2 text-gray-500 hover:text-gray-800 rounded hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <h3 class="text-lg font-semibold text-gray-800">
            {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
        </h3>
        <button wire:click="nextMonth" class="p-2 text-gray-500 hover:text-gray-800 rounded hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Costo Total</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format($totalCost, 2) }} BOB</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Cobrado</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($totalRevenue, 2) }} BOB</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Esperado</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($expectedRevenue, 2) }} BOB</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Ganancia</div>
            <div class="text-2xl font-bold {{ ($totalRevenue - $totalCost) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($totalRevenue - $totalCost, 2) }} BOB
            </div>
        </div>
    </div>

    {{-- Per-service breakdown --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Rentabilidad por Servicio</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Cuentas</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Clientes</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Costo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ingreso</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ganancia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($serviceStats as $stat)
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $stat['color'] ?? '#6366F1' }}"></span>
                            <span class="font-medium text-gray-800">{{ $stat['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center text-sm hidden sm:table-cell">{{ $stat['accounts'] }}</td>
                    <td class="px-4 py-3 text-center text-sm hidden sm:table-cell">{{ $stat['clients'] }}</td>
                    <td class="px-4 py-3 text-right text-sm text-red-600">{{ number_format($stat['cost'], 2) }}</td>
                    <td class="px-4 py-3 text-right text-sm text-green-600">{{ number_format($stat['revenue'], 2) }}</td>
                    <td class="px-4 py-3 text-right text-sm font-semibold {{ $stat['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($stat['profit'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
