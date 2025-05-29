@props(['user', 'roles', 'label'])

<div class="bg-white dark:bg-slate-800 rounded-md p-5 pb-6">

    <div class="grid sm:grid-cols-1 gap-x-8 gap-y-4">


        {{-- Name input end --}}
        <div class="input-area">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input name="name" type="text" id="name" class="form-control"
                placeholder="{{ __('Enter your name') }}" value="{{ $user ? $user->name : old('name') }}" required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email input start --}}
        <div class="input-area">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input name="email" type="email" id="email" class="form-control"
                placeholder="{{ __('Enter your email') }}" value="{{ $user ? $user->email : old('email') }}" required>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Email input end --}}
        {{-- Phone input start --}}
        <div class="input-area">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input name="phone" type="tel" id="phone" class="form-control"
                placeholder="{{ __('Enter Phone') }}" value="{{ $user ? $user->phone : old('phone') }}">
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        {{-- Enterprise input start --}}
        <div class="input-area">
            <label for="company" class="form-label">{{ __('COMPANY') }}</label>
            <input name="company" type="text" id="company" class="form-control"
                placeholder="{{ __('Enter Company') }}" value="{{ $user ? $user->company : old('company') }}">
            <x-input-error :messages="$errors->get('company')" class="mt-2" />
        </div>
        {{-- Enterprise input end --}}
        {{-- Password input start --}}
        <div class="input-area">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input name="password" type="password" id="password" class="form-control"
                placeholder="{{ __('Enter Password') }}">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        {{-- Password input end --}}
        {{-- Photo input start --}}
        <div class="input-area">
            <label for="country" class="form-label">
                {{ __('Photo') }}
            </label>
            <input name="photo" type="file" placeholder="Default input"
                class="form-control
            p-[0.565rem] pl-2">
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>
        {{-- Photo input end --}}
        {{-- Role input start --}}
        <div class="input-area">
            <label for="role" class="form-label">{{ __('Role') }}</label>
            <select name="role" class="form-control">
                <option value="" @selected(!$user) disabled>
                    {{ __('Select Role') }}
                </option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected($user && $user->hasRole($role->name))>
                        {{ $allRoles[$role->name] }}
                    </option>
                @endforeach
            </select>
            <iconify-icon class="absolute right-3 bottom-3 text-xl dark:text-white z-10"
                icon="material-symbols:keyboard-arrow-down-rounded"></iconify-icon>
        </div>
        {{-- Role input end --}}
    </div>
    <button type="submit" class="btn inline-flex justify-center btn-dark mt-4 w-full">
        {{ $label }}
    </button>
</div>
