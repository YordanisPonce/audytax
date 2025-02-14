@props(['auditoryType', 'label'])

<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">

    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
        {{-- Name input end --}}
        <div class="input-area">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input name="name" type="text" id="name" class="form-control" placeholder="{{ __('Enter name') }}"
                value="{{ $auditoryType ? $auditoryType->name : old('name') }}" required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
    </div>
    <button type="submit" class="btn inline-flex justify-center btn-dark mt-4">
        {{ $label }}
    </button>
</div>
