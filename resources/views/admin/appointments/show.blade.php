<x-app-layout>
<x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.appointments.index') }}" class="text-gray-600 hover:text-gray-800">
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
            </div>

            @if($appointment->isLate())
            <!-- Late Appointment Alert -->
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">Client is {{ $appointment->getMinutesLate() }} minutes late</h3>
                        <p class="text-sm text-red-700 mt-1">The client has not arrived for their scheduled appointment. You may consider cancelling or marking as no-show if they don't arrive soon.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Appointment Information -->
                    <div class="bg-white rounded-xl shadow-sm p-6 {{ $appointment->isLate() ? 'border-2 border-red-300' : '' }}">
                        <div class="flex justify-between items-start mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Appointment Information</h2>
                            <div class="flex space-x-2">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $appointment->status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $appointment->payment_status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->payment_status)) }}
                                </span>
                                @if($appointment->isLate())
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 animate-pulse" title="Client is {{ $appointment->getMinutesLate() }} minutes late">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $appointment->getMinutesLate() }}m Late
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                                <div>
                                    @if($appointment->isWalkIn())
                                        <div class="mb-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Walk-in Customer
                                            </span>
                                        </div>
                                        <p class="text-gray-900 font-medium">{{ $appointment->customer_name }}</p>
                                        @if($appointment->customer_email)
                                            <p class="text-sm text-gray-600">{{ $appointment->customer_email }}</p>
                                        @endif
                                        @if($appointment->customer_phone)
                                            <p class="text-sm text-gray-600">{{ $appointment->customer_phone }}</p>
                                        @endif
                                    @else
                                        <p class="text-gray-900 font-medium">{{ $appointment->client->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $appointment->client->email }}</p>
                                        <p class="text-sm text-gray-600">{{ $appointment->client->mobile_number }}</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                <p class="text-gray-900 font-medium">{{ $appointment->service->name }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment->service->formatted_duration }} • {{ $appointment->service->formatted_price }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                                <div class="flex items-center space-x-2">
                                    <p class="text-gray-900 font-medium">{{ $appointment->formatted_date_time }}</p>
                                    @if($appointment->isLate())
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800" title="Client is {{ $appointment->getMinutesLate() }} minutes late">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            {{ $appointment->getMinutesLate() }}m late
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Staff</label>
                                <p class="text-gray-900 font-medium">
                                    {{ $appointment->staff ? $appointment->staff->name : 'Unassigned' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                                <p class="text-gray-900 font-medium text-lg">{{ $appointment->formatted_total }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Date</label>
                                <p class="text-gray-900">{{ $appointment->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                @if($appointment->confirmed_at)
                                <div>
                                    <span class="text-gray-600 font-medium">Confirmed:</span>
                                    <span class="text-gray-900">{{ $appointment->confirmed_at->format('M d, Y g:i A') }}</span>
                                </div>
                                @endif
                                @if($appointment->completed_at)
                                <div>
                                    <span class="text-gray-600 font-medium">Completed:</span>
                                    <span class="text-gray-900">{{ $appointment->completed_at->format('M d, Y g:i A') }}</span>
                                </div>
                                @endif
                                @if($appointment->cancelled_at)
                                <div>
                                    <span class="text-gray-600 font-medium">Cancelled:</span>
                                    <span class="text-gray-900">{{ $appointment->cancelled_at->format('M d, Y g:i A') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Feedback Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                            
                            @if($appointment->client_notes)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client Notes</label>
                                <p class="text-gray-900 bg-blue-50 p-3 rounded-lg">{{ $appointment->client_notes }}</p>
                            </div>
                            @endif

                            @if($appointment->notes)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $appointment->notes }}</p>
                            </div>
                            @endif

                            @if($appointment->staff_notes)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Staff Notes</label>
                                <p class="text-gray-900 bg-green-50 p-3 rounded-lg">{{ $appointment->staff_notes }}</p>
                            </div>
                            @endif

                            @if($appointment->cancellation_reason)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                                <p class="text-gray-900 bg-red-50 p-3 rounded-lg">{{ $appointment->cancellation_reason }}</p>
                            </div>
                            @endif

                            @if(!$appointment->client_notes && !$appointment->notes && !$appointment->staff_notes && !$appointment->cancellation_reason)
                            <p class="text-gray-500 italic">No notes available for this appointment.</p>
                            @endif
                        </div>

                        @if($appointment->hasClientFeedback())
                        <div class="pt-6 border-t border-gray-200 space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Client Feedback</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-700">Rating:</span>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= $appointment->client_rating; $i++)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-xs text-gray-600">({{ $appointment->client_rating }})</span>
                                    </div>
                                </div>
                                @if($appointment->client_feedback)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-1">Client Comments</p>
                                        <p class="text-gray-900 bg-white rounded-lg p-3 border border-gray-100">{{ $appointment->client_feedback }}</p>
                                    </div>
                                @endif

                                @if($appointment->hasAdminFeedbackReply())
                                    <div class="pt-3 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-700 mb-1">Admin Reply</p>
                                        <p class="text-gray-900 bg-white rounded-lg p-3 border border-gray-100">{{ $appointment->admin_feedback_reply }}</p>
                                        @if($appointment->admin_feedback_replied_at)
                                            <p class="text-xs text-gray-500 mt-1">Replied on {{ $appointment->admin_feedback_replied_at->format('M d, Y g:i A') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Admin reply form -->
                            <form method="POST" action="{{ route('admin.appointments.feedback-reply', $appointment) }}" class="space-y-2">
                                @csrf
                                <label for="admin_feedback_reply" class="block text-sm font-medium text-gray-700">Your Reply to Client</label>
                                <textarea id="admin_feedback_reply" name="admin_feedback_reply" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Write a short reply to the client...">{{ old('admin_feedback_reply', $appointment->admin_feedback_reply) }}</textarea>
                                @error('admin_feedback_reply')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Save Reply</button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            <!-- Status Update -->
                            <div>
                                <label for="statusUpdate" class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                                <select id="statusUpdate" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    <option value="">Select new status...</option>
                                    <option value="pending" {{ $appointment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $appointment->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="in_progress" {{ $appointment->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="no_show" {{ $appointment->status === 'no_show' ? 'selected' : '' }}>No Show</option>
                                </select>
                                <button onclick="updateStatus()" class="w-full mt-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                    Update Status
                                </button>
                            </div>

                            <hr class="my-4">

                            <!-- Action Buttons -->
                            <a href="{{ route('admin.appointments.edit', $appointment) }}" 
                               class="w-full bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Appointment
                            </a>

                            @if($appointment->canBeCancelled())
                            <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.')"
                                        class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Appointment
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('admin.appointments.index') }}" 
                               class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Appointments
                            </a>
                        </div>

                        <!-- Client Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-3">Client Information</h4>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Name:</span>
                                    <span class="text-gray-900 font-medium">{{ $appointment->client->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Email:</span>
                                    <a href="mailto:{{ $appointment->client->email }}" class="text-pink-600 hover:text-pink-800">{{ $appointment->client->email }}</a>
                                </div>
                                <div>
                                    <span class="text-gray-600">Phone:</span>
                                    <a href="tel:{{ $appointment->client->mobile_number }}" class="text-pink-600 hover:text-pink-800">{{ $appointment->client->mobile_number }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function updateStatus() {
            const statusSelect = document.getElementById('statusUpdate');
            const newStatus = statusSelect.value;
            
            if (!newStatus) {
                alert('Please select a status to update.');
                return;
            }

            if (newStatus === '{{ $appointment->status }}') {
                alert('This appointment is already in the selected status.');
                return;
            }

            let reason = null;
            if (newStatus === 'cancelled') {
                reason = prompt('Please provide a reason for cancellation:');
                if (!reason) {
                    return;
                }
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('status', newStatus);
            if (reason) {
                formData.append('cancellation_reason', reason);
            }

            // Send AJAX request
            fetch('{{ route('admin.appointments.update-status', $appointment) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status. Please try again.');
            });
        }
    </script>

    <!-- Chat Widget -->
    @if(in_array($appointment->status, ['confirmed', 'in_progress']) && $appointment->client_id)
        <x-chat-widget :appointment-id="$appointment->id" />
    @endif
</x-app-layout>
