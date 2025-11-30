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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Book New Appointment</h1>
                        <p class="text-gray-600 mt-2">Schedule your appointment with K-Derma</p>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="max-w-2xl">
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <form method="POST" action="{{ route('client.appointments.store') }}" id="appointmentForm">
                        @csrf

                        <!-- Date Selection (Required but shown first) -->
                        <div class="mb-6">
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Appointment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="appointment_date" id="appointment_date"
                                min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}" required
                                class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('appointment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Step 1: Service Selection -->
                        <div class="mb-6">
                            <div class="flex items-center mb-2">
                                <span class="flex items-center justify-center w-6 h-6 bg-pink-500 text-white rounded-full text-xs font-semibold mr-2">1</span>
                                <label for="service_id" class="block text-sm font-medium text-gray-700">
                                Select Service <span class="text-red-500">*</span>
                            </label>
                            </div>
                            <select name="service_id" id="service_id" required
                                class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Choose a service...</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->formatted_price }}"
                                        data-duration="{{ $service->formatted_duration }}"
                                        {{ old('service_id', $selectedService?->id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} - {{ $service->formatted_price }}
                                        ({{ $service->formatted_duration }})
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Service Details -->
                            <div id="serviceDetails" class="mt-4 p-4 bg-pink-50 rounded-lg hidden">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Price:</span>
                                        <span id="servicePrice" class="text-pink-600 font-semibold"></span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Duration:</span>
                                        <span id="serviceDuration" class="text-gray-900"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Time Selection -->
                        <div class="mb-6">
                            <div class="flex items-center mb-2">
                                <span class="flex items-center justify-center w-6 h-6 bg-pink-500 text-white rounded-full text-xs font-semibold mr-2">2</span>
                                <label for="appointment_time" class="block text-sm font-medium text-gray-700">
                                    Appointment Time <span class="text-red-500">*</span>
                                </label>
                            </div>
                            <div id="timeSlotLoading" class="hidden">
                                <div class="flex items-center justify-center p-4">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-pink-500"></div>
                                    <span class="ml-2 text-gray-600">Loading available times...</span>
                                </div>
                            </div>
                            <div id="timeSlotContainer" class="grid grid-cols-3 md:grid-cols-4 gap-3">
                                <p class="text-gray-500 text-sm col-span-full">Please select a service first</p>
                            </div>
                            <input type="hidden" name="appointment_time" id="appointment_time"
                                value="{{ old('appointment_time') }}">
                            @error('appointment_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Step 3: Staff Selection (Optional) -->
                        <div class="mb-6">
                            <div class="flex items-center mb-2">
                                <span class="flex items-center justify-center w-6 h-6 bg-pink-500 text-white rounded-full text-xs font-semibold mr-2">3</span>
                                <label for="staff_id" class="block text-sm font-medium text-gray-700">
                                    Assign Staff <span class="text-gray-500">(Optional)</span>
                            </label>
                            </div>
                            <div id="staffLoading" class="hidden">
                                <div class="flex items-center justify-center p-4">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-pink-500"></div>
                                    <span class="ml-2 text-gray-600">Loading available staff...</span>
                                </div>
                            </div>
                            <select name="staff_id" id="staff_id"
                                class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                                disabled>
                                <option value="">Please select a service and appointment time first</option>
                            </select>
                            <div id="staffInfo" class="mt-2 text-sm text-gray-600 hidden">
                                <!-- Staff information will be displayed here -->
                            </div>
                            @error('staff_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="client_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes (Optional)
                            </label>
                            <textarea name="client_notes" id="client_notes" rows="3" placeholder="Any special requests or information..."
                                class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('client_notes') }}</textarea>
                            @error('client_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Booking Summary -->
                        <div id="bookingSummary" class="mb-6 p-4 bg-gray-50 rounded-lg hidden">
                            <h3 class="font-medium text-gray-900 mb-3">Booking Summary</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service:</span>
                                    <span id="summaryService" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span id="summaryDate" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span id="summaryTime" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Staff:</span>
                                    <span id="summaryStaff" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between border-t pt-2 mt-2">
                                    <span class="text-gray-900 font-medium">Total Amount:</span>
                                    <span id="summaryTotal" class="font-bold text-pink-600"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('client.appointments.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                Cancel
                            </a>
                            <x-confirm-button action="create" title="Book Appointment"
                                text="Are you sure you want to book this appointment?"
                                class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg">
                                Book Appointment
                            </x-confirm-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedTimeSlot = null;

        // Date change handler - Load time slots if service is selected
        document.getElementById('appointment_date').addEventListener('change', function() {
            const serviceId = document.getElementById('service_id').value;
            
            if (serviceId && this.value) {
                // Clear previous time and staff selections
                clearTimeSlots();
                clearStaffSelection();
                
                // Load time slots
                loadTimeSlots();
            } else {
                clearTimeSlots();
                clearStaffSelection();
            }
        });

        // Service selection handler - Step 1
        document.getElementById('service_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const serviceDetails = document.getElementById('serviceDetails');
            const dateInput = document.getElementById('appointment_date');

            if (this.value) {
                document.getElementById('servicePrice').textContent = selectedOption.dataset.price;
                document.getElementById('serviceDuration').textContent = selectedOption.dataset.duration;
                serviceDetails.classList.remove('hidden');
                
                // Clear time slots and staff
                clearTimeSlots();
                clearStaffSelection();

                // If date is already selected, load time slots
                if (dateInput.value) {
                    loadTimeSlots();
                }
                
                updateBookingSummary();
            } else {
                serviceDetails.classList.add('hidden');
                document.getElementById('bookingSummary').classList.add('hidden');
                
                clearTimeSlots();
                clearStaffSelection();
            }
        });

        // Staff change handler
        document.getElementById('staff_id').addEventListener('change', function() {
            updateBookingSummary();
        });

        // Load available staff for selected service, date, and time - Step 4
        function loadAvailableStaff() {
            const serviceId = document.getElementById('service_id').value;
            const date = document.getElementById('appointment_date').value;
            const time = selectedTimeSlot;
            const staffSelect = document.getElementById('staff_id');
            const staffLoading = document.getElementById('staffLoading');
            const staffInfo = document.getElementById('staffInfo');

            if (!serviceId || !date || !time) {
                staffSelect.innerHTML = '<option value="">Please select a service, date, and time first</option>';
                staffSelect.disabled = true;
                staffInfo.classList.add('hidden');
                return;
            }

            staffLoading.classList.remove('hidden');
            staffSelect.disabled = true;

            // Build URL with time parameter
            let url = `{{ route('client.appointments.available-staff') }}?service_id=${serviceId}&date=${date}`;
            if (time) {
                url += `&time=${time}`;
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (response.status === 401) {
                        throw new Error('Authentication required. Please log in again.');
                    } else if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    } else if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    staffLoading.classList.add('hidden');

                    // Clear existing options
                    staffSelect.innerHTML = '<option value="">Any available staff</option>';


                    if (data.error) {
                        staffInfo.innerHTML = `
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="font-medium text-red-900">Error Loading Staff</p>
                                <p class="text-red-700 text-sm">${data.message || 'Unknown error occurred'}</p>
                            </div>
                        `;
                        staffInfo.classList.remove('hidden');
                        staffSelect.innerHTML = '<option value="">Error loading staff</option>';
                    } else if (data.available_staff && data.available_staff.length > 0) {
                        data.available_staff.forEach(staff => {
                            const option = document.createElement('option');
                            option.value = staff.id;
                            option.textContent = staff.name;
                            option.dataset.specialization = staff.specialization;
                            option.dataset.schedule = staff.schedule ? staff.schedule.formatted_hours :
                                'Not available';
                            staffSelect.appendChild(option);
                        });

                        // Show staff info
                        const timeDisplay = selectedTimeSlot ? formatTime12Hour(selectedTimeSlot) : 'selected time';
                        staffInfo.innerHTML = `
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="font-medium text-blue-900">Available Staff for ${data.day_of_week} at ${timeDisplay}</p>
                                <p class="text-blue-700 text-sm">${data.available_staff.length} staff member(s) available for ${data.service_name} at this time</p>
                            </div>
                        `;
                        staffInfo.classList.remove('hidden');
                    } else {
                        const timeDisplay = selectedTimeSlot ? formatTime12Hour(selectedTimeSlot) : 'selected time';
                        staffInfo.innerHTML = `
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="font-medium text-yellow-900">No Staff Available</p>
                                <p class="text-yellow-700 text-sm">No staff members are available for ${data.service_name} on ${data.day_of_week} at ${timeDisplay}. You can still book and we'll assign available staff.</p>
                            </div>
                        `;
                        staffInfo.classList.remove('hidden');
                    }

                    staffSelect.disabled = false;
                })
                .catch(error => {
                    staffLoading.classList.add('hidden');
                    console.error('Error loading staff:', error);
                    console.error('Error details:', {
                        message: error.message,
                        stack: error.stack,
                        name: error.name
                    });

                    // Show fallback staff options if API fails
                    const fallbackStaff = @json($staff);
                    if (fallbackStaff && fallbackStaff.length > 0) {
                        staffSelect.innerHTML = '<option value="">Any available staff</option>';
                        fallbackStaff.forEach(staff => {
                            const option = document.createElement('option');
                            option.value = staff.id;
                            option.textContent = staff.name;
                            staffSelect.appendChild(option);
                        });

                        staffInfo.innerHTML = `
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="font-medium text-yellow-900">Using Fallback Staff List</p>
                                <p class="text-yellow-700 text-sm">Unable to load real-time availability. Showing all staff members. Please contact us to confirm availability.</p>
                                <p class="text-xs text-gray-500 mt-1">Error: ${error.message}</p>
                            </div>
                        `;
                    } else {
                        staffSelect.innerHTML = '<option value="">Error loading staff</option>';
                        staffInfo.innerHTML = `
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="font-medium text-red-900">Error Loading Staff</p>
                                <p class="text-red-700 text-sm">${error.message || 'Failed to load available staff. Please refresh the page and try again.'}</p>
                            </div>
                        `;
                    }

                    staffSelect.disabled = false;
                    staffInfo.classList.remove('hidden');
                });
        }

        // Load available time slots - Step 2 (after service selection)
        function loadTimeSlots() {
            const serviceId = document.getElementById('service_id').value;
            const date = document.getElementById('appointment_date').value;

            if (!serviceId || !date) {
                if (!serviceId) {
                    document.getElementById('timeSlotContainer').innerHTML = 
                        '<p class="text-gray-500 text-sm col-span-full">Please select a service first</p>';
                } else {
                    document.getElementById('timeSlotContainer').innerHTML = 
                        '<p class="text-gray-500 text-sm col-span-full">Please select a date first</p>';
                }
                clearStaffSelection();
                return;
            }

            document.getElementById('timeSlotLoading').classList.remove('hidden');
            document.getElementById('timeSlotContainer').innerHTML = '';

            // Clear previous time selection
            selectedTimeSlot = null;
            document.getElementById('appointment_time').value = '';
            clearStaffSelection();

            // Don't pass staff_id when loading time slots - we want all available times
            fetch(`{{ route('client.appointments.available-slots') }}?service_id=${serviceId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('timeSlotLoading').classList.add('hidden');
                    displayTimeSlots(data.available_slots, data);
                })
                .catch(error => {
                    document.getElementById('timeSlotLoading').classList.add('hidden');
                    console.error('Error loading time slots:', error);
                    document.getElementById('timeSlotContainer').innerHTML =
                        '<p class="text-red-600 text-sm col-span-full">Error loading available times. Please try again.</p>';
                });
        }

        // Display time slots
        function displayTimeSlots(slots, data = null) {
            const container = document.getElementById('timeSlotContainer');

            if (slots.length === 0) {
                let message = '<p class="text-gray-600 text-sm col-span-full">No available time slots for this date.</p>';

                if (data && data.blocked_slots > 0) {
                    message += `<p class="text-xs text-gray-500 mt-2">${data.blocked_slots} time slots are already booked.</p>`;
                }

                container.innerHTML = message;
                return;
            }

            container.innerHTML = slots.map(slot => {
                const time12 = formatTime12Hour(slot);
                return `
                    <button type="button"
                            class="time-slot-btn p-3 border border-gray-300 rounded-lg text-sm hover:border-pink-500 hover:bg-pink-50 transition-colors duration-200"
                            data-time="${slot}"
                            onclick="selectTimeSlot('${slot}', this)">
                        ${time12}
                    </button>
                `;
            }).join('');

            // Show additional info if available
            if (data && data.blocked_slots > 0) {
                const infoDiv = document.createElement('div');
                infoDiv.className = 'col-span-full text-xs text-gray-500 mt-2 p-2 bg-yellow-50 rounded';
                infoDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>${data.blocked_slots} time slots are already booked and not shown.</span>
                    </div>
                `;
                container.appendChild(infoDiv);
            }
        }

        // Select time slot - Step 2 (triggers staff loading)
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

            // Clear previous staff selection and load available staff for this service and time
            clearStaffSelection();
            loadAvailableStaff();

            updateBookingSummary();
        }
        
        // Clear staff selection
        function clearStaffSelection() {
            const staffSelect = document.getElementById('staff_id');
            const staffInfo = document.getElementById('staffInfo');
            
            staffSelect.innerHTML = '<option value="">Please select a service and appointment time first</option>';
            staffSelect.disabled = true;
            staffInfo.classList.add('hidden');
        }

        // Clear time slots
        function clearTimeSlots() {
            document.getElementById('timeSlotContainer').innerHTML = '<p class="text-gray-500 text-sm col-span-full">Please select a service first</p>';
            document.getElementById('appointment_time').value = '';
            selectedTimeSlot = null;
        }

        // Format time to 12-hour format
        function formatTime12Hour(time24) {
            const [hours, minutes] = time24.split(':');
            const hour12 = hours % 12 || 12;
            const ampm = hours < 12 ? 'AM' : 'PM';
            return `${hour12}:${minutes} ${ampm}`;
        }

        // Update booking summary
        function updateBookingSummary() {
            const serviceSelect = document.getElementById('service_id');
            const staffSelect = document.getElementById('staff_id');
            const date = document.getElementById('appointment_date').value;

            if (!serviceSelect.value || !date || !selectedTimeSlot) {
                document.getElementById('bookingSummary').classList.add('hidden');
                return;
            }

            const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
            const selectedStaff = staffSelect.options[staffSelect.selectedIndex];

            document.getElementById('summaryService').textContent = selectedService.text.split(' - ')[0];
            document.getElementById('summaryDate').textContent = new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('summaryTime').textContent = formatTime12Hour(selectedTimeSlot);
            document.getElementById('summaryStaff').textContent = selectedStaff.text;
            document.getElementById('summaryTotal').textContent = selectedService.dataset.price;

            document.getElementById('bookingSummary').classList.remove('hidden');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const serviceId = document.getElementById('service_id').value;
            const dateInput = document.getElementById('appointment_date');
            
            // If service is already selected, show service details
            if (serviceId) {
                const serviceSelect = document.getElementById('service_id');
                const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    document.getElementById('servicePrice').textContent = selectedOption.dataset.price;
                    document.getElementById('serviceDuration').textContent = selectedOption.dataset.duration;
                    document.getElementById('serviceDetails').classList.remove('hidden');
                }
                
                // If date is also selected, load time slots
                if (dateInput.value) {
                    loadTimeSlots();
                }
            }
            
            // If there are old values (validation errors), restore the state
            const oldTime = document.getElementById('appointment_time').value;
            if (oldTime && serviceId && dateInput.value) {
                selectedTimeSlot = oldTime;
                loadTimeSlots();
                
                // After time slots load, select the old time and load staff
                setTimeout(() => {
                    const timeButton = document.querySelector(`[data-time="${oldTime}"]`);
                    if (timeButton) {
                        selectTimeSlot(oldTime, timeButton);
                    }
                }, 500);
            }
        });
    </script>
</x-app-layout>
