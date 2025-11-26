<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 py-20">
        <!-- Background decorations -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-200/30 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">Edit Appointment</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Reschedule your appointment with our expert dermatologists.
                </p>
            </div>

            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('appointments.show', $appointment) }}"
                    class="inline-flex items-center text-gray-600 hover:text-gray-800 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Appointment
                </a>
            </div>

            <!-- Edit Appointment Form -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12 border border-pink-100">
                <form action="{{ route('appointments.update', $appointment) }}" method="POST" id="editAppointmentForm">
                    @csrf
                    @method('PUT')
                    
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
                                    <option value="{{ $service->id }}" 
                                        {{ $appointment->service_id == $service->id ? 'selected' : '' }}
                                        data-price="{{ $service->price }}" data-duration="{{ $service->duration }}">
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
                                min="{{ date('Y-m-d') }}" value="{{ $appointment->appointment_date }}"
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
                                <option value="{{ $appointment->appointment_time }}" selected>
                                    {{ date('g:i A', strtotime($appointment->appointment_time)) }}
                                </option>
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
                                @if($appointment->staff)
                                    <option value="{{ $appointment->staff->id }}" selected>
                                        {{ $appointment->staff->first_name }} {{ $appointment->staff->last_name }}
                                    </option>
                                @endif
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
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 resize-none">{{ $appointment->client_notes }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('appointments.show', $appointment) }}"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-8 py-4 rounded-xl text-center font-semibold transition-colors">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                            class="flex-1 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-8 py-4 rounded-xl font-semibold shadow-md hover:shadow-rose-200/80 transition-all">
                            <span id="btnText">Update Appointment</span>
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

    <!-- JavaScript for dynamic form behavior -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service_id');
            const dateInput = document.getElementById('appointment_date');
            const timeSelect = document.getElementById('appointment_time');
            const staffSelect = document.getElementById('staff_id');
            const form = document.getElementById('editAppointmentForm');
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
                    .then(response => response.json())
                    .then(data => {
                        const currentTime = '{{ $appointment->appointment_time }}';
                        timeSelect.innerHTML = '<option value="">Select time...</option>';
                        
                        // Add current appointment time as an option
                        if (currentTime) {
                            const currentOption = document.createElement('option');
                            currentOption.value = currentTime;
                            currentOption.textContent = '{{ date("g:i A", strtotime($appointment->appointment_time)) }} (Current)';
                            currentOption.selected = true;
                            timeSelect.appendChild(currentOption);
                        }
                        
                        // Add available time slots
                        data.timeSlots.forEach(slot => {
                            if (slot.time !== currentTime) {
                                const option = document.createElement('option');
                                option.value = slot.time;
                                option.textContent = slot.formatted_time;
                                timeSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading time slots:', error);
                    });
            }

            // Load available staff when service, date, and time are selected
            function loadAvailableStaff() {
                const serviceId = serviceSelect.value;
                const date = dateInput.value;
                const time = timeSelect.value;

                if (!serviceId || !date || !time) {
                    staffSelect.innerHTML = '<option value="">Any available staff</option>';
                    return;
                }

                fetch(`{{ route('appointments.available-staff') }}?service_id=${serviceId}&date=${date}&time=${time}`)
                    .then(response => response.json())
                    .then(data => {
                        const currentStaffId = '{{ $appointment->staff_id ?? '' }}';
                        staffSelect.innerHTML = '<option value="">Any available staff</option>';
                        
                        // Add current staff as an option if exists
                        @if($appointment->staff)
                        if (currentStaffId) {
                            const currentOption = document.createElement('option');
                            currentOption.value = currentStaffId;
                            currentOption.textContent = '{{ $appointment->staff->first_name }} {{ $appointment->staff->last_name }} (Current)';
                            currentOption.selected = true;
                            staffSelect.appendChild(currentOption);
                        }
                        @endif
                        
                        // Add available staff
                        data.staff.forEach(staff => {
                            if (staff.id != currentStaffId) {
                                const option = document.createElement('option');
                                option.value = staff.id;
                                option.textContent = `${staff.first_name} ${staff.last_name}`;
                                staffSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading staff:', error);
                    });
            }

            // Event listeners
            serviceSelect.addEventListener('change', loadTimeSlots);
            dateInput.addEventListener('change', loadTimeSlots);
            timeSelect.addEventListener('change', loadAvailableStaff);

            // Form submission
            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                btnText.textContent = 'Updating...';
                spinner.classList.remove('hidden');
            });
        });
    </script>
</x-layout>
