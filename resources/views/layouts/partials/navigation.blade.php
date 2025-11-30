<nav class="bg-white/80 backdrop-blur-md shadow-md fixed w-full z-50" style="overflow: visible;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="overflow: visible;">
        <div class="flex justify-between items-center h-16" style="overflow: visible;">
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
            <button id="menu-btn" class="md:hidden text-gray-700 hover:text-pink-600 focus:outline-none transition-colors duration-200">
                <svg class="h-7 w-7 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="hidden md:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none"></div>

    <!-- Mobile Menu Sidebar -->
    <div id="mobile-menu" class="md:hidden fixed top-0 right-0 h-screen w-80 max-w-[85vw] bg-gradient-to-br from-white to-pink-50/30 shadow-2xl z-[70] translate-x-full transition-transform duration-300 ease-in-out" style="overflow-y: auto; overflow-x: hidden; display: none;">
        <div class="px-6 py-6 h-full flex flex-col">
            @auth
                <!-- User Info Section -->
                <div class="mb-6 pb-6 border-b border-pink-200">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 bg-gradient-to-br from-pink-500 to-rose-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">{{ substr(auth()->user()->first_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Authenticated Client Menu -->
                <div class="space-y-2 flex-shrink-0">
                    <a href="{{ route('client.appointments.index') }}" class="flex items-center text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200 group">
                        <div class="h-10 w-10 rounded-lg bg-pink-100 group-hover:bg-pink-200 flex items-center justify-center mr-3 transition-colors duration-200">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        My Appointments
                    </a>
                    <a href="{{ route('index.landing') }}#services" class="flex items-center text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200 group">
                        <div class="h-10 w-10 rounded-lg bg-pink-100 group-hover:bg-pink-200 flex items-center justify-center mr-3 transition-colors duration-200">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        Services
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200 group">
                        <div class="h-10 w-10 rounded-lg bg-pink-100 group-hover:bg-pink-200 flex items-center justify-center mr-3 transition-colors duration-200">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        Profile
                    </a>
                    
                    <!-- Separator Line -->
                    <div class="my-4">
                        <hr class="border-pink-200">
                    </div>
                    
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="flex items-center w-full text-left text-gray-800 hover:text-red-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 transition-all duration-200 group">
                            <div class="h-10 w-10 rounded-lg bg-red-100 group-hover:bg-red-200 flex items-center justify-center mr-3 transition-colors duration-200">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <!-- Guest Menu -->
                <div class="space-y-2 mb-6 flex-shrink-0">
                    <a href="#home" class="block text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200">
                        Home
                    </a>
                    <a href="#services" class="block text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200">
                        Services
                    </a>
                    <a href="#about" class="block text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200">
                        About
                    </a>
                    <a href="#contact" class="block text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200">
                        Contact
                    </a>
                </div>
                
                <!-- Separator Line -->
                <div class="my-6">
                    <hr class="border-pink-200">
                </div>
                
                <!-- Auth Section -->
                <div class="space-y-3 flex-shrink-0">
                    <a href="{{ route('login') }}" class="block text-gray-800 hover:text-pink-600 font-semibold text-base py-3.5 px-4 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200 text-center">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="block w-full text-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-4 rounded-xl font-bold text-base shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                        Sign Up
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
<script>
    // Mobile menu sidebar functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing mobile menu...');
        
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        
        if (!menuBtn || !mobileMenu || !mobileMenuOverlay) {
            console.error('Required elements not found');
            return;
        }
        
        const icon = menuBtn.querySelector('svg path');
        if (!icon) {
            console.error('Menu icon not found');
            return;
        }
        
        const hamburgerPath = 'M4 6h16M4 12h16M4 18h16';
        const closePath = 'M6 18L18 6M6 6l12 12';
        
        function setIcon(isOpen) {
            if (icon) {
                icon.setAttribute('d', isOpen ? closePath : hamburgerPath);
            }
        }
        
        function closeMenu() {
            // Slide out menu
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('translate-x-full');
            // Fade out overlay
            mobileMenuOverlay.classList.remove('opacity-100', 'pointer-events-auto');
            mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
            // Hide after animation
            setTimeout(() => {
                mobileMenuOverlay.classList.add('hidden');
                mobileMenu.style.display = 'none';
            }, 300);
            setIcon(false);
            document.body.style.overflow = '';
        }
        
        function openMenu() {
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
            
            // Show overlay first
            mobileMenuOverlay.classList.remove('hidden');
            mobileMenuOverlay.classList.remove('pointer-events-none');
            mobileMenuOverlay.classList.add('pointer-events-auto');
            // Force reflow
            void mobileMenuOverlay.offsetWidth;
            // Fade in overlay
            mobileMenuOverlay.classList.remove('opacity-0');
            mobileMenuOverlay.classList.add('opacity-100');
            
            // Show and slide in menu - ensure it's visible
            mobileMenu.style.display = 'block';
            mobileMenu.style.visibility = 'visible';
            mobileMenu.style.opacity = '1';
            // Force reflow
            void mobileMenu.offsetWidth;
            // Remove translate-x-full and add translate-x-0
            mobileMenu.classList.remove('translate-x-full');
            mobileMenu.classList.add('translate-x-0');
            
            setIcon(true);
        }
        
        function toggleMenu() {
            const isOpen = mobileMenu.classList.contains('translate-x-0');
            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        }
        
        // Toggle menu on button click
        menuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleMenu();
        });
        
        // Close menu when clicking on overlay
        mobileMenuOverlay.addEventListener('click', function(e) {
            if (e.target === mobileMenuOverlay) {
                closeMenu();
            }
        });
        
        // Close menu when clicking on a link
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(() => closeMenu(), 100);
            });
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && mobileMenu.classList.contains('translate-x-0')) {
                closeMenu();
            }
        });
    });
</script>
