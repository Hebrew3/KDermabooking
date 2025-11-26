<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Hero Background with same styling as landing page -->
        <section class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 overflow-hidden min-h-screen">
            <!-- Decorative gradient blur elements -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-200/30 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex items-center min-h-screen">
                <div class="max-w-md w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-pink-100">
                        <div class="space-y-8">
            <!-- Login Form -->
            <!-- Session Status -->
            <x-auth-session-status class="mb-6 p-4 bg-pink-50 border border-pink-200 rounded-lg text-pink-800"
                :status="session('status')" />

            <!-- Error Messages -->
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm">
                @csrf

                <!-- Header Section -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                    <p class="text-gray-600">Sign in to your K-Derma account</p>
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email Address')" class="text-neutral-700 font-medium mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                        </div>
                        <x-text-input id="email"
                            class="block w-full pl-10 pr-3 py-3 border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200 bg-pink-50/50"
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            placeholder="Enter your email" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-neutral-700 font-medium mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <x-text-input id="password"
                            class="block w-full pl-10 pr-12 py-3 border border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200 bg-pink-50/50"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="Enter your password" />
                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="password-eye" class="h-5 w-5 text-pink-400 hover:text-pink-600 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600 text-sm" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-pink-300 text-pink-600 shadow-sm focus:ring-pink-500 focus:ring-offset-0"
                            name="remember">
                        <span class="ml-2 text-sm text-neutral-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-pink-600 hover:text-pink-700 font-medium transition-colors duration-200"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <div>
                    <button type="submit" id="loginButton"
                        class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-pink-500 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors duration-200">
                        <svg id="loginIcon" class="h-5 w-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <svg id="loginSpinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span id="loginButtonText">{{ __('Sign In') }}</span>
                    </button>
                </div>

                @if(config('services.google.client_id') && config('services.google.client_secret'))
                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-pink-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white/80 text-gray-500">Or continue with</span>
                    </div>
                </div>

                <!-- Google Login Button -->
                <div>
                    <a href="{{ route('auth.google') }}"
                        class="w-full flex justify-center items-center py-3 px-4 border border-pink-200 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all duration-200">
                        <svg class="h-5 w-5 mr-3 flex-shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-gray-700">Sign in with Google</span>
                    </a>
                </div>
                @endif

                <!-- Register Link -->
                @if (Route::has('register'))
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}"
                                class="font-medium text-pink-600 hover:text-pink-700 transition-colors duration-200">
                                Sign up here
                            </a>
                        </p>
                    </div>
                @endif
            </form>

                            <!-- Footer -->
                            <div class="text-center">
                                <p class="text-xs text-gray-500">
                                    © {{ date('Y') }} K-Derma Booking System. All rights reserved.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            // Password toggle functionality
            function togglePassword(fieldId) {
                const passwordField = document.getElementById(fieldId);
                const eyeIcon = document.getElementById(fieldId + '-eye');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    `;
                } else {
                    passwordField.type = 'password';
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    `;
                }
            }

            // Login form submission
            document.getElementById('loginForm').addEventListener('submit', function() {
                const button = document.getElementById('loginButton');
                const icon = document.getElementById('loginIcon');
                const spinner = document.getElementById('loginSpinner');
                const text = document.getElementById('loginButtonText');

                // Disable button and show loading state
                button.disabled = true;
                icon.classList.add('hidden');
                spinner.classList.remove('hidden');
                text.textContent = 'Signing In...';
            });
        </script>
    </body>
</html>
