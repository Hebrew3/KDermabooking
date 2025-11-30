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
                    <a href="{{ route('client.appointments.show', $appointment) }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Reschedule Appointment</h1>
                        <p class="text-gray-600 mt-2">{{ $appointment->appointment_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Current Appointment Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8">
                <h3 class="font-medium text-blue-900 mb-2">Current Appointment</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-blue-700 font-medium">Service:</span>
                        <span class="text-blue-900">{{ $appointment->service->name }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Date & Time:</span>
                        <span class="text-blue-900">{{ $appointment->formatted_date_time }}</span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Staff:</span>
                        <span class="text-blue-900">{{ $appointment->staff ? $appointment->staff->name : 'To be assigned' }}</span>
                    </div>
                </div>
            </div>

            <!-- Reschedule Form -->
            <div class="max-w-2xl">
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <form method="POST" action="{{ route('client.appointments.update', $appointment) }}" id="rescheduleForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Service Selection -->
                        <div class="mb-6">
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
                                    {{ $service->name }} - {{ $service->formatted_price }} ({{ $service->formatted_duration }})
                                </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Staff Selection (Optional) -->
                        <div class="mb-6">
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Staff (Optional)
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
                        <div class="mb-6">
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                New Appointment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="appointment_date" 
                                   id="appointment_date" 
                                   min="{{ date('Y-m-d') }}"
                                   max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                                   required 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('appointment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time Selection -->
                        <div class="mb-6">
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                                New Appointment Time <span class="text-red-500">*</span>
                            </label>
                            <div id="timeSlotLoading" class="hidden">
                                <div class="flex items-center justify-center p-4">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-pink-500"></div>
                                    <span class="ml-2 text-gray-600">Loading available times...</span>
                                </div>
                            </div>
                            <div id="timeSlotContainer" class="grid grid-cols-3 md:grid-cols-4 gap-3">
                                <!-- Time slots will be populated here -->
                            </div>
                            <input type="hidden" name="appointment_time" id="appointment_time" value="{{ old('appointment_time', $appointment->appointment_time->format('H:i')) }}">
                            <div id="timeSlotMeta" class="mt-3 text-xs text-gray-500"></div>
                            <div id="bookingUnavailableMessage" class="mt-2 text-sm text-red-600 hidden"></div>
                            @error('appointment_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="client_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes (Optional)
                            </label>
                            <textarea name="client_notes" 
                                      id="client_notes" 
                                      rows="3" 
                                      placeholder="Any special requests or information..."
                                      class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('client_notes', $appointment->client_notes) }}</textarea>
                            @error('client_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('client.appointments.show', $appointment) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                Cancel
                            </a>
                            <x-confirm-button id="bookAppointmentButton" action="update" title="Update Appointment" text="Are you sure you want to update this appointment?" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg">
                                Update Appointment
                            </x-confirm-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedTimeSlot = '{{ old('appointment_time', $appointment->appointment_time->format('H:i')) }}';

        // Service selection handler
        document.getElementById('service_id').addEventListener('change', loadTimeSlots);
        document.getElementById('appointment_date').addEventListener('change', loadTimeSlots);
        document.getElementById('staff_id').addEventListener('change', loadTimeSlots);

        // Load available time slots
        function loadTimeSlots() {
            const serviceId = document.getElementById('service_id').value;
            const date = document.getElementById('appointment_date').value;
            const staffId = document.getElementById('staff_id').value;

            if (!serviceId || !date) {
                clearTimeSlots();
                return;
            }

            document.getElementById('timeSlotLoading').classList.remove('hidden');
            document.getElementById('timeSlotContainer').innerHTML = '';

            fetch(`{{ route('client.appointments.available-slots') }}?service_id=${serviceId}&date=${date}&staff_id=${staffId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed to load available times (${response.status})`);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('timeSlotLoading').classList.add('hidden');
                    displayTimeSlots(data.available_slots, data);
                })
                .catch(error => {
                    document.getElementById('timeSlotLoading').classList.add('hidden');
                    console.error('Error loading time slots:', error);
                    document.getElementById('timeSlotContainer').innerHTML = '<p class="text-red-600 text-sm">Error loading available times. Please try again.</p>';
                });
        }

        // Display time slots
        function displayTimeSlots(slots, data = null) {
            const container = document.getElementById('timeSlotContainer');
            const meta = document.getElementById('timeSlotMeta');
            const safeSlots = Array.isArray(slots) ? [...slots] : [];
            
            // Add current appointment time to available slots if it's not already there
            const currentTime = '{{ $appointment->appointment_time->format('H:i') }}';
            if (currentTime && !safeSlots.includes(currentTime)) {
                safeSlots.push(currentTime);
                safeSlots.sort();
            }
            
            if (safeSlots.length === 0) {
                container.innerHTML = '<p class="text-gray-600 text-sm col-span-full">No available time slots for this date.</p>';
                if (meta) meta.textContent = '';
                setBookingEnabled(false, 'No time slots are available. Please choose another date.');
                return;
            }

            container.innerHTML = safeSlots.map(slot => {
                const time12 = formatTime12Hour(slot);
                const isCurrentTime = slot === currentTime;
                return `
                    <button type="button" 
                            class="time-slot-btn p-3 border rounded-lg text-sm transition-colors duration-200 ${
                                slot === selectedTimeSlot 
                                    ? 'border-pink-500 bg-pink-100 text-pink-700' 
                                    : 'border-gray-300 hover:border-pink-500 hover:bg-pink-50'
                            } ${isCurrentTime ? 'ring-2 ring-blue-200' : ''}"
                            data-time="${slot}"
                            onclick="selectTimeSlot('${slot}', this)">
                        ${time12}
                        ${isCurrentTime ? '<br><span class="text-xs text-blue-600">Current</span>' : ''}
                    </button>
                `;
            }).join('');

            if (meta) {
                if (data && data.business_hours) {
                    const businessWindow = `${formatTime12Hour(data.business_hours.start)} - ${formatTime12Hour(data.business_hours.end)}`;
                    meta.textContent = `Staff availability window: ${businessWindow}`;
                } else {
                    meta.textContent = '';
                }
            }

            setBookingEnabled(true, '');
        }

        // Select time slot
        function selectTimeSlot(time, button) {
            // Remove previous selection
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.classList.remove('border-pink-500', 'bg-pink-100', 'text-pink-700');
                btn.classList.add('border-gray-300');
            });

            // Add selection to clicked button
            button.classList.remove('border-gray-300');
            button.classList.add('border-pink-500', 'bg-pink-100', 'text-pink-700');

            // Set hidden input value
            document.getElementById('appointment_time').value = time;
            selectedTimeSlot = time;
        }

        // Clear time slots
        function clearTimeSlots(showMessage = false) {
            const container = document.getElementById('timeSlotContainer');
            if (showMessage) {
                container.innerHTML = '<p class="text-gray-600 text-sm col-span-full">No available time slots for this date.</p>';
            } else {
                container.innerHTML = '';
            }
            const meta = document.getElementById('timeSlotMeta');
            if (meta) {
                meta.textContent = '';
            }
            setBookingEnabled(false, showMessage ? 'No time slots are available. Please choose another date.' : '');
        }

        function setBookingEnabled(enabled, message = '') {
            const button = document.getElementById('bookAppointmentButton');
            const warning = document.getElementById('bookingUnavailableMessage');
            if (button) {
                button.disabled = !enabled;
                button.classList.toggle('opacity-50', !enabled);
                button.classList.toggle('cursor-not-allowed', !enabled);
            }
            if (warning) {
                if (!enabled && message) {
                    warning.textContent = message;
                    warning.classList.remove('hidden');
                } else {
                    warning.textContent = '';
                    warning.classList.add('hidden');
                }
            }
        }

        // Format time to 12-hour format
        function formatTime12Hour(time24) {
            const [hours, minutes] = time24.split(':');
            const hour12 = hours % 12 || 12;
            const ampm = hours < 12 ? 'AM' : 'PM';
            return `${hour12}:${minutes} ${ampm}`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTimeSlots();
        });
    </script>
</x-app-layout>
