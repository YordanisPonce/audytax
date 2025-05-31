{{-- resources/views/components/form-fase.blade.php --}}
@props(['fase', 'auditoryTypes', 'label'])

@php
    // ¿Desde dónde venimos? (vía query string)
    $auditoryType   = request()->query('auditorytype');   // null si no viene
    $qualityControl = request()->query('qualityControl'); // null si no viene
@endphp

<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">
  <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
    {{-- Name --}}
    <div class="input-area">
      <label for="name" class="form-label">{{ __('Name') }}</label>
      <input
        name="name"
        type="text"
        id="name"
        class="form-control"
        placeholder="{{ __('Enter name') }}"
        {{-- Este valor ahora usa ?-> y un fallback vacío --}}
        value="{{ old('name', $fase?->name ?? '') }}"
        required
      >
      <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- Description --}}
    <div class="input-area">
      <label for="description" class="form-label">{{ __('Enter description') }}</label>
      <textarea
        name="description"
        id="description"
        rows="3"
        class="form-control"
        placeholder="{{ __('Your description') }}"
      >{{ old('description', $fase?->description ?? '') }}</textarea>
      <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    {{-- Campos ocultos de contexto --}}
    @if($qualityControl)
      {{-- 1) Si venimos de QualityControl, mandamos ese ID --}}
      <input
        type="hidden"
        name="quality_control_id"
        value="{{ $qualityControl }}"
      >
      {{-- 2) También necesito enviar el auditory_type_id asociado a ese QC --}}
      <input
        type="hidden"
        name="auditory_type_id"
        value="{{ \App\Models\QualityControl::find($qualityControl)?->auditoryType->id ?? '' }}"
      >
    @endif

    @if($auditoryType)
      {{-- Si venimos de Plantilla de Auditoría, mandamos ese ID --}}
      <input
        type="hidden"
        name="auditorytype"
        value="{{ $auditoryType }}"
      >
      <input
        type="hidden"
        name="auditory_type_id"
        value="{{ $auditoryType }}"
      >
    @endif

    {{-- EN CASO DE CREACIÓN DIRECTA (sin QC ni auditorytype), podrías añadir un select opcional:
    <div class="input-area">
      <label for="auditory_type_id" class="form-label">{{ __('Auditory Type') }}</label>
      <select name="auditory_type_id" id="auditory_type_id" class="form-control">
        @foreach($auditoryTypes as $at)
          <option
            value="{{ $at->id }}"
            @selected(old('auditory_type_id', $fase?->auditory_type_id ?? '') == $at->id)
          >
            {{ $at->name }}
          </option>
        @endforeach
      </select>
      <x-input-error :messages="$errors->get('auditory_type_id')" class="mt-2" />
    </div>
    --}}
  </div>

  <button type="submit" class="btn inline-flex justify-center btn-dark mt-4">
    {{ $label }}
  </button>
</div>
