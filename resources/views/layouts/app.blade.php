<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PayStre') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen bg-gray-100 flex">
            {{-- Sidebar Overlay (mobile) --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-gray-600/75 lg:hidden" @click="sidebarOpen = false">
            </div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-col">

                {{-- Logo --}}
                <div class="flex items-center justify-between h-16 px-4 bg-gray-950">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span class="text-white text-lg font-bold">PayStre</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home" wire:navigate>
                        Dashboard
                    </x-sidebar-link>

                    <div class="pt-4 pb-2 px-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestion</p>
                    </div>

                    <x-sidebar-link :href="route('streaming-services')" :active="request()->routeIs('streaming-services')" icon="tv" wire:navigate>
                        Servicios
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('accounts')" :active="request()->routeIs('accounts*')" icon="key" wire:navigate>
                        Cuentas
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('clients')" :active="request()->routeIs('clients*')" icon="users" wire:navigate>
                        Clientes
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('subscriptions')" :active="request()->routeIs('subscriptions*')" icon="link" wire:navigate>
                        Suscripciones
                    </x-sidebar-link>

                    <div class="pt-4 pb-2 px-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cobros</p>
                    </div>

                    <x-sidebar-link :href="route('payments')" :active="request()->routeIs('payments*')" icon="dollar" wire:navigate>
                        Pagos
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('notifications')" :active="request()->routeIs('notifications*')" icon="bell" wire:navigate>
                        Notificaciones
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('templates')" :active="request()->routeIs('templates*')" icon="message" wire:navigate>
                        Plantillas
                    </x-sidebar-link>

                    <div class="pt-4 pb-2 px-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistema</p>
                    </div>

                    <x-sidebar-link :href="route('reports')" :active="request()->routeIs('reports*')" icon="chart" wire:navigate>
                        Reportes
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('settings')" :active="request()->routeIs('settings*')" icon="cog" wire:navigate>
                        Configuracion
                    </x-sidebar-link>
                </nav>

                {{-- User --}}
                <div class="border-t border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-bold">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        </div>
                        <a href="{{ route('profile') }}" wire:navigate class="text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
                {{-- Top Bar (mobile) --}}
                <header class="sticky top-0 z-30 bg-white border-b border-gray-200 lg:hidden">
                    <div class="flex items-center justify-between h-16 px-4">
                        <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <span class="text-lg font-bold text-gray-800">PayStre</span>
                        <div class="w-6"></div>
                    </div>
                </header>

                {{-- Page Header --}}
                @if (isset($header))
                    <header class="bg-white shadow-sm">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{-- Page Content --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewire('wire-elements-modal')
    </body>
</html>
