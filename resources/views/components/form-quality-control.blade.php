@props(['qualityControl', 'label', 'auditoryTypes', 'statuses', 'clients', 'consultants'])

<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">

    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
        {{-- Name input start --}}
        <div class="input-area">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input name="name" type="text" id="name" class="form-control" placeholder="{{ __('Enter name') }}"
                value="{{ $qualityControl ? $qualityControl->name : old('name') }}" required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        {{-- Name input end --}}
        {{-- Description input start --}}
        <div class="input-area">
            <label for="description" class="form-label">{{ __('Enter description') }}</label>
            <textarea name="description" id="description" rows="3" class="form-control"
                placeholder="{{ __('Your description') }}">
                {{ $qualityControl ? $qualityControl->description : old('description') }}
            </textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>
        {{-- Description input end --}}
        {{-- Description input start --}}
        <div>
            <label for="select2basic" class="form-label">{{ __('Auditory Type') }}</label>
            <select name="auditory_type_id" id="auditory_type_id" class="select2 form-control w-full mt-2 py-2">
                @foreach ($auditoryTypes as $auditoryType)
                    <option value="{{ $auditoryType->id }}"
                        class=" inline-block font-Inter font-normal text-sm text-slate-600"
                        @selected($qualityControl && $auditoryType->id == $qualityControl->auditory_type_id)>
                        {{ $auditoryType->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('auditory_type_id')" class="mt-2" />
        </div>
        {{-- Description input end --}}

        {{-- statuses input start --}}
       {{--  <div>
            <label for="status_id" class="form-label">{{ __('Statuses') }}</label>
            <select name="status_id" id="status_id" class="select2 form-control w-full mt-2 py-2">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" @selected($qualityControl && $status->id == $qualityControl->status_id)
                        class=" inline-block font-Inter font-normal text-sm text-slate-600">
                        {{ $status->label }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
        </div> --}}
        {{-- statuses input end --}}
        {{-- clientes input start --}}
        <div>
            <label for="clients" class="form-label">{{ __('Clients') }}</label>
            <select name="clients[]" id="clients" class="select2 form-control w-full mt-2 py-2" multiple="multiple">
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}"
                        class=" inline-block font-Inter font-normal text-sm text-slate-600">{{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- clients input end --}}

        {{-- consultants input start --}}
        <div>
            <label for="consultants" class="form-label">{{ __('Consultants') }}</label>
            <select name="consultants[]" id="consultants" class="select2 form-control w-full mt-2 py-2"
                multiple="multiple">
                @foreach ($consultants as $consultant)
                    <option value="{{ $consultant->id }}"
                        class=" inline-block font-Inter font-normal text-sm text-slate-600">{{ $consultant->name }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- consultants input end --}}
    </div>
    <button type="submit" class="btn inline-flex justify-center btn-dark mt-4">
        {{ $label }}
    </button>
</div>
