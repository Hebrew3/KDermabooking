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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Type Selection -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Customer Type
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="is_walkin" value="0" id="registeredCustomer" {{ !$appointment->isWalkIn() ? 'checked' : '' }} class="mr-2" onchange="toggleCustomerType()">
                                        <span>Registered Client</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="is_walkin" value="1" id="walkinCustomer" {{ $appointment->isWalkIn() ? 'checked' : '' }} class="mr-2" onchange="toggleCustomerType()">
                                        <span>Walk-in Customer</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Client Selection (Registered) -->
                            <div class="md:col-span-2" id="registeredClientSection" style="display: {{ $appointment->isWalkIn() ? 'none' : 'block' }}">
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Client <span class="text-red-500">*</span>
                                </label>
                                <select name="client_id" id="client_id" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select a customer...</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (old('client_id', $appointment->client_id) == $client->id) ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->email }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Walk-in Customer Fields -->
                            <div class="md:col-span-2" id="walkinCustomerSection" style="display: {{ $appointment->isWalkIn() ? 'block' : 'none' }}">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <h3 class="text-sm font-medium text-blue-900 mb-3">Walk-in Customer Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="walkin_customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                Full Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="walkin_customer_name" 
                                                   id="walkin_customer_name" 
                                                   value="{{ old('walkin_customer_name', $appointment->walkin_customer_name) }}"
                                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                            @error('walkin_customer_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="walkin_customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                                Phone Number <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="walkin_customer_phone" 
                                                   id="walkin_customer_phone" 
                                                   value="{{ old('walkin_customer_phone', $appointment->walkin_customer_phone) }}"
                                                   placeholder="09XX XXX XXXX"
                                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                            @error('walkin_customer_phone')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="walkin_customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                                Email (Optional)
                                            </label>
                                            <input type="email" 
                                                   name="walkin_customer_email" 
                                                   id="walkin_customer_email" 
                                                   value="{{ old('walkin_customer_email', $appointment->walkin_customer_email) }}"
                                                   placeholder="customer@example.com"
                                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                            @error('walkin_customer_email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                <input type="time" 
                                       name="appointment_time" 
                                       id="appointment_time" 
                                       value="{{ old('appointment_time', $appointment->appointment_time) }}"
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
        // Toggle between registered client and walk-in customer
        function toggleCustomerType() {
            const isWalkin = document.getElementById('walkinCustomer').checked;
            const registeredSection = document.getElementById('registeredClientSection');
            const walkinSection = document.getElementById('walkinCustomerSection');
            const clientSelect = document.getElementById('client_id');
            const appointmentDate = document.getElementById('appointment_date');
            const today = new Date().toISOString().split('T')[0];
            
            if (isWalkin) {
                registeredSection.style.display = 'none';
                walkinSection.style.display = 'block';
                clientSelect.removeAttribute('required');
                clientSelect.value = '';
                // Set date to today and disable for walk-in
                appointmentDate.value = today;
                appointmentDate.setAttribute('readonly', 'readonly');
                appointmentDate.style.backgroundColor = '#f3f4f6';
                appointmentDate.style.cursor = 'not-allowed';
            } else {
                registeredSection.style.display = 'block';
                walkinSection.style.display = 'none';
                clientSelect.setAttribute('required', 'required');
                // Re-enable date field for registered clients
                appointmentDate.removeAttribute('readonly');
                appointmentDate.style.backgroundColor = '';
                appointmentDate.style.cursor = '';
            }
        }

        // Initialize date restriction if appointment is walk-in on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isWalkin = document.getElementById('walkinCustomer').checked;
            if (isWalkin) {
                const appointmentDate = document.getElementById('appointment_date');
                const today = new Date().toISOString().split('T')[0];
                appointmentDate.value = today;
                appointmentDate.setAttribute('readonly', 'readonly');
                appointmentDate.style.backgroundColor = '#f3f4f6';
                appointmentDate.style.cursor = 'not-allowed';
            }
        });
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

