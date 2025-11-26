<nav class="bg-white/80 backdrop-blur-md shadow-md fixed w-full z-50 ">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="#home" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo1.jpg') }}" alt="K-Derma Logo"
                    class="h-10 w-10 rounded-lg object-cover shadow-sm">
                <span
                    class="text-2xl font-extrabold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                    KDERMA
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#home" class="nav-link text-gray-800 hover:text-pink-600 font-medium ">Home</a>
                <a href="#services" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">Services</a>
                <a href="#about" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">About</a>
                <a href="#contact" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">Contact</a>
                <a href="#my-bookings" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">My Bookings</a>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-600 font-medium ">Login</a>
                <a href="{{ route('register') }}"
                    class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-5 py-2 rounded-full font-medium shadow-md hover:shadow-lg transition-all">
                    Sign Up
                </a>
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
            <a href="#home" class="block text-gray-900 hover:text-pink-600 font-medium">Home</a>
            <a href="#services" class="block text-gray-700 hover:text-pink-600 font-medium">Services</a>
            <a href="#about" class="block text-gray-700 hover:text-pink-600 font-medium">About</a>
            <a href="#contact" class="block text-gray-700 hover:text-pink-600 font-medium">Contact</a>
            <a href="#my-bookings" class="nav-link text-gray-700 hover:text-pink-600 font-medium ">My Bookings</a>
            <div class="border-t border-gray-200 pt-3">
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Login</a>
                <a href="{{ route('register') }}"
                    class="block text-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-5 py-2 rounded-full font-medium mt-2 shadow-md hover:shadow-lg transition-all">
                    Sign Up
                </a>
            </div>
        </div>
    </div>
</nav>
