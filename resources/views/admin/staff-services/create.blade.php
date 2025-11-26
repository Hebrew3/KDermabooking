<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('admin.staff-services.index') }}" 
               class="text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Assign Service to Staff</h1>
                <p class="text-gray-600 mt-2">Create a new staff service assignment</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <form action="{{ route('admin.staff-services.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf

            <!-- Staff Selection -->
            <div class="mb-6">
                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Staff Member <span class="text-red-500">*</span>
                </label>
                <select name="staff_id" id="staff_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                        onchange="loadAvailableServices()">
                    <option value="">Choose a staff member...</option>
                    @foreach($staff as $staffMember)
                        <option value="{{ $staffMember->id }}" {{ old('staff_id') == $staffMember->id ? 'selected' : '' }}>
                            {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                            @if($staffMember->staffSpecializations->count() > 0)
                                ({{ $staffMember->staffSpecializations->pluck('specialization')->map(fn($s) => ucwords(str_replace('_', ' ', $s)))->join(', ') }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('staff_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Service Selection -->
            <div class="mb-6">
                <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Service <span class="text-red-500">*</span>
                </label>
                <select name="service_id" id="service_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Choose a service...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" 
                                data-price="{{ $service->price }}"
                                data-specialization="{{ $service->required_specialization }}"
                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} ({{ $service->formatted_price }})
                            @if($service->requires_specialization)
                                - Requires: {{ ucwords(str_replace('_', ' ', $service->required_specialization)) }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div id="service-info" class="mt-2 text-sm text-gray-600 hidden">
                    <p id="specialization-info"></p>
                </div>
            </div>

            <!-- Primary Service -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                           {{ old('is_primary') ? 'checked' : '' }}
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="is_primary" class="ml-2 block text-sm text-gray-700">
                        This is a primary service for this staff member
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-1">Primary services are highlighted and prioritized in staff profiles</p>
            </div>

            <!-- Custom Price -->
            <div class="mb-6">
                <label for="custom_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Custom Price (Optional)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">₱</span>
                    <input type="number" name="custom_price" id="custom_price" 
                           value="{{ old('custom_price') }}"
                           step="0.01" min="0" max="99999999.99"
                           placeholder="Leave empty to use service default"
                           class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
                @error('custom_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Set a custom rate for this staff-service combination (maximum: ₱99,999,999.99)</p>
            </div>

            <!-- Proficiency Level -->
            <div class="mb-6">
                <label for="proficiency_level" class="block text-sm font-medium text-gray-700 mb-2">
                    Proficiency Level <span class="text-red-500">*</span>
                </label>
                <select name="proficiency_level" id="proficiency_level" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Select proficiency level...</option>
                    <option value="1" {{ old('proficiency_level') == '1' ? 'selected' : '' }}>1 - Beginner</option>
                    <option value="2" {{ old('proficiency_level') == '2' ? 'selected' : '' }}>2 - Novice</option>
                    <option value="3" {{ old('proficiency_level') == '3' ? 'selected' : '' }}>3 - Intermediate</option>
                    <option value="4" {{ old('proficiency_level') == '4' ? 'selected' : '' }}>4 - Advanced</option>
                    <option value="5" {{ old('proficiency_level') == '5' ? 'selected' : '' }}>5 - Expert</option>
                </select>
                @error('proficiency_level')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Assignment Notes (Optional)
                </label>
                <textarea name="notes" id="notes" rows="3" 
                          placeholder="Any special notes or instructions for this assignment..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-4">
                <button type="submit" 
                        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Assign Service
                </button>
                <a href="{{ route('admin.staff-services.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for dynamic service loading -->
<script>
    function loadAvailableServices() {
        const staffId = document.getElementById('staff_id').value;
        const serviceSelect = document.getElementById('service_id');
        
        if (!staffId) {
            serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
            return;
        }

        // Show loading
        serviceSelect.innerHTML = '<option value="">Loading services...</option>';
        
        // Fetch available services for this staff member
        fetch(`{{ route('admin.staff-services.available') }}?staff_id=${staffId}`)
            .then(response => response.json())
            .then(services => {
                serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
                
                services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.name} (₱${parseFloat(service.price).toFixed(2)})`;
                    if (service.requires_specialization) {
                        option.textContent += ` - Requires: ${service.required_specialization.replace(/_/g, ' ')}`;
                    }
                    option.dataset.price = service.price;
                    option.dataset.specialization = service.required_specialization || '';
                    serviceSelect.appendChild(option);
                });

                if (services.length === 0) {
                    serviceSelect.innerHTML = '<option value="">No available services for this staff member</option>';
                }
            })
            .catch(error => {
                console.error('Error loading services:', error);
                serviceSelect.innerHTML = '<option value="">Error loading services</option>';
            });
    }

    // Update custom price placeholder when service changes
    document.getElementById('service_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const customPriceInput = document.getElementById('custom_price');
        
        if (selectedOption.dataset.price) {
            customPriceInput.placeholder = `Default: ₱${parseFloat(selectedOption.dataset.price).toFixed(2)}`;
        } else {
            customPriceInput.placeholder = 'Leave empty to use service default';
        }
    });
</script>
</div>
</div>
</x-app-layout>
