<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-50 via-white to-rose-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-gradient-to-br from-pink-500 to-rose-500 rounded-full flex items-center justify-center shadow-lg mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">Verify Your Email</h2>
                <p class="mt-2 text-sm text-gray-600">
                    We've sent a verification code to<br>
                    <span class="font-semibold text-pink-600">{{ $email }}</span>
                </p>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm text-red-800">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Verification Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-pink-100">
                <form method="POST" action="{{ route('verification.verify-code') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Enter Verification Code
                        </label>
                        <div class="mt-1">
                            <input 
                                id="verification_code" 
                                name="verification_code" 
                                type="text" 
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                required 
                                autofocus
                                autocomplete="one-time-code"
                                class="appearance-none block w-full px-4 py-4 border-2 border-pink-200 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-center text-3xl font-bold tracking-widest bg-pink-50"
                                placeholder="000000"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                            >
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Enter the 6-digit code sent to your email address
                        </p>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all duration-200"
                        >
                            Verify Email
                        </button>
                    </div>
                </form>

                <!-- Resend Code -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600 mb-4">
                        Didn't receive the code?
                    </p>
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button 
                            type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-pink-300 rounded-xl text-sm font-medium text-pink-600 bg-white hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all duration-200"
                        >
                            Resend Verification Code
                        </button>
                    </form>
                    <p class="mt-3 text-xs text-center text-gray-500">
                        Code expires in 15 minutes
                    </p>
                </div>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                        ← Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

