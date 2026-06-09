<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Pagos</h2>
    </x-slot>

    <div x-data="{ tab: 'tracker' }">
        {{-- Pestañas --}}
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex gap-6">
                <button @click="tab = 'tracker'"
                    :class="tab === 'tracker' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm transition-colors">
                    Seguimiento mensual
                </button>
                <button @click="tab = 'historial'"
                    :class="tab === 'historial' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm transition-colors">
                    Historial de pagos
                </button>
            </nav>
        </div>

        <div x-show="tab === 'tracker'" x-cloak>
            <livewire:payment-tracker />
        </div>

        <div x-show="tab === 'historial'" x-cloak>
            <livewire:payment-list />
        </div>
    </div>
</x-app-layout>
