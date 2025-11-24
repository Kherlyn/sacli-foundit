<x-guest-layout>
    <!-- Page Title -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">{{ __('Admin Login') }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('Sign in to your admin account') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Message -->
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" icon="envelope" iconPosition="left" :hasError="$errors->has('email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" icon="lock-closed" iconPosition="left" :hasError="$errors->has('password')" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded-md border-gray-300 text-sacli-green-600 shadow-sm focus:ring-sacli-green-500"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" id="login-btn"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sacli-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sacli-green-700 focus:bg-sacli-green-700 active:bg-sacli-green-800 focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <x-icon name="arrow-right-on-rectangle" size="sm" id="login-icon" />
                <span id="login-text">{{ __('Log in') }}</span>
                <span id="login-spinner" class="hidden">
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

        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500">
                {{ __('This is a restricted area for administrators only.') }}
            </p>
            <a href="{{ route('home') }}"
                class="text-sm text-sacli-green-600 hover:text-sacli-green-700 font-medium mt-2 inline-block transition-colors">
                {{ __('Return to main site') }}
            </a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const loginBtn = document.getElementById('login-btn');
            const loginIcon = document.getElementById('login-icon');
            const loginText = document.getElementById('login-text');
            const loginSpinner = document.getElementById('login-spinner');

            form.addEventListener('submit', function(e) {
                loginBtn.disabled = true;
                loginIcon.classList.add('hidden');
                loginSpinner.classList.remove('hidden');
                loginText.textContent = 'Signing in...';
            });
        });
    </script>
</x-guest-layout>
