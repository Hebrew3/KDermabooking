<!-- Staff Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white/95 backdrop-blur-sm shadow-pink-lg border-r border-pink-100 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="staff-sidebar">
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Enhanced Logo Section -->
        <div class="flex items-center justify-between h-20 px-4 bg-gradient-to-br from-pink-500 via-rose-500 to-pink-600 shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 shadow-lg">
                    <img src="{{ asset('images/logo1.jpg') }}" alt="K-Derma Logo" class="h-10 w-10 rounded-lg object-cover">
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-bold text-xl tracking-wide">K-Derma</span>
                    <span class="text-pink-100 text-xs font-medium">Staff Portal</span>
                </div>
            </div>
            <!-- Close Button (Mobile Only) -->
            <button onclick="toggleSidebar()" class="lg:hidden p-2 text-white hover:bg-white/20 rounded-lg transition-colors duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar" style="max-height: calc(100vh - 180px);">
            <!-- Dashboard -->
            <x-sidebar-link 
                href="{{ route('staff.dashboard') }}" 
                :active="request()->routeIs('staff.dashboard')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </x-slot>
                Dashboard
            </x-sidebar-link>

            <!-- My Schedule -->
            <x-sidebar-link 
                href="{{ route('staff.schedule') }}" 
                :active="request()->routeIs('staff.schedule*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </x-slot>
                My Schedule
            </x-sidebar-link>

            <!-- My Leave Requests -->
            <x-sidebar-link 
                href="{{ route('staff.leave.index') }}" 
                :active="request()->routeIs('staff.leave.*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </x-slot>
                My Leave Requests
            </x-sidebar-link>

            <!-- Clients -->
            <x-sidebar-link 
                href="{{ route('staff.clients') }}" 
                :active="request()->routeIs('staff.clients*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </x-slot>
                Clients
                @php
                    $newAppointmentsCount = \App\Models\Appointment::where('staff_id', Auth::id())
                        ->where('created_at', '>=', now()->subHours(24))
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count();
                @endphp
                @if($newAppointmentsCount > 0)
                    <span class="ml-auto bg-pink-100 text-pink-800 text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ $newAppointmentsCount }}
                    </span>
                @endif
            </x-sidebar-link>

            <!-- Services -->
            <x-sidebar-link 
                href="{{ route('staff.services') }}" 
                :active="request()->routeIs('staff.services*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </x-slot>
                All Services
            </x-sidebar-link>

            <!-- My Assigned Services -->
            <x-sidebar-link 
                href="{{ route('staff.my-services') }}" 
                :active="request()->routeIs('staff.my-services*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </x-slot>
                My Services
                @if(Auth::user()->assignedServices->count() > 0)
                    <span class="ml-auto bg-pink-100 text-pink-800 text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ Auth::user()->assignedServices->count() }}
                    </span>
                @endif
            </x-sidebar-link>

            <!-- Messages -->
            <x-sidebar-link 
                href="{{ route('chat.index') }}" 
                :active="request()->routeIs('chat*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </x-slot>
                <span class="flex-1">Messages</span>
                <span id="messages-badge" class="ml-auto bg-pink-500 text-white text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
            </x-sidebar-link>
        </nav>

        <!-- Enhanced User Profile Section -->
        <div class="p-4 border-t border-pink-100 bg-gradient-to-r from-pink-50 to-rose-50">
            <div class="flex items-center space-x-3 p-4 bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-pink-100/50 hover:shadow-md transition-all duration-200">
                <div class="h-12 w-12 bg-gradient-to-br from-pink-500 to-rose-500 rounded-full flex items-center justify-center shadow-lg ring-2 ring-white/50">
                    <span class="text-white font-bold text-lg">{{ substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->first_name ?? Auth::user()->name }} {{ Auth::user()->last_name ?? '' }}</p>
                    <p class="text-xs text-pink-600 font-medium">Staff Member</p>
                    <div class="flex items-center mt-1">
                        <div class="h-2 w-2 bg-green-400 rounded-full mr-2"></div>
                        <span class="text-xs text-gray-500">Online</span>
                    </div>
                </div>
                <div class="relative">
                    <button class="p-2 text-gray-400 hover:text-pink-600 hover:bg-pink-50 rounded-lg transition-colors duration-200" onclick="toggleUserMenu()">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                    <!-- Enhanced User Menu Dropdown -->
                    <div id="user-menu" class="hidden absolute bottom-full right-0 mb-2 w-52 bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl border border-pink-100 py-2 z-50">
                        <div class="px-4 py-3 border-b border-pink-100">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name ?? Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('staff.profile') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-700 transition-colors duration-200">
                            <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button" onclick="confirmLogout()" class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-700 transition-colors duration-200">
                                <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('staff-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (sidebar && overlay) {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
}

function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}


// Close user menu when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const userButton = event.target.closest('button');

    if (userMenu && (!userButton || !userButton.onclick || userButton.onclick.toString().indexOf('toggleUserMenu') === -1)) {
        userMenu.classList.add('hidden');
    }
});

// Update messages notification badge
function updateMessagesBadge() {
    fetch('{{ route("chat.unread-count") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.getElementById('messages-badge');
        if (badge) {
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    })
    .catch(error => {
        console.error('Error fetching unread count:', error);
    });
}

// Update badge on page load
document.addEventListener('DOMContentLoaded', function() {
    updateMessagesBadge();
    // Update badge every 30 seconds
    setInterval(updateMessagesBadge, 30000);
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #ec4899;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #db2777;
}
</style>
