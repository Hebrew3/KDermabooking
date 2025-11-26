<!-- Mobile Header -->
<div class="lg:hidden bg-white/90 backdrop-blur-sm shadow-pink-sm border-b border-pink-100 sticky top-0 z-40">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Menu Button -->
        <button onclick="toggleSidebar()" class="p-2 text-neutral-600 hover:text-pink-600 transition-colors duration-200">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <div class="h-8 w-8 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="text-lg font-bold text-neutral-800">K-Derma</span>
        </div>

        <!-- User Avatar -->
        <div class="h-8 w-8 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full flex items-center justify-center">
            <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
        </div>
    </div>
</div>
