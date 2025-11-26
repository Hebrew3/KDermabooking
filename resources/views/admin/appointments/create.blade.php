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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Type Selection -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Customer Type
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="is_walkin" value="0" id="registeredCustomer" checked class="mr-2" onchange="toggleCustomerType()">
                                        <span>Registered Client</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="is_walkin" value="1" id="walkinCustomer" {{ old('is_walkin') == '1' ? 'checked' : '' }} class="mr-2" onchange="toggleCustomerType()">
                                        <span>Walk-in Customer</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Client Selection (Registered) -->
                            <div class="md:col-span-2" id="registeredClientSection">
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Client <span class="text-red-500">*</span>
                                </label>
                                <select name="client_id" id="client_id" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select a customer...</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->email }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Walk-in Customer Fields -->
                            <div class="md:col-span-2 hidden" id="walkinCustomerSection">
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

                            <!-- Service Selection -->
                            <div>
                                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Select Service <span class="text-red-500 ml-1">*</span>
                                    </span>
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
                            </div>

                            <!-- Staff Selection -->
                            <div>
                                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Assign Staff <span class="text-gray-500 text-xs font-normal ml-1">(Optional)</span>
                                    </span>
                                </label>
                                <div class="relative">
                                    <select name="staff_id" id="staff_id" class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 pl-10 pr-4 py-2.5 appearance-none bg-white">
                                        <option value="">Any available staff</option>
                                        @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}" {{ old('staff_id') == $staffMember->id ? 'selected' : '' }}>
                                            {{ $staffMember->name }} ({{ ucfirst($staffMember->role) }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
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
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ old('appointment_date', date('Y-m-d')) }}"
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
                                       value="{{ old('appointment_time') }}"
                                       required 
                                       class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                @error('appointment_time')
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

                        <!-- Service Details -->
                        <div id="serviceDetails" class="mt-6 p-4 bg-pink-50 rounded-lg hidden">
                            <h3 class="font-medium text-gray-900 mb-2">Service Details</h3>
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
        // Toggle between registered client and walk-in customer
        function toggleCustomerType() {
            const isWalkin = document.getElementById('walkinCustomer').checked;
            const registeredSection = document.getElementById('registeredClientSection');
            const walkinSection = document.getElementById('walkinCustomerSection');
            const clientSelect = document.getElementById('client_id');
            const appointmentDate = document.getElementById('appointment_date');
            const today = new Date().toISOString().split('T')[0];
            
            if (isWalkin) {
                registeredSection.classList.add('hidden');
                walkinSection.classList.remove('hidden');
                clientSelect.removeAttribute('required');
                clientSelect.value = '';
                // Set date to today and disable for walk-in
                appointmentDate.value = today;
                appointmentDate.setAttribute('readonly', 'readonly');
                appointmentDate.style.backgroundColor = '#f3f4f6';
                appointmentDate.style.cursor = 'not-allowed';
            } else {
                registeredSection.classList.remove('hidden');
                walkinSection.classList.add('hidden');
                clientSelect.setAttribute('required', 'required');
                document.getElementById('walkin_customer_name').value = '';
                document.getElementById('walkin_customer_phone').value = '';
                document.getElementById('walkin_customer_email').value = '';
                // Re-enable date field for registered clients
                appointmentDate.removeAttribute('readonly');
                appointmentDate.style.backgroundColor = '';
                appointmentDate.style.cursor = '';
            }
        }

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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date restriction if walk-in is pre-selected
            const isWalkin = document.getElementById('walkinCustomer').checked;
            if (isWalkin) {
                const appointmentDate = document.getElementById('appointment_date');
                const today = new Date().toISOString().split('T')[0];
                appointmentDate.value = today;
                appointmentDate.setAttribute('readonly', 'readonly');
                appointmentDate.style.backgroundColor = '#f3f4f6';
                appointmentDate.style.cursor = 'not-allowed';
            }
            // Initialize customer type toggle
            toggleCustomerType();
            
            // If there's an old service value (validation errors), show the details
            const serviceSelect = document.getElementById('service_id');
            if (serviceSelect.value) {
                serviceSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
