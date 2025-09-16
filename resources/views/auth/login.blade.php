<x-guest-layout>
    {{-- Full screen background with gradient --}}
    <div class="min-h-screen w-full flex items-center justify-center relative overflow-hidden"
        style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #cbd5e1 100%);">

        {{-- Decorative background elements --}}
        <div class="absolute inset-0">
            {{-- Top left decoration --}}
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-blue-200 opacity-20 rounded-full blur-3xl"></div>
            {{-- Bottom right decoration --}}
            <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-indigo-200 opacity-20 rounded-full blur-3xl"></div>
            {{-- Center decoration --}}
            <div class="absolute top-1/3 left-1/4 w-64 h-64 bg-sky-200 opacity-15 rounded-full blur-2xl"></div>

            {{-- Subtle grid pattern --}}
            <div class="absolute inset-0 opacity-10">
                <div class="w-full h-full"
                    style="background-image: linear-gradient(rgba(71, 85, 105, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(71, 85, 105, 0.1) 1px, transparent 1px); background-size: 50px 50px;">
                </div>
            </div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            {{-- Logo & Title Section - More Compact --}}
            <div class="text-center mb-6">
                <div class="inline-block p-3 bg-white rounded-2xl shadow-lg mb-4">
                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ asset('images/logo.jpeg') }}"
                        alt="Logo Sekolah">
                </div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">
                    Sistem Pembayaran Sekolah
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    MA Modern Miftahussa'adah Cimahi
                </p>
            </div>

            {{-- Compact Login Card --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email Field --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username" placeholder="Masukkan email atau username"
                                class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Password Field --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required
                                autocomplete="current-password" placeholder="Masukkan password"
                                class="w-full pl-9 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm">

                            {{-- Toggle Password Visibility --}}
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600 focus:outline-none">
                                <svg id="eye-open" class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-closed" class="h-4 w-4 text-gray-400 hidden" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                Ingat saya
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-sm">
                                <a href="{{ route('password.request') }}"
                                    class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                    Lupa password?
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Login Button --}}
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Masuk
                        </button>
                    </div>
                </form>
            </div>

            {{-- Compact Footer --}}
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} MA Modern Miftahussa'adah Cimahi. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    {{-- JavaScript for password toggle --}}
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>
