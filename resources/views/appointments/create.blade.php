<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 py-20">
        <!-- Background decorations -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-200/30 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">Book Your Appointment</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Schedule your personalized skincare consultation with our expert dermatologists.
                </p>
            </div>

            <!-- Appointment Form -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12 border border-pink-100">
                <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Service Selection -->
                        <div class="lg:col-span-2">
                            <label for="service_id" class="block text-sm font-semibold text-gray-900 mb-3">
                                Select Service *
                            </label>
                            <select name="service_id" id="service_id" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-gray-900">
                                <option value="">Choose a service...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-duration="{{ $service->duration }}">
                                        {{ $service->name }} - {{ $service->formatted_price }}
                                        @if($service->duration)
                                            ({{ $service->formatted_duration }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Appointment Date -->
                        <div>
                            <label for="appointment_date" class="block text-sm font-semibold text-gray-900 mb-3">
                                Preferred Date *
                            </label>
                            <input type="date" name="appointment_date" id="appointment_date" required
                                min="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            @error('appointment_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Appointment Time -->
                        <div>
                            <label for="appointment_time" class="block text-sm font-semibold text-gray-900 mb-3">
                                Preferred Time *
                            </label>
                            <select name="appointment_time" id="appointment_time" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select time...</option>
                            </select>
                            @error('appointment_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Staff Selection -->
                        <div>
                            <label for="staff_id" class="block text-sm font-semibold text-gray-900 mb-3">
                                Preferred Staff (Optional)
                            </label>
                            <select name="staff_id" id="staff_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Any available staff</option>
                            </select>
                            @error('staff_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="lg:col-span-2">
                            <label for="notes" class="block text-sm font-semibold text-gray-900 mb-3">
                                Additional Notes (Optional)
                            </label>
                            <textarea name="notes" id="notes" rows="4" placeholder="Any specific concerns or requests..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 resize-none"></textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('index.landing') }}"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-8 py-4 rounded-xl text-center font-semibold transition-colors">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                            class="flex-1 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-8 py-4 rounded-xl font-semibold shadow-md hover:shadow-rose-200/80 transition-all">
                            <span id="btnText">Book Appointment</span>
                            <svg id="spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- JavaScript for dynamic form behavior -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service_id');
            const dateInput = document.getElementById('appointment_date');
            const timeSelect = document.getElementById('appointment_time');
            const staffSelect = document.getElementById('staff_id');
            const form = document.getElementById('appointmentForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');

            // Load available time slots when date or service changes
            function loadTimeSlots() {
                const serviceId = serviceSelect.value;
                const date = dateInput.value;

                if (!serviceId || !date) {
                    timeSelect.innerHTML = '<option value="">Select time...</option>';
                    return;
                }

                fetch(`{{ route('appointments.available-slots') }}?service_id=${serviceId}&date=${date}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        timeSelect.innerHTML = '<option value="">Select time...</option>';
                        
                        // Handle the response format: available_slots is an array of time strings
                        const timeSlots = data.available_slots || [];
                        
                        if (timeSlots.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No available time slots for this date';
                            option.disabled = true;
                            timeSelect.appendChild(option);
                            
                            Swal.fire({
                                icon: 'info',
                                title: 'No Available Times',
                                text: 'No staff are available on this date. Please choose a different date.',
                                toast: true,
                                position: 'top-end',
                                timer: 4000,
                                showConfirmButton: false
                            });
                        } else {
                            timeSlots.forEach(time => {
                                const option = document.createElement('option');
                                option.value = time;
                                // Format time for display (e.g., "09:00" -> "9:00 AM")
                                const [hours, minutes] = time.split(':');
                                const hour = parseInt(hours);
                                const ampm = hour >= 12 ? 'PM' : 'AM';
                                const displayHour = hour % 12 || 12;
                                option.textContent = `${displayHour}:${minutes} ${ampm}`;
                                timeSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading time slots:', error);
                        timeSelect.innerHTML = '<option value="">Select time...</option>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load available time slots. Please try again.',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    });
            }

            // Load available staff when service and date are selected (time is optional)
            function loadAvailableStaff() {
                const serviceId = serviceSelect.value;
                const date = dateInput.value;
                let time = timeSelect.value;

                if (!serviceId || !date) {
                    staffSelect.innerHTML = '<option value="">Any available staff</option>';
                    return;
                }

                // Build URL with optional time parameter
                let url = `{{ route('appointments.available-staff') }}?service_id=${serviceId}&date=${date}`;
                
                // Add time if selected (convert from 12-hour format if needed)
                if (time) {
                    // Convert time from 12-hour format (if needed) to 24-hour format
                    if (time.includes('AM') || time.includes('PM')) {
                        const [timePart, period] = time.split(' ');
                        const [hours, minutes] = timePart.split(':');
                        let hour24 = parseInt(hours);
                        if (period === 'PM' && hour24 !== 12) {
                            hour24 += 12;
                        } else if (period === 'AM' && hour24 === 12) {
                            hour24 = 0;
                        }
                        time = `${hour24.toString().padStart(2, '0')}:${minutes}`;
                    }
                    url += `&time=${encodeURIComponent(time)}`;
                }

                // Show loading state
                staffSelect.innerHTML = '<option value="">Loading staff...</option>';
                staffSelect.disabled = true;

                fetch(url)
                    .then(response => {
                        console.log('Staff response status:', response.status);
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || `HTTP error! status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Staff data received:', data);
                        staffSelect.innerHTML = '<option value="">Any available staff</option>';
                        staffSelect.disabled = false;
                        
                        // Handle both 'staff' and 'available_staff' response formats
                        const staffList = data.staff || data.available_staff || [];
                        
                        console.log('Staff list:', staffList);
                        console.log('Staff list length:', staffList.length);
                        
                        if (!Array.isArray(staffList)) {
                            console.error('Staff list is not an array:', typeof staffList, staffList);
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'Error: Invalid staff data format';
                            option.disabled = true;
                            staffSelect.appendChild(option);
                            return;
                        }
                        
                        if (staffList.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = time ? 'No staff available for this time slot' : 'No staff available for this date';
                            option.disabled = true;
                            staffSelect.appendChild(option);
                            
                            // Show info message if no staff available
                            if (data.total_found === 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'No Staff Available',
                                    text: 'No staff members have schedules for this date and service combination.',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 4000,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            staffList.forEach(staff => {
                                const option = document.createElement('option');
                                option.value = staff.id;
                                
                                // Build staff name from available fields
                                let staffName = '';
                                if (staff.name) {
                                    staffName = staff.name;
                                } else if (staff.first_name || staff.last_name) {
                                    staffName = `${staff.first_name || ''} ${staff.last_name || ''}`.trim();
                                } else {
                                    staffName = 'Staff Member';
                                }
                                
                                option.textContent = staffName;
                                staffSelect.appendChild(option);
                            });
                            
                            console.log(`Successfully loaded ${staffList.length} staff members`);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading staff:', error);
                        console.error('Error details:', {
                            message: error.message,
                            stack: error.stack
                        });
                        staffSelect.innerHTML = '<option value="">Any available staff</option>';
                        staffSelect.disabled = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to load available staff. Please try again.',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    });
            }

            // Event listeners
            serviceSelect.addEventListener('change', function() {
                loadTimeSlots();
                loadAvailableStaff(); // Load staff when service changes
            });
            dateInput.addEventListener('change', function() {
                loadTimeSlots();
                loadAvailableStaff(); // Load staff when date changes
            });
            timeSelect.addEventListener('change', function() {
                loadAvailableStaff(); // Reload staff when time changes (to filter by time)
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                btnText.textContent = 'Booking...';
                spinner.classList.remove('hidden');
            });
        });
    </script>
</x-layout>
