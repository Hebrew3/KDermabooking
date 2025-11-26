<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 py-20">
        <!-- Background decorations -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-rose-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    My <span class="bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">Bookings</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    View and manage your appointments with ease
                </p>
                <a href="{{ route('client.appointments.create') }}" class="inline-flex items-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-8 py-4 rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Book New Appointment
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Upcoming Appointments -->
            @if($upcomingAppointments->count() > 0)
            <div class="mb-12">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Upcoming Appointments</h2>
                    <div class="flex items-center space-x-2 text-pink-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold">{{ $upcomingAppointments->count() }} Upcoming</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($upcomingAppointments as $appointment)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 border border-pink-100 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $appointment->service->name }}</h3>
                                <div class="flex items-center text-gray-600 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $appointment->formatted_date_time }}</span>
                                </div>
                            </div>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $appointment->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </div>
                        <div class="space-y-3 mb-6">
                            @if($appointment->staff)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium">{{ $appointment->staff->name }}</span>
                            </div>
                            @endif
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="font-bold text-lg text-gray-900">{{ $appointment->formatted_total }}</span>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('client.appointments.show', $appointment) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-xl font-medium text-center transition-colors duration-200">
                                View Details
                            </a>
                            @if($appointment->canBeRescheduled())
                            <a href="{{ route('client.appointments.edit', $appointment) }}" class="flex-1 bg-pink-100 hover:bg-pink-200 text-pink-700 px-4 py-3 rounded-xl font-medium text-center transition-colors duration-200">
                                Reschedule
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-pink-100">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-pink-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-900">Filter Appointments</h3>
                </div>
                <!-- Quick Filter Buttons -->
                <div class="mb-6">
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('client.appointments.index') }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ !request()->hasAny(['status', 'date_from', 'date_to']) ? 'bg-pink-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            All Appointments
                        </a>
                        <a href="{{ route('client.appointments.index', ['status' => 'completed']) }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ request('status') === 'completed' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Past Appointments
                        </a>
                        <a href="{{ route('client.appointments.index', ['status' => 'pending']) }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Pending
                        </a>
                        <a href="{{ route('client.appointments.index', ['status' => 'confirmed']) }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ request('status') === 'confirmed' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Confirmed
                        </a>
                        <a href="{{ route('client.appointments.index', ['status' => 'cancelled']) }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 {{ request('status') === 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Cancelled
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('client.appointments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">Status</label>
                        <select name="status" id="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-3">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-3">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200">
                    </div>
                    <div class="flex items-end">
                        <div class="flex space-x-3 w-full">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                                Apply Filters
                            </button>
                            <a href="{{ route('client.appointments.index') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold text-center transition-colors duration-200">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- All Appointments -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-pink-100">
                <div class="px-8 py-6 bg-gradient-to-r from-pink-50 to-rose-50 border-b border-pink-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">All Appointments</h2>
                        <div class="flex items-center space-x-2 text-pink-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="font-semibold">{{ $appointments->count() }} Total</span>
                        </div>
                    </div>
                </div>

                @if($appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Appointment #</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Service</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Date & Time</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Staff</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Rating</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($appointments as $appointment)
                            <tr class="hover:bg-pink-50 transition-colors duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $appointment->appointment_number }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $appointment->service->name }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $appointment->formatted_date_time }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $appointment->staff ? $appointment->staff->name : 'To be assigned' }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $appointment->status_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($appointment->status === 'completed' && $appointment->hasClientFeedback())
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= $appointment->client_rating; $i++)
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-xs text-gray-600">({{ $appointment->client_rating }})</span>
                                        </div>
                                    @elseif($appointment->status === 'completed')
                                        <span class="text-xs text-gray-400 italic">No rating</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $appointment->formatted_total }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('client.appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">View</a>
                                        @if($appointment->canBeRescheduled())
                                        <a href="{{ route('client.appointments.edit', $appointment) }}" class="text-pink-600 hover:text-pink-800 font-medium text-sm">Reschedule</a>
                                        @endif
                                        @if($appointment->canBeCancelled())
                                        <button onclick="openCancelModal({{ $appointment->id }})" class="text-red-600 hover:text-red-800 font-medium text-sm">Cancel</button>
                                        @endif
                                        @if($appointment->status === 'completed' && !$appointment->hasClientFeedback())
                                        <a href="{{ route('client.appointments.show', $appointment) }}#feedback" class="text-green-600 hover:text-green-800 font-medium text-sm">Leave Feedback</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
                    {{ $appointments->withQueryString()->links() }}
                </div>
                @else
                <div class="px-8 py-16 text-center">
                    <div class="mx-auto w-24 h-24 bg-pink-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No appointments found</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">Book your first appointment to get started with our premium dermatological services.</p>
                    <a href="{{ route('client.appointments.create') }}" class="inline-flex items-center bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-8 py-4 rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Book Your First Appointment
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Cancel Appointment Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-6 w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Cancel Appointment</h3>
                    </div>
                    <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-6">
                        <label for="cancellation_reason" class="block text-sm font-semibold text-gray-700 mb-3">Reason for cancellation</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="4" required class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" placeholder="Please provide a reason for cancelling this appointment..."></textarea>
                    </div>
                    <div class="flex space-x-4">
                        <button type="button" onclick="closeCancelModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-colors duration-200">
                            Keep Appointment
                        </button>
                        <x-confirm-button action="delete" title="Cancel Appointment" text="Are you sure you want to cancel this appointment? This action cannot be undone!" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold">
                            Cancel Appointment
                        </x-confirm-button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        function openCancelModal(appointmentId) {
            const modal = document.getElementById('cancelModal');
            const form = document.getElementById('cancelForm');
            form.action = `/client/appointments/${appointmentId}/cancel`;
            modal.classList.remove('hidden');
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
            document.getElementById('cancellation_reason').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
    </script>
</x-layouts.client>
