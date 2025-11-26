<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 py-20">
        <!-- Background decorations -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-200/30 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">Appointment Details</h1>
                <p class="text-lg text-gray-600">
                    View your appointment information and status.
                </p>
            </div>

            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('appointments.index') }}"
                    class="inline-flex items-center text-gray-600 hover:text-gray-800 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to My Appointments
                </a>
            </div>

            <!-- Appointment Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12 border border-pink-100">
                <!-- Status Badge -->
                <div class="flex justify-center mb-8">
                    @switch($appointment->status)
                        @case('pending')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-yellow-100 text-yellow-800">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Pending Confirmation
                            </span>
                            @break
                        @case('confirmed')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-green-100 text-green-800">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Confirmed
                            </span>
                            @break
                        @case('completed')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-blue-100 text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Completed
                            </span>
                            @break
                        @case('cancelled')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-red-100 text-red-800">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Cancelled
                            </span>
                            @break
                    @endswitch
                </div>

                <!-- Appointment Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Service Details -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Service Information</h3>
                            <div class="bg-pink-50 rounded-xl p-4 space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Service</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $appointment->service->name }}</p>
                                </div>
                                @if($appointment->service->description)
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Description</label>
                                        <p class="text-gray-700">{{ $appointment->service->short_description ?? Str::limit($appointment->service->description, 150) }}</p>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center pt-2 border-t border-pink-100">
                                    <span class="text-sm font-medium text-gray-600">Price</span>
                                    <span class="text-xl font-bold text-pink-600">{{ $appointment->service->formatted_price }}</span>
                                </div>
                                @if($appointment->service->duration)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">Duration</span>
                                        <span class="text-gray-900">{{ $appointment->service->formatted_duration }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Appointment Details</h3>
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Date</label>
                                        <p class="text-gray-900 font-semibold">{{ $appointment->formatted_date }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Time</label>
                                        <p class="text-gray-900 font-semibold">{{ $appointment->formatted_time }}</p>
                                    </div>
                                </div>

                                @if($appointment->staff)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <div>
                                            <label class="text-sm font-medium text-gray-600">Staff</label>
                                            <p class="text-gray-900 font-semibold">{{ $appointment->staff->first_name }} {{ $appointment->staff->last_name }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Booked On</label>
                                        <p class="text-gray-900">{{ $appointment->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                @if($appointment->notes)
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Additional Notes</h3>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <p class="text-gray-700">{{ $appointment->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Feedback Section (for completed appointments) -->
                @if($appointment->status === 'completed')
                    <div id="feedback" class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Your Feedback</h3>

                        @if(session('success'))
                            <p class="mb-4 text-sm text-green-600">{{ session('success') }}</p>
                        @endif
                        @if(session('error'))
                            <p class="mb-4 text-sm text-red-600">{{ session('error') }}</p>
                        @endif

                        @if($appointment->hasClientFeedback())
                            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-700">Rating:</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= $appointment->client_rating; $i++)
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-xs text-gray-600">({{ $appointment->client_rating }})</span>
                                    </div>
                                </div>
                                @if($appointment->client_feedback)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-1">Comments</p>
                                        <p class="text-gray-900 bg-white rounded-lg p-3 border border-gray-100">{{ $appointment->client_feedback }}</p>
                                    </div>
                                @endif

                                @if($appointment->hasAdminFeedbackReply())
                                    <div class="pt-3 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-700 mb-1">Clinic Reply</p>
                                        <p class="text-gray-900 bg-white rounded-lg p-3 border border-gray-100">{{ $appointment->admin_feedback_reply }}</p>
                                        @if($appointment->admin_feedback_replied_at)
                                            <p class="text-xs text-gray-500 mt-1">Replied on {{ $appointment->admin_feedback_replied_at->format('M d, Y g:i A') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <form method="POST" action="{{ route('appointments.feedback', $appointment) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="client_rating" class="block text-sm font-medium text-gray-700 mb-1">Rating <span class="text-red-500">*</span></label>
                                    <select name="client_rating" id="client_rating" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">Select rating</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ old('client_rating') == $i ? 'selected' : '' }}>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                    @error('client_rating')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="client_feedback" class="block text-sm font-medium text-gray-700 mb-1">Comments (Optional)</label>
                                    <textarea name="client_feedback" id="client_feedback" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Share your experience with this service...">{{ old('client_feedback') }}</textarea>
                                    @error('client_feedback')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                        Submit Feedback
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                    @if(in_array($appointment->status, ['pending', 'confirmed']) && $appointment->appointment_date >= now()->format('Y-m-d'))
                        <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="flex-1" onsubmit="return confirmCancelAppointment(event)">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="cancellation_reason" id="cancellation_reason">
                            <button type="submit" 
                                class="w-full bg-red-100 hover:bg-red-200 text-red-800 px-6 py-3 rounded-xl font-semibold transition-colors">
                                Cancel Appointment
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('appointments.index') }}"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-xl text-center font-semibold transition-colors">
                        Back to Appointments
                    </a>
                    
                    <a href="{{ route('appointments.book') }}"
                        class="flex-1 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-3 rounded-xl text-center font-semibold shadow-md hover:shadow-rose-200/80 transition-all">
                        Book Another
                    </a>
                </div>
            </div>
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

    <!-- Chat Widget -->
    @if(in_array($appointment->status, ['confirmed', 'in_progress']) && $appointment->client_id)
        @if($appointment->staff_id)
            <x-chat-widget :appointment-id="$appointment->id" />
        @else
            <div class="fixed bottom-4 right-4 z-50">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 shadow-lg max-w-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-sm text-yellow-800">Chat will be available once a staff member is assigned to your appointment.</p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</x-layout>
