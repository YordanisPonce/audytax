@props(['document', 'label', 'fases', 'statuses', 'qualityControl'])
@php
    $param = request()->query('fase');
    // Obtengo la clave (“key”) del estado actual del documento, si existe.
    $currentKey = $document->status->key ?? null;

    // Busco en la colección de $statuses los IDs de cada estado usando su key.
    // Suponemos que $statuses es algo como Status::all(), e incluye los modelos con ->key y ->id y ->label.
    $statusMap = $statuses->keyBy('key'); // ahora puedo hacer: $statusMap['open']->id, etc.

    $openId         = $statusMap['open']->id ?? null;
    $waitingId      = $statusMap['waiting_review']->id ?? null;
    $acceptedId     = $statusMap['accepted']->id ?? null;
    $rejectedId     = $statusMap['rejected']->id ?? null;
@endphp
<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">

    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">
        @if(request()->has('auditorytype'))
    <input type="hidden" name="auditorytype" value="{{ request()->get('auditorytype') }}">
@endif

@if(request()->has('fase'))
    <input type="hidden" name="fase" value="{{ request()->get('fase') }}">
@endif
@if(request()->has('qualityControl'))
    <input type="hidden" name="qualityControl" value="{{ request()->get('qualityControl') }}">
@endif


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
        {{-- ==================================================================== --}}
        {{-- Bloque de “Estado” (solo en editar y solo administrador) --}}
        {{-- ==================================================================== --}}
        @if($document && auth()->user()->hasRole('admin'))
            <div class="input-area">
                <label for="status_id" class="form-label">{{ __('Estado') }}</label>

                {{-- 1) Si el estado es “open”: mostrar un select disabled solo “Abierta” --}}
                @if($currentKey === 'open')
                    <select disabled class="form-control w-full mt-2 py-2 bg-gray-100 text-gray-600 cursor-not-allowed">
                        <option selected>{{ $statusMap['open']->label }}</option>
                    </select>


                {{-- 2) Si el estado es “waiting_review”, “accepted” o “rejected”:
                      mostrar siempre un select habilitado con las dos opciones (Aceptada/Rechazada),
                      y hacer “waiting_review” disabled si corresponde --}}
                @elseif(in_array($currentKey, ['waiting_review', 'accepted', 'rejected']))
                    <select name="status_id" id="status_id" class="form-control w-full mt-2 py-2">
                        {{-- Opción “En espera de revisión” --}}
                        <option value="{{ $waitingId }}"
                            @if($currentKey === 'waiting_review') selected @endif
                            @if($currentKey === 'waiting_review') disabled @endif
                        >
                            {{ $statusMap['waiting_review']->label }}
                        </option>

                        {{-- Opción “Aceptada” --}}
                        <option value="{{ $acceptedId }}"
                            @if($currentKey === 'accepted') selected @endif
                        >
                            {{ $statusMap['accepted']->label }}
                        </option>

                        {{-- Opción “Rechazada” --}}
                        <option value="{{ $rejectedId }}"
                            @if($currentKey === 'rejected') selected @endif
                        >
                            {{ $statusMap['rejected']->label }}
                        </option>
                    </select>


                {{-- 3) Cualquier otro caso (seguridad), mostrar disabled con label actual --}}
                @else
                    <select disabled class="form-control w-full mt-2 py-2 bg-gray-100 text-gray-600 cursor-not-allowed">
                        <option selected>{{ $document->status->label ?? __('Sin estado') }}</option>
                    </select>
                @endif

                <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
            </div>
        @endif
        {{-- Fin bloque “Estado” --}}
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
