<x-guest-layout>
    {{-- Alpine for the password toggle (safe to keep even if already included globally) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Left: Brand / Pitch --}}
            <div class="hidden md:flex flex-col justify-center p-8 rounded-2xl bg-white/60 ring-1 ring-gray-100">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/edushare-logo.png') }}" alt="EduShare Logo" class="h-14 w-auto">
                    <span class="text-2xl font-semibold text-gray-800">EduShare</span>
                </div>
                <p class="mt-4 text-gray-600">
                    Log in to access your saved notes, bookmarks, and tailored recommendations.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5 text-emerald-600" />
                        Fast search across subjects and tags
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5 text-emerald-600" />
                        Save, like, and comment on notes
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5 text-emerald-600" />
                        Smart recommendations for your courses
                    </li>
                </ul>
            </div>

            {{-- Right: Form --}}
            <div class="bg-white shadow-sm ring-1 ring-gray-100 rounded-2xl p-6 sm:p-8" x-data="{ showPassword:false }">
                <div class="mb-6">
                    {{-- Mobile logo --}}
                    <div class="md:hidden mb-2 flex items-center gap-3 justify-center">
                        <img src="{{ asset('images/edushare-logo.png') }}" alt="EduShare Logo" class="h-10 w-auto">
                        <span class="text-xl font-semibold text-gray-800">EduShare</span>
                    </div>
                    
                    <h1 class="text-xl font-semibold text-gray-900">Welcome back</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        New here?
                        <a href="{{ route('register') }}" class="font-medium text-emerald-600 hover:text-emerald-700">
                            Create an account
                        </a>
                    </p>
                </div>

                <x-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <x-label for="email" value="{{ __('Email') }}" class="text-gray-700" />
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                            </span>
                            <x-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                                class="pl-10 w-full focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="you@example.com"
                            />
                        </div>
                    </div>

                    {{-- Password (native input so Alpine can bind :type safely) --}}
                    <div class="mt-4">
                        <x-label for="password" value="{{ __('Password') }}" class="text-gray-700" />
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                            </span>
                            <input
                                id="password"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Your password"
                                class="pl-10 pr-10 w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                aria-label="Toggle password visibility"
                            >
                                <template x-if="!showPassword">
                                    <x-heroicon-o-eye class="h-5 w-5" />
                                </template>
                                <template x-if="showPassword">
                                    <x-heroicon-o-eye-slash class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
                    </div>

                    {{-- Remember me + Forgot --}}
                    <div class="mt-4 flex items-center justify-between">
                        <label for="remember_me" class="flex items-center gap-2">
                            <x-checkbox id="remember_me" name="remember" class="text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-emerald-600 hover:text-emerald-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                               href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="mt-6">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-white font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            {{ __('Log in') }}
                        </button>
                    </div>

                    {{-- Divider --}}
                    <div class="mt-6 flex items-center">
                        <div class="h-px flex-1 bg-gray-100"></div>
                        <span class="px-3 text-xs text-gray-400">or</span>
                        <div class="h-px flex-1 bg-gray-100"></div>
                    </div>

                    {{-- Link to Register (secondary) --}}
                    <div class="mt-4 text-center">
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Create a new account') }}
                        </a>
                    </div>
                </form>

                <p class="mt-8 text-center text-xs text-gray-500">
                    © {{ now()->year }} EduShare. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
