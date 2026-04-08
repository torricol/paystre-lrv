<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Variables: <code class="bg-gray-100 px-1 rounded">{client_name}</code> <code class="bg-gray-100 px-1 rounded">{service_name}</code> <code class="bg-gray-100 px-1 rounded">{amount}</code> <code class="bg-gray-100 px-1 rounded">{due_date}</code> <code class="bg-gray-100 px-1 rounded">{credentials}</code> <code class="bg-gray-100 px-1 rounded">{month}</code></p>
        <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Nueva Plantilla</button>
    </div>

    @if($showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Editar' : 'Nueva' }} Plantilla</h3>
        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input wire:model="slug" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="reminder_custom" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select wire:model="category" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="welcome">Bienvenida</option>
                    <option value="reminder">Recordatorio</option>
                    <option value="overdue">Mora</option>
                    <option value="update">Actualizacion</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Canal</label>
                <select wire:model="channel" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="any">Cualquiera</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telegram">Telegram</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cuerpo del mensaje</label>
                <textarea wire:model="body" rows="6" class="w-full rounded-lg border-gray-300 text-sm font-mono" required></textarea>
            </div>
            <div class="sm:col-span-2 flex gap-3">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                <button type="button" wire:click="$set('showForm', false)" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    <div class="space-y-3">
        @foreach($templates as $template)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        {{ match($template->category) {
                            'welcome' => 'bg-green-100 text-green-800',
                            'reminder' => 'bg-blue-100 text-blue-800',
                            'overdue' => 'bg-red-100 text-red-800',
                            'update' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800',
                        } }}">
                        {{ ucfirst($template->category) }}
                    </span>
                    <span class="font-semibold text-gray-800">{{ $template->name }}</span>
                    <span class="text-xs text-gray-400">{{ $template->slug }}</span>
                </div>
                <div class="flex gap-2">
                    <button wire:click="edit({{ $template->id }})" class="text-xs text-indigo-600 hover:text-indigo-800">Editar</button>
                    <button wire:click="delete({{ $template->id }})" wire:confirm="Eliminar {{ $template->name }}?" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                </div>
            </div>
            <pre class="text-sm text-gray-600 bg-gray-50 rounded p-3 whitespace-pre-wrap font-mono">{{ $template->body }}</pre>
        </div>
        @endforeach
    </div>
</div>
