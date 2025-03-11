@props(['auditoryType', 'label', 'clients'])

<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">
    <!-- Sección para el nombre de la auditoría -->
    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
        {{-- Name input end --}}
        <div class="input-area">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input 
                name="name" 
                type="text" 
                id="name" 
                class="form-control" 
                placeholder="{{ __('Enter name') }}"
                value="{{ $auditoryType ? $auditoryType->name : old('name') }}" 
                required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
    </div>

    <!-- Sección para seleccionar múltiples clientes -->
    <div class="form-group mt-4">
        <label class="form-label">{{ __('Clients') }}</label>
        <div class="flex flex-col space-y-2 mt-2">
            @foreach($clients as $client)
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="client_ids[]" 
                        id="client_{{ $client->id }}" 
                        value="{{ $client->id }}" 
                        class="form-checkbox h-4 w-4 text-primary border-slate-300 rounded" 
                        {{ $auditoryType && $auditoryType->clients->contains($client->id) ? 'checked' : '' }}>
                    <label for="client_{{ $client->id }}" class="ml-2 text-sm text-slate-600 dark:text-slate-300">
                        {{ $client->name }}
                    </label>
                </div>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('client_ids')" class="mt-2" />
    </div>

    <!-- Sección para ingresar documentos usando un textarea -->
    <div class="form-group mt-4">
        <label for="documents" class="form-label">{{ __('Documentos') }}</label>
        <textarea 
            name="documents" 
            id="documents" 
            class="form-control" 
            placeholder="Escribe los nombres de los documentos separados por comas" 
            rows="4">{{ old('documents', $auditoryType ? implode(',', $auditoryType->documents->pluck('name')->toArray()) : '') }}</textarea>
    </div>

    <!-- Botón para enviar el formulario -->
    <button type="submit" class="btn btn-dark mt-4">
        {{ $label }}
    </button>
</div>
