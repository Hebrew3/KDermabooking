<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 py-20">
        <!-- Background decorations -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-200/30 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">My Appointments</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    View and manage your upcoming and past appointments.
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <a href="{{ route('appointments.book') }}"
                    class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-rose-200/80 transition-all text-center">
                    Book New Appointment
                </a>
                <a href="{{ route('index.landing') }}"
                    class="bg-white hover:bg-gray-50 text-gray-800 px-6 py-3 rounded-xl font-semibold border border-gray-200 transition-colors text-center">
                    Back to Home
                </a>
            </div>

            <!-- Appointments List -->
            <div class="space-y-6">
                @forelse($appointments as $appointment)
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-pink-100 hover:shadow-xl transition-shadow">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <!-- Appointment Info -->
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <!-- Status Badge -->
                                    <div class="flex-shrink-0">
                                        @switch($appointment->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    Pending
                                                </span>
                                                @break
                                            @case('confirmed')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Confirmed
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Completed
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    Cancelled
                                                </span>
                                                @break
                                        @endswitch
                                    </div>

                                    <!-- Appointment Details -->
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $appointment->service->name }}</h3>
                                        <div class="space-y-1 text-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $appointment->formatted_date }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $appointment->formatted_time }}
                                            </div>
                                            @if($appointment->staff)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $appointment->staff->first_name }} {{ $appointment->staff->last_name }}
                                                </div>
                                            @endif
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </svg>
                                                {{ $appointment->service->formatted_price }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('appointments.show', $appointment) }}"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors text-center">
                                    View Details
                                </a>
                                @if($appointment->status === 'completed' && !$appointment->hasClientFeedback())
                                    <a href="{{ route('appointments.show', $appointment) }}#feedback"
                                       class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-lg font-medium transition-colors text-center">
                                        Leave Feedback
                                    </a>
                                @endif
                                @if(in_array($appointment->status, ['pending', 'confirmed']) && $appointment->appointment_date >= now()->format('Y-m-d'))
                                    <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="inline" onsubmit="return confirmCancelAppointment(event)">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="cancellation_reason" id="cancellation_reason_{{ $appointment->id }}">
                                        <button type="submit" 
                                            class="w-full bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-lg font-medium transition-colors">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if($appointment->notes)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-sm text-gray-600"><strong>Notes:</strong> {{ $appointment->notes }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-pink-100">
                        <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Appointments Yet</h3>
                        <p class="text-gray-600 mb-6">You haven't booked any appointments yet. Schedule your first consultation today!</p>
                        <a href="{{ route('appointments.book') }}"
                            class="inline-block bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-rose-200/80 transition-all">
                            Book Your First Appointment
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($appointments->hasPages())
                <div class="mt-8">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- JavaScript for appointment cancellation -->
    <script>
        function confirmCancelAppointment(event) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Cancel Appointment?',
                text: 'Please provide a reason for cancellation:',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Enter cancellation reason...',
                inputAttributes: {
                    'aria-label': 'Cancellation reason'
                },
                showCancelButton: true,
                confirmButtonColor: '#ec4899',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, cancel it!',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to provide a reason for cancellation!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set the cancellation reason
                    const form = event.target;
                    const reasonInput = form.querySelector('input[name="cancellation_reason"]');
                    reasonInput.value = result.value;
                    
                    // Submit the form
                    form.submit();
                }
            });
            
            return false;
        }
    </script>
</x-layout>
