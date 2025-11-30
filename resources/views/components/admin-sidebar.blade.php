<!-- Admin Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white/95 backdrop-blur-sm shadow-pink-lg border-r border-pink-100 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="admin-sidebar">
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Enhanced Logo Section -->
        <div class="flex items-center justify-center h-20 px-4 bg-gradient-to-br from-pink-500 via-rose-500 to-pink-600 shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 shadow-lg">
                    <img src="{{ asset('images/logo1.jpg') }}" alt="K-Derma Logo" class="h-10 w-10 rounded-lg object-cover">
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-bold text-xl tracking-wide">K-Derma</span>
                    <span class="text-pink-100 text-xs font-medium">Admin Panel</span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
            <!-- Dashboard -->
            <x-sidebar-link 
                href="{{ route('admin.dashboard') }}" 
                :active="request()->routeIs('admin.dashboard')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </x-slot>
                Dashboard
            </x-sidebar-link>

            <!-- Client Records / User Management -->
            <x-sidebar-link 
                href="{{ route('admin.users.index') }}" 
                :active="request()->routeIs('admin.users.*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </x-slot>
                User Management
            </x-sidebar-link>

            <!-- Appointments -->
            <x-sidebar-link 
                href="{{ route('admin.appointments.index') }}" 
                :active="request()->routeIs('admin.appointments*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </x-slot>
                Appointments
            </x-sidebar-link>

            <!-- Services -->
            <x-sidebar-link 
                href="{{ route('admin.services.index') }}" 
                :active="request()->routeIs('admin.services.*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </x-slot>
                Services
            </x-sidebar-link>

            <!-- Staff Management Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.staff-services.*') || request()->routeIs('admin.staff-schedule.*') || request()->routeIs('admin.staff-leave.*') ? 'true' : 'false' }} }" class="space-y-2">
                <button @click="open = !open" 
                        :class="open || request()->routeIs('admin.staff-services.*') || request()->routeIs('admin.staff-schedule.*') || request()->routeIs('admin.staff-leave.*') ? 'bg-pink-50 text-pink-600 border-l-4 border-pink-500' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors duration-200 group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" :class="open || request()->routeIs('admin.staff-services.*') || request()->routeIs('admin.staff-schedule.*') || request()->routeIs('admin.staff-leave.*') ? 'text-pink-600' : 'text-gray-500 group-hover:text-pink-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="font-medium">Staff Management</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="ml-8 space-y-1">
                    <!-- Staff Services -->
                    <a href="{{ route('admin.staff-services.index') }}" 
                       class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.staff-services.*') ? 'bg-pink-50 text-pink-600 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Staff Services</span>
                    </a>

                    <!-- Staff Schedule -->
                    <a href="{{ route('admin.staff-schedule.index') }}" 
                       class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.staff-schedule.index') || request()->routeIs('admin.staff-schedule.edit') ? 'bg-pink-50 text-pink-600 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Staff Schedule</span>
                    </a>

                    <!-- Time Slot Availability -->
                    <a href="{{ route('admin.staff-schedule.time-slots') }}" 
                       class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.staff-schedule.time-slots') ? 'bg-pink-50 text-pink-600 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Time Slot Availability</span>
                    </a>

                    <!-- Staff Leave Requests -->
                    <a href="{{ route('admin.staff-leave.index') }}" 
                       class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.staff-leave.*') ? 'bg-pink-50 text-pink-600 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="flex-1">Staff Leave Requests</span>
                        <span id="leave-requests-badge" class="ml-auto bg-pink-500 text-white text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                    </a>
                </div>
            </div>

            <!-- Inventory -->
            <x-sidebar-link 
                href="{{ route('admin.inventory.index', ['show_alert' => '1']) }}" 
                :active="request()->routeIs('admin.inventory*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </x-slot>
                Inventory
            </x-sidebar-link>

            <!-- POS -->
            <x-sidebar-link 
                href="{{ route('admin.pos.index') }}" 
                :active="request()->routeIs('admin.pos*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </x-slot>
                Point of Sale
            </x-sidebar-link>

            <!-- Sales -->
            <x-sidebar-link 
                href="{{ route('admin.sales.index') }}" 
                :active="request()->routeIs('admin.sales*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </x-slot>
                Sales Analytics
            </x-sidebar-link>

            <!-- Analytics -->
            <x-sidebar-link 
                href="{{ route('admin.analytics') }}" 
                :active="request()->routeIs('admin.analytics*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </x-slot>
                Analytics
            </x-sidebar-link>

            <!-- Settings -->
            <x-sidebar-link 
                href="{{ route('admin.settings.index') }}" 
                :active="request()->routeIs('admin.settings*')"
            >
                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </x-slot>
                Settings
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
                    <p class="text-xs text-pink-600 font-medium">Administrator</p>
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
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-700 transition-colors duration-200">
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
    const sidebar = document.getElementById('admin-sidebar');
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

    // Update leave requests notification badge and show pop-up
    function updateLeaveRequestsBadge() {
        fetch('{{ route("admin.staff-leave.pending-count") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('leave-requests-badge');
            if (badge) {
                if (data.pending_count > 0) {
                    badge.textContent = data.pending_count > 99 ? '99+' : data.pending_count;
                    badge.classList.remove('hidden');
                    
                    // Show pop-up alert if there are pending requests and user hasn't been notified yet
                    const lastNotification = sessionStorage.getItem('leaveRequestsLastNotification');
                    const currentCount = data.pending_count;
                    
                    if (!lastNotification || parseInt(lastNotification) !== currentCount) {
                        // Show SweetAlert pop-up
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Pending Leave Requests',
                                html: `<div class="text-left">
                                    <p class="mb-3">You have <strong class="text-pink-600">${currentCount}</strong> pending leave request${currentCount > 1 ? 's' : ''} that need${currentCount === 1 ? 's' : ''} your review.</p>
                                    <p class="text-sm text-gray-600">Click on "Staff Leave Requests" to review and approve or reject them.</p>
                                </div>`,
                                icon: 'info',
                                iconColor: '#ec4899',
                                confirmButtonColor: '#ec4899',
                                confirmButtonText: 'View Requests',
                                showCancelButton: true,
                                cancelButtonText: 'Later',
                                reverseButtons: true,
                                allowOutsideClick: false,
                                allowEscapeKey: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route("admin.staff-leave.index", ["status" => "pending"]) }}';
                                }
                                // Store notification to prevent showing again for same count
                                sessionStorage.setItem('leaveRequestsLastNotification', currentCount.toString());
                            });
                        }
                    }
                } else {
                    badge.classList.add('hidden');
                    // Clear notification if count is 0
                    sessionStorage.removeItem('leaveRequestsLastNotification');
                }
            }
        })
        .catch(error => {
            console.error('Error fetching pending leave requests count:', error);
        });
    }

    // Update badge on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLeaveRequestsBadge();
        // Update badge every 30 seconds
        setInterval(updateLeaveRequestsBadge, 30000);
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
