@props(['fase', 'label', 'auditoryTypes'])

@php
    $auditoryType = request()->query('auditorytype');
    $qualityControl = request()->query('qualityControl');
@endphp
<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">

    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
        {{-- Name input start --}}
        <div class="input-area">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input name="name" type="text" id="name" class="form-control" placeholder="{{ __('Enter name') }}"
                value="{{ $fase ? $fase->name : old('name') }}" required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        {{-- Name input end --}}
        {{-- Description input start --}}
        <div class="input-area">
            <label for="description" class="form-label">{{ __('Enter description') }}</label>
            <textarea name="description" id="description" rows="3" class="form-control"
                placeholder="{{ __('Your description') }}">
                {{ $fase ? $fase->description : old('description') }}
            </textarea>
        </div>
        {{-- Description input end --}}
        {{-- Description input start --}}
        <div>
            <input type="hidden" value="{{ $auditoryType }}"   name="auditory_type_id">
            @isset($qualityControl)
            <input type="hidden" value="{{ $qualityControl }}"   name="quality_control_id">
            @endisset
        </div>
        {{-- Description input end --}}
    </div>
    <button type="submit" class="btn inline-flex justify-center btn-dark mt-4">
        {{ $label }}
    </button>
</div>
