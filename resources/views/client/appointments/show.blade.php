<x-app-layout>
<x-mobile-header />

    <!-- Include Client Sidebar -->
    <x-client-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('client.appointments.index') }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
                        <p class="text-gray-600 mt-2">{{ $appointment->appointment_number }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <div class="flex justify-between items-start mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Appointment Information</h2>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $appointment->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                <p class="text-gray-900 font-medium">{{ $appointment->service->name }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment->service->formatted_duration }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                                <p class="text-gray-900 font-medium">{{ $appointment->formatted_date_time }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Staff Member</label>
                                <p class="text-gray-900 font-medium">
                                    {{ $appointment->staff ? $appointment->staff->name : 'To be assigned' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                                <p class="text-gray-900 font-medium text-lg">{{ $appointment->formatted_total }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->payment_status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->payment_status)) }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Booked On</label>
                                <p class="text-gray-900">{{ $appointment->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        @if($appointment->client_notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Notes</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $appointment->client_notes }}</p>
                        </div>
                        @endif

                        @if($appointment->staff_notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Staff Notes</label>
                            <p class="text-gray-900 bg-blue-50 p-3 rounded-lg">{{ $appointment->staff_notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Service Details -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Details</h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $appointment->service->name }}</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ $appointment->service->short_description }}</p>
                            </div>
                            @if($appointment->service->description)
                            <div>
                                <p class="text-gray-700 text-sm">{{ $appointment->service->description }}</p>
                            </div>
                            @endif
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span>Duration: {{ $appointment->service->formatted_duration }}</span>
                                <span>•</span>
                                <span>Price: {{ $appointment->service->formatted_price }}</span>
                            </div>
                        </div>
                    </div>

                    @if($appointment->status === 'completed')
                    <div id="feedback" class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Feedback</h3>

                        @if(session('success'))
                            <p class="mb-4 text-sm text-green-600">{{ session('success') }}</p>
                        @endif
                        @if(session('error'))
                            <p class="mb-4 text-sm text-red-600">{{ session('error') }}</p>
                        @endif

                        @if($appointment->hasClientFeedback())
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-700">Rating:</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= $appointment->client_rating; $i++)
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                @if($appointment->client_feedback)
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Comments</p>
                                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $appointment->client_feedback }}</p>
                                </div>
                                @endif
                            </div>
                        @else
                            <form method="POST" action="{{ route('client.appointments.feedback', $appointment) }}" class="space-y-4">
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
                                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                        Submit Feedback
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Actions Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        
                        <div class="space-y-3">
                            @if($appointment->canBeRescheduled())
                            <a href="{{ route('client.appointments.edit', $appointment) }}" 
                               class="w-full bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Reschedule
                            </a>
                            @endif

                            @if($appointment->canBeCancelled())
                            <button onclick="openCancelModal()" 
                                    class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Appointment
                            </button>
                            @endif

                            <a href="{{ route('client.appointments.index') }}" 
                               class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Appointments
                            </a>
                        </div>

                        @if(in_array($appointment->status, ['confirmed', 'in_progress']))
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-2">Preparation Tips</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Arrive 10 minutes early</li>
                                <li>• Bring a valid ID</li>
                                <li>• Avoid sun exposure before treatment</li>
                                <li>• Remove makeup if applicable</li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Appointment Modal -->
    @if($appointment->canBeCancelled())
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Cancel Appointment</h3>
                    <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('client.appointments.cancel', $appointment) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for cancellation</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="3" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Please provide a reason for cancelling this appointment..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCancelModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Keep Appointment
                        </button>
                        <x-confirm-button action="delete" title="Cancel Appointment" text="Are you sure you want to cancel this appointment? This action cannot be undone!" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            Cancel Appointment
                        </x-confirm-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('cancellation_reason').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
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
</x-app-layout>
