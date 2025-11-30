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
                    <a href="{{ route('admin.appointments.index') }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Schedule New Appointment</h1>
                        <p class="text-gray-600 mt-2">Create a new appointment for a client</p>
                    </div>
                </div>
            </div>

            <!-- Create Form -->
            <div class="max-w-3xl">
                <div class="bg-white rounded-xl shadow-sm p-8">
                    <form method="POST" action="{{ route('admin.appointments.store') }}" id="appointmentForm">
                        @csrf
                        
                        <!-- Hidden input to set walk-in as default -->
                        <input type="hidden" name="is_walkin" value="1">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Walk-in Customer Fields -->
                            <div class="md:col-span-2" id="walkinCustomerSection">
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
                                                   value="{{ old('walkin_customer_name') }}"
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
                                                   value="{{ old('walkin_customer_phone') }}"
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
                                                   value="{{ old('walkin_customer_email') }}"
                                                   placeholder="customer@example.com"
                                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                            @error('walkin_customer_email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Selection -->
                            <div class="md:col-span-2" id="dateSection">
                                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Appointment Date <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500 font-normal ml-2">(Automatically set to today)</span>
                                </label>
                                <input type="date" 
                                       name="appointment_date" 
                                       id="appointment_date" 
                                       value="{{ date('Y-m-d') }}"
                                       readonly
                                       required 
                                       class="w-full border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-pink-500 focus:border-pink-500">
                                @error('appointment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Service Selection -->
                            <div class="md:col-span-2" id="serviceSection">
                                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Service <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="service_id" id="service_id" required class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 pl-10 pr-4 py-2.5 appearance-none bg-white">
                                        <option value="">Choose a service...</option>
                                        @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                                data-price="{{ $service->formatted_price }}" 
                                                data-duration="{{ $service->formatted_duration }}"
                                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - {{ $service->formatted_price }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                        </svg>
                                    </div>
                                </div>
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

                            <!-- Walk-in Time Selection -->
                            <div class="md:col-span-2" id="walkinTimeSection">
                                <label for="walkin_appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Appointment Time <span class="text-red-500">*</span>
                                </label>
                                <input type="time" 
                                       name="appointment_time" 
                                       id="walkin_appointment_time" 
                                       value="{{ old('appointment_time') }}"
                                       required 
                                       class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                                       onchange="loadAvailableStaffForWalkin()">
                                @error('appointment_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Available Staff Selection -->
                            <div class="md:col-span-2" id="staffSectionWalkin">
                                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assign Staff <span class="text-gray-500">(Optional)</span>
                                </label>
                                <div id="staffLoadingWalkin" class="hidden mb-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-pink-500 mr-2"></div>
                                        <span>Loading available staff...</span>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select name="staff_id" 
                                            id="staff_id" 
                                            class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 pl-10 pr-4 py-2.5 appearance-none bg-white">
                                        <option value="">Any available staff</option>
                                        @if(old('staff_id'))
                                            @foreach($staff as $staffMember)
                                                @if(old('staff_id') == $staffMember->id)
                                                    <option value="{{ $staffMember->id }}" selected>{{ $staffMember->full_name ?? $staffMember->name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div id="staffInfoWalkin" class="mt-2 text-sm text-gray-600 hidden"></div>
                                @error('staff_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Admin Notes (Optional)
                            </label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="3" 
                                      placeholder="Any special instructions or notes..."
                                      class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="{{ route('admin.appointments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                Cancel
                            </a>
                            <x-confirm-button action="create" title="Schedule Appointment" text="Are you sure you want to schedule this appointment?" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg">
                                Schedule Appointment
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
                serviceDetails.classList.remove('hidden');
            } else {
                serviceDetails.classList.add('hidden');
            }
        });

        // Load available staff for walk-in appointments
        function loadAvailableStaffForWalkin() {
            const serviceId = document.getElementById('service_id').value;
            const date = document.getElementById('appointment_date').value;
            const time = document.getElementById('walkin_appointment_time').value;
            const staffSelect = document.getElementById('staff_id');
            const staffLoading = document.getElementById('staffLoadingWalkin');
            const staffInfo = document.getElementById('staffInfoWalkin');

            if (!serviceId || !date || !time) {
                staffSelect.innerHTML = '<option value="">Please select service, date, and time first</option>';
                staffSelect.disabled = false;
                staffInfo.classList.add('hidden');
                return;
            }

            staffLoading.classList.remove('hidden');
            staffSelect.disabled = true;

            // Use admin route for available staff
            const url = `{{ route('admin.appointments.available-staff') }}?service_id=${serviceId}&date=${date}&time=${time}`;
            console.log('Loading staff from:', url);
            
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
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    staffLoading.classList.add('hidden');
                    staffSelect.innerHTML = '<option value="">Any available staff</option>';

                    if (data.error) {
                        staffInfo.innerHTML = `
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="font-medium text-red-900">Error Loading Staff</p>
                                <p class="text-red-700 text-sm">${data.message || 'Unknown error occurred'}</p>
                            </div>
                        `;
                        staffInfo.classList.remove('hidden');
                        staffSelect.disabled = false;
                    } else if ((data.available_staff && data.available_staff.length > 0) || (data.staff && data.staff.length > 0)) {
                        // Handle both 'available_staff' and 'staff' keys for compatibility
                        const staffList = data.available_staff || data.staff || [];
                        
                        staffList.forEach(staff => {
                            const option = document.createElement('option');
                            option.value = staff.id;
                            option.textContent = staff.name || (staff.first_name + ' ' + staff.last_name);
                            staffSelect.appendChild(option);
                        });

                        const timeDisplay = time ? formatTime12Hour(time) : 'selected time';
                        staffInfo.innerHTML = `
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="font-medium text-blue-900">Available Staff</p>
                                <p class="text-blue-700 text-sm">${staffList.length} staff member(s) available for ${timeDisplay}</p>
                            </div>
                        `;
                        staffInfo.classList.remove('hidden');
                        staffSelect.disabled = false;
                    } else {
                        const timeDisplay = time ? formatTime12Hour(time) : 'selected time';
                        staffInfo.innerHTML = `
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="font-medium text-yellow-900">No Staff Available</p>
                                <p class="text-yellow-700 text-sm">No staff members are scheduled for this day and time. You can still book and we'll assign available staff.</p>
                            </div>
                        `;
                        staffInfo.classList.remove('hidden');
                        staffSelect.disabled = false;
                    }
                })
                .catch(error => {
                    staffLoading.classList.add('hidden');
                    console.error('Error loading staff:', error);
                    
                    // Show error message
                    staffSelect.innerHTML = '<option value="">Error loading staff. Please try again.</option>';
                    staffInfo.innerHTML = `
                        <div class="bg-red-50 p-3 rounded-lg">
                            <p class="font-medium text-red-900">Error Loading Staff</p>
                            <p class="text-red-700 text-sm">Unable to load available staff. Please check your connection and try again.</p>
                            <p class="text-red-600 text-xs mt-1">Error: ${error.message || 'Network or server error'}</p>
                        </div>
                    `;
                    staffInfo.classList.remove('hidden');
                    staffSelect.disabled = false;
                });
        }

        // Format time to 12-hour format
        function formatTime12Hour(time24) {
            const [hours, minutes] = time24.split(':');
            const hour12 = hours % 12 || 12;
            const ampm = hours < 12 ? 'AM' : 'PM';
            return `${hour12}:${minutes} ${ampm}`;
        }

        // Service change handler - also load staff if time is already selected
        document.getElementById('service_id').addEventListener('change', function() {
            const timeInput = document.getElementById('walkin_appointment_time');
            if (timeInput && timeInput.value) {
                loadAvailableStaffForWalkin();
            }
        });

        // Date change handler - load staff if service and time are already selected
        document.getElementById('appointment_date').addEventListener('change', function() {
            const serviceId = document.getElementById('service_id').value;
            const time = document.getElementById('walkin_appointment_time').value;
            if (serviceId && time && this.value) {
                loadAvailableStaffForWalkin();
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // If there's an old service value (validation errors), show the details
            const serviceSelect = document.getElementById('service_id');
            if (serviceSelect.value) {
                serviceSelect.dispatchEvent(new Event('change'));
            }

            // If all fields are filled, load staff (this will automatically filter by schedule)
            const serviceId = document.getElementById('service_id').value;
            const date = document.getElementById('appointment_date').value;
            const time = document.getElementById('walkin_appointment_time').value;
            if (serviceId && date && time) {
                // Small delay to ensure DOM is ready
                setTimeout(function() {
                    loadAvailableStaffForWalkin();
                }, 100);
            }
        });
    </script>
</x-app-layout>
