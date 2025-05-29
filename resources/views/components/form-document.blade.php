@props(['document', 'label', 'fases', 'statuses', 'qualityControl'])
@php
    $param = request()->query('fase');
@endphp
<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">

    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
        {{-- Name input start --}}
        <div class="input-area">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input name="name" type="text" id="name" class="form-control" placeholder="{{ __('Enter name') }}"
                value="{{ $document ? $document->name : old('name') }}" required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        {{-- Name input end --}}
        {{-- file input start --}}

        {{-- File input end --}}
        {{-- Description input start --}}
        <div class="input-area">
            <label for="description" class="form-label">{{ __('Enter description') }}</label>
            <textarea name="description" id="description" rows="3" class="form-control"
                placeholder="{{ __('Your description') }}">
            {{ $document ? $document->description : old('description') }}
            </textarea>
        </div>
        @if (!$param)
            <label for="fase_id" class="form-label">{{ __('Fases') }}</label>
            <select name="fase_id" id="fase_id" class="select2 form-control w-full mt-2 py-2">
                @foreach ($fases as $fase)
                    <option value="{{ $fase->id }}" @selected($document && $fase->id === $document->fase_id)
                        class=" inline-block font-Inter font-normal text-sm text-slate-600">
                        {{ $fase->name }}
                    </option>
                @endforeach
            </select>
        @else
            <input type="hidden" name="fase_id" value="{{ $param }}">
        @endif
        {{-- Description input end --}}
        <div>

        </div>
        {{-- Document input end --}}

        {{-- Document statuses start --}}
      {{--   <div>
            <label for="status_id" class="form-label">{{ __('Statuses') }}</label>
            <select name="status_id" id="status_id" class="select2 form-control w-full mt-2 py-2">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" @selected($document && $status->id === $document->status_id)
                        class=" inline-block font-Inter font-normal text-sm text-slate-600">
                        {{ $status->label }}
                    </option>
                @endforeach
            </select>
        </div> --}}
        {{-- Document statuses end --}}
    </div>
    <button type="submit" class="btn inline-flex justify-center btn-dark mt-4">
        {{ $label }}
    </button>
</div>
