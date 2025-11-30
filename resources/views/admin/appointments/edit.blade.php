<x-app-layout>
<x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.appointments.show', $appointment) }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Appointment</h1>
                        <p class="text-gray-600 mt-2">{{ $appointment->appointment_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="max-w-4xl">
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" id="appointmentForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Customer Information (Read-only) -->
                        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Customer Information (Cannot be changed)</h3>
                            @if($appointment->isWalkIn())
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Customer Type</label>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Walk-in Customer
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Full Name</label>
                                        <p class="text-gray-900 font-medium">{{ $appointment->walkin_customer_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Phone Number</label>
                                        <p class="text-gray-900">{{ $appointment->walkin_customer_phone ?? 'N/A' }}</p>
                                    </div>
                                    @if($appointment->walkin_customer_email)
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Email</label>
                                        <p class="text-gray-900">{{ $appointment->walkin_customer_email }}</p>
                                    </div>
                                    @endif
                                </div>
                                <!-- Hidden fields to preserve walk-in data -->
                                <input type="hidden" name="is_walkin" value="1">
                                <input type="hidden" name="walkin_customer_name" value="{{ $appointment->walkin_customer_name }}">
                                <input type="hidden" name="walkin_customer_phone" value="{{ $appointment->walkin_customer_phone }}">
                                <input type="hidden" name="walkin_customer_email" value="{{ $appointment->walkin_customer_email }}">
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Customer Type</label>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Registered Client
                                        </span>
                                    </div>
                                    @if($appointment->client)
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Client Name</label>
                                        <p class="text-gray-900 font-medium">{{ $appointment->client->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Email</label>
                                        <p class="text-gray-900">{{ $appointment->client->email ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Phone Number</label>
                                        <p class="text-gray-900">{{ $appointment->client->mobile_number ?? 'N/A' }}</p>
                                    </div>
                                    @else
                                    <div class="md:col-span-2">
                                        <p class="text-gray-500 italic">Client information not available</p>
                                    </div>
                                    @endif
                                </div>
                                <!-- Hidden fields to preserve registered client data -->
                                <input type="hidden" name="is_walkin" value="0">
                                <input type="hidden" name="client_id" value="{{ $appointment->client_id }}">
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Service Selection -->
                            <div>
                                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Service <span class="text-red-500">*</span>
                                </label>
                                <select name="service_id" id="service_id" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Choose a service...</option>
                                    @foreach($services as $service)
                                    <option value="{{ $service->id }}" 
                                            data-price="{{ $service->formatted_price }}" 
                                            data-duration="{{ $service->formatted_duration }}"
                                            {{ (old('service_id', $appointment->service_id) == $service->id) ? 'selected' : '' }}>
                                        {{ $service->name }} - {{ $service->formatted_price }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Staff Selection -->
                            <div>
                                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assign Staff (Optional)
                                </label>
                                <select name="staff_id" id="staff_id" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Any available staff</option>
                                    @foreach($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}" {{ (old('staff_id', $appointment->staff_id) == $staffMember->id) ? 'selected' : '' }}>
                                        {{ $staffMember->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('staff_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date Selection -->
                            <div>
                                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Appointment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="appointment_date" 
                                       id="appointment_date" 
                                       value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                                       required 
                                       class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                @error('appointment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Time Selection -->
                            <div>
                                <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Appointment Time <span class="text-red-500">*</span>
                                </label>
                                @php
                                    // Format time to H:i format (remove seconds if present)
                                    $timeValue = old('appointment_time', $appointment->appointment_time);
                                    if ($timeValue) {
                                        // If time has seconds (H:i:s), remove them
                                        if (strlen($timeValue) > 5) {
                                            $timeValue = substr($timeValue, 0, 5);
                                        }
                                        // Ensure it's in H:i format
                                        $timeParts = explode(':', $timeValue);
                                        if (count($timeParts) >= 2) {
                                            $timeValue = sprintf('%02d:%02d', (int)$timeParts[0], (int)$timeParts[1]);
                                        }
                                    }
                                @endphp
                                <input type="time" 
                                       name="appointment_time" 
                                       id="appointment_time" 
                                       value="{{ $timeValue }}"
                                       required 
                                       class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                @error('appointment_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                    <option value="pending" {{ (old('status', $appointment->status) === 'pending') ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ (old('status', $appointment->status) === 'confirmed') ? 'selected' : '' }}>Confirmed</option>
                                    <option value="in_progress" {{ (old('status', $appointment->status) === 'in_progress') ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ (old('status', $appointment->status) === 'completed') ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ (old('status', $appointment->status) === 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                                    <option value="no_show" {{ (old('status', $appointment->status) === 'no_show') ? 'selected' : '' }}>No Show</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Status -->
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Payment Status <span class="text-red-500">*</span>
                                </label>
                                <select name="payment_status" id="payment_status" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                    <option value="unpaid" {{ (old('payment_status', $appointment->payment_status) === 'unpaid') ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partial" {{ (old('payment_status', $appointment->payment_status) === 'partial') ? 'selected' : '' }}>Partial</option>
                                    <option value="paid" {{ (old('payment_status', $appointment->payment_status) === 'paid') ? 'selected' : '' }}>Paid</option>
                                    <option value="refunded" {{ (old('payment_status', $appointment->payment_status) === 'refunded') ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mt-8 space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Notes</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Admin Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Notes
                                    </label>
                                    <textarea name="notes" 
                                              id="notes" 
                                              rows="4" 
                                              placeholder="Internal notes and instructions..."
                                              class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('notes', $appointment->notes) }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Staff Notes -->
                                <div>
                                    <label for="staff_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Staff Notes
                                    </label>
                                    <textarea name="staff_notes" 
                                              id="staff_notes" 
                                              rows="4" 
                                              placeholder="Treatment notes and observations..."
                                              class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('staff_notes', $appointment->staff_notes) }}</textarea>
                                    @error('staff_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Client Notes (Read-only) -->
                            @if($appointment->client_notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client Notes (Read-only)</label>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-gray-900">{{ $appointment->client_notes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Cancellation Reason (if cancelled) -->
                            <div id="cancellationReasonDiv" class="{{ old('status', $appointment->status) === 'cancelled' ? '' : 'hidden' }}">
                                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cancellation Reason <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="cancellation_reason" 
                                       id="cancellation_reason" 
                                       value="{{ old('cancellation_reason', $appointment->cancellation_reason) }}"
                                       placeholder="Reason for cancellation..."
                                       class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                @error('cancellation_reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div id="serviceDetails" class="mt-6 p-4 bg-pink-50 rounded-lg">
                            <h3 class="font-medium text-gray-900 mb-2">Service Details</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Price:</span>
                                    <span id="servicePrice" class="text-pink-600 font-semibold">{{ $appointment->service->formatted_price }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Duration:</span>
                                    <span id="serviceDuration" class="text-gray-900">{{ $appointment->service->formatted_duration }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="{{ route('admin.appointments.show', $appointment) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                Cancel
                            </a>
                            <x-confirm-button action="update" title="Update Appointment" text="Are you sure you want to update this appointment?" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg">
                                Update Appointment
                            </x-confirm-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Service selection handler
        document.getElementById('service_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const serviceDetails = document.getElementById('serviceDetails');
            
            if (this.value) {
                document.getElementById('servicePrice').textContent = selectedOption.dataset.price;
                document.getElementById('serviceDuration').textContent = selectedOption.dataset.duration;
            }
        });

        // Status change handler
        document.getElementById('status').addEventListener('change', function() {
            const cancellationDiv = document.getElementById('cancellationReasonDiv');
            if (this.value === 'cancelled') {
                cancellationDiv.classList.remove('hidden');
                document.getElementById('cancellation_reason').required = true;
            } else {
                cancellationDiv.classList.add('hidden');
                document.getElementById('cancellation_reason').required = false;
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger service change to show current details
            const serviceSelect = document.getElementById('service_id');
            if (serviceSelect.value) {
                serviceSelect.dispatchEvent(new Event('change'));
            }

            // Trigger status change to show/hide cancellation reason
            const statusSelect = document.getElementById('status');
            statusSelect.dispatchEvent(new Event('change'));
        });
    </script>
</x-app-layout>

