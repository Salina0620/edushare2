<x-guest-layout>
    {{-- Alpine for toggles. If your layout already includes Alpine, remove this line. --}}
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
                    Create an account to share notes, save favorites, and get smart recommendations.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5 text-emerald-600" />
                        Upload notes and past papers
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5 text-emerald-600" />
                        Follow subjects and tags
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5 text-emerald-600" />
                        Like, bookmark, and comment
                    </li>
                </ul>
            </div>

            {{-- Right: Form --}}
            <div class="bg-white shadow-sm ring-1 ring-gray-100 rounded-2xl p-6 sm:p-8"
                 x-data="{ showPassword:false, showPassword2:false, pw:'', meter:0 }"
                 x-init="$watch('pw', v => {
                    let s = 0;
                    if ((v||'').length >= 8) s++;
                    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
                    if (/\d/.test(v)) s++;
                    if (/[^A-Za-z0-9]/.test(v)) s++;
                    meter = s;
                 })">

                <div class="mb-6">
                    <div class="md:hidden mb-2 flex items-center gap-3">
                        <img src="{{ asset('images/edushare-logo.png') }}" alt="EduShare Logo" class="h-10 w-auto">
                        <span class="text-xl font-semibold text-gray-800">EduShare</span>
                    </div>
                    <h1 class="text-xl font-semibold text-gray-900">Create your account</h1>
                    <p class="mt-1 text-sm text-gray-600">Already have one?
                        <a href="{{ route('login') }}" class="font-medium text-emerald-600 hover:text-emerald-700">Log in</a>
                    </p>
                </div>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <x-label for="name" value="{{ __('Name') }}" class="text-gray-700" />
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-user class="h-5 w-5 text-gray-400" />
                            </span>
                            <x-input
                                id="name"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                class="pl-10 w-full focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Your full name"
                            />
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mt-4">
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
                                autocomplete="username"
                                class="pl-10 w-full focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="you@example.com"
                            />
                        </div>
                    </div>

                    {{-- Password (native input so Alpine can control type) --}}
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
                                autocomplete="new-password"
                                x-model="pw"
                                placeholder="At least 8 characters"
                                class="pl-10 pr-10 w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    aria-label="Toggle password visibility">
                                <template x-if="!showPassword">
                                    <x-heroicon-o-eye class="h-5 w-5" />
                                </template>
                                <template x-if="showPassword">
                                    <x-heroicon-o-eye-slash class="h-5 w-5" />
                                </template>
                            </button>
                        </div>

                        {{-- Simple strength meter --}}
                        <div class="mt-2">
                            <div class="h-1.5 w-full rounded bg-gray-100 overflow-hidden">
                                <div class="h-full"
                                     :class="{
                                        'bg-red-400': meter===1,
                                        'bg-yellow-400': meter===2,
                                        'bg-emerald-400': meter>=3
                                     }"
                                     :style="`width:${(meter/4)*100}%`"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500" x-text="meter<=1 ? 'Weak' : (meter==2 ? 'Okay' : 'Good')"></p>
                        </div>
                    </div>

                    {{-- Confirm Password (native input so Alpine can control type) --}}
                    <div class="mt-4">
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-gray-700" />
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-lock-closed class="h-5 w-5 text-gray-400" />
                            </span>
                            <input
                                id="password_confirmation"
                                x-bind:type="showPassword2 ? 'text' : 'password'"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter your password"
                                class="pl-10 pr-10 w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <button type="button"
                                    @click="showPassword2 = !showPassword2"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    aria-label="Toggle confirm password visibility">
                                <template x-if="!showPassword2">
                                    <x-heroicon-o-eye class="h-5 w-5" />
                                </template>
                                <template x-if="showPassword2">
                                    <x-heroicon-o-eye-slash class="h-5 w-5" />
                                </template>
                            </button>
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-4">
                            <label for="terms" class="flex items-start gap-3">
                                <x-checkbox name="terms" id="terms" required class="mt-1 focus:ring-emerald-500 text-emerald-600" />
                                <span class="text-sm text-gray-600">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="font-medium text-emerald-600 hover:text-emerald-700">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="font-medium text-emerald-600 hover:text-emerald-700">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                </span>
                            </label>
                        </div>
                    @endif

                    <div class="mt-6">
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-white font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            {{ __('Create account') }}
                        </button>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Already registered? Log in') }}
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
