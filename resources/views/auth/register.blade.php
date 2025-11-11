<x-guest-layout>
    <!-- Page Title -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">{{ __('Create Account') }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('Join SACLI FOUNDIT to report and find lost items') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" icon="user" iconPosition="left" :hasError="$errors->has('name')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" icon="envelope" iconPosition="left" :hasError="$errors->has('email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Course (Optional) -->
        <div>
            <x-input-label for="course" :value="__('Course (Optional)')" />
            <x-text-input id="course" class="block mt-1 w-full" type="text" name="course" :value="old('course')"
                maxlength="100" placeholder="e.g., Computer Science" icon="academic-cap" iconPosition="left"
                :hasError="$errors->has('course')" />
            <x-input-error :messages="$errors->get('course')" class="mt-2" />
        </div>

        <!-- Year Level (Optional) -->
        <div>
            <x-input-label for="year" :value="__('Year Level (Optional)')" />
            <select id="year" name="year"
                class="block mt-1 w-full border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-lg shadow-sm @error('year') border-red-500 @enderror">
                <option value="">{{ __('Select Year') }}</option>
                <option value="1" {{ old('year') == '1' ? 'selected' : '' }}>{{ __('1st Year') }}</option>
                <option value="2" {{ old('year') == '2' ? 'selected' : '' }}>{{ __('2nd Year') }}</option>
                <option value="3" {{ old('year') == '3' ? 'selected' : '' }}>{{ __('3rd Year') }}</option>
                <option value="4" {{ old('year') == '4' ? 'selected' : '' }}>{{ __('4th Year') }}</option>
                <option value="5" {{ old('year') == '5' ? 'selected' : '' }}>{{ __('5th Year') }}</option>
                <option value="6" {{ old('year') == '6' ? 'selected' : '' }}>{{ __('6th Year') }}</option>
            </select>
            <x-input-error :messages="$errors->get('year')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" icon="lock-closed" iconPosition="left" :hasError="$errors->has('password')" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" icon="lock-closed" iconPosition="left"
                :hasError="$errors->has('password_confirmation')" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" id="register-btn"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sacli-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sacli-green-700 focus:bg-sacli-green-700 active:bg-sacli-green-800 focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <x-icon name="user-plus" size="sm" id="register-icon" />
                <span id="register-text">{{ __('Create Account') }}</span>
                <span id="register-spinner" class="hidden">
                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
            </button>
        </div>

        <div class="text-center pt-4">
            <span class="text-sm text-gray-600">{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}"
                class="text-sm text-sacli-green-600 hover:text-sacli-green-700 font-medium ml-1 transition-colors">
                {{ __('Sign in') }}
            </a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const registerBtn = document.getElementById('register-btn');
            const registerIcon = document.getElementById('register-icon');
            const registerText = document.getElementById('register-text');
            const registerSpinner = document.getElementById('register-spinner');

            form.addEventListener('submit', function(e) {
                registerBtn.disabled = true;
                registerIcon.classList.add('hidden');
                registerSpinner.classList.remove('hidden');
                registerText.textContent = 'Creating account...';
            });
        });
    </script>
</x-guest-layout>
