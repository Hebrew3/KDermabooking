<nav class="bg-white/80 backdrop-blur-md shadow-md fixed w-full z-50 ">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="{{ route('index.landing') }}#home" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo1.jpg') }}" alt="K-Derma Logo"
                    class="h-10 w-10 rounded-lg object-cover shadow-sm">
                <span
                    class="text-2xl font-extrabold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    KDERMA
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('index.landing') }}#home" class="nav-link text-gray-800 hover:text-pink-600 font-medium ">Home</a>
                <a href="{{ route('index.landing') }}#services" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">Services</a>
                <a href="{{ route('index.landing') }}#about" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">About</a>
                <a href="{{ route('index.landing') }}#contact" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">Contact</a>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <!-- Authenticated User Menu -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-pink-600 font-medium">
                            <span>{{ auth()->user()->first_name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="{{ route('client.appointments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    My Appointments
                                </a>
                                <a href="{{ route('index.landing') }}#services" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Services
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Guest User Buttons -->
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-600 font-medium ">Login</a>
                    <a href="{{ route('register') }}"
                        class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-5 py-2 rounded-full font-medium shadow-md hover:shadow-lg transition-all">
                        Sign Up
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <button id="menu-btn" class="md:hidden text-gray-700 hover:text-pink-600 focus:outline-none">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-sm ">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('index.landing') }}#home" class="block text-gray-900 hover:text-pink-600 font-medium">Home</a>
            <a href="{{ route('index.landing') }}#services" class="block text-gray-700 hover:text-pink-600 font-medium">Services</a>
            <a href="{{ route('index.landing') }}#about" class="block text-gray-700 hover:text-pink-600 font-medium">About</a>
            <a href="{{ route('index.landing') }}#contact" class="block text-gray-700 hover:text-pink-600 font-medium">Contact</a>
            
            @auth
                <!-- Authenticated Mobile Menu -->
                <div class="border-t border-gray-200 pt-3">
                    <div class="text-sm text-gray-500 mb-2">Welcome, {{ auth()->user()->first_name }}!</div>
                    <a href="{{ route('client.appointments.index') }}" class="block text-gray-700 hover:text-pink-600 font-medium py-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        My Appointments
                    </a>
                    <a href="{{ route('client.services') }}" class="block text-gray-700 hover:text-pink-600 font-medium py-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Services
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block text-gray-700 hover:text-pink-600 font-medium py-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left text-gray-700 hover:text-pink-600 font-medium py-2">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <!-- Guest Mobile Menu -->
                <div class="border-t border-gray-200 pt-3">
                    <a href="{{ route('login') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Login</a>
                    <a href="{{ route('register') }}"
                        class="block text-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-5 py-2 rounded-full font-medium mt-2 shadow-md hover:shadow-lg transition-all">
                        Sign Up
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
