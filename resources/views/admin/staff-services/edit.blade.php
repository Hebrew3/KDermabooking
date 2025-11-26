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
                <h1 class="text-3xl font-bold text-gray-900">Edit Staff Service Assignment</h1>
                <p class="text-gray-600 mt-2">Update assignment details for {{ $staffService->staff->first_name }} {{ $staffService->staff->last_name }} - {{ $staffService->service->name }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <form action="{{ route('admin.staff-services.update', $staffService) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf
            @method('PUT')

            <!-- Current Assignment Info -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Current Assignment</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Staff Member:</span>
                        <span class="font-medium">{{ $staffService->staff->first_name }} {{ $staffService->staff->last_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium">{{ $staffService->service->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Service Price:</span>
                        <span class="font-medium">{{ $staffService->service->formatted_price }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium">{{ $staffService->service->formatted_duration }}</span>
                    </div>
                </div>
            </div>

            <!-- Primary Service -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                           {{ old('is_primary', $staffService->is_primary) ? 'checked' : '' }}
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
                           value="{{ old('custom_price', $staffService->custom_price) }}"
                           step="0.01" min="0" max="99999999.99"
                           placeholder="Leave empty to use service default (₱{{ number_format($staffService->service->price, 2) }})"
                           class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
                @error('custom_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    Service default: {{ $staffService->service->formatted_price }}
                    @if($staffService->custom_price)
                        | Current custom rate: ₱{{ number_format($staffService->custom_price, 2) }}
                    @endif
                    | Maximum: ₱99,999,999.99
                </p>
            </div>

            <!-- Proficiency Level -->
            <div class="mb-6">
                <label for="proficiency_level" class="block text-sm font-medium text-gray-700 mb-2">
                    Proficiency Level <span class="text-red-500">*</span>
                </label>
                <select name="proficiency_level" id="proficiency_level" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Select proficiency level...</option>
                    <option value="1" {{ old('proficiency_level', $staffService->proficiency_level) == '1' ? 'selected' : '' }}>1 - Beginner</option>
                    <option value="2" {{ old('proficiency_level', $staffService->proficiency_level) == '2' ? 'selected' : '' }}>2 - Novice</option>
                    <option value="3" {{ old('proficiency_level', $staffService->proficiency_level) == '3' ? 'selected' : '' }}>3 - Intermediate</option>
                    <option value="4" {{ old('proficiency_level', $staffService->proficiency_level) == '4' ? 'selected' : '' }}>4 - Advanced</option>
                    <option value="5" {{ old('proficiency_level', $staffService->proficiency_level) == '5' ? 'selected' : '' }}>5 - Expert</option>
                </select>
                @error('proficiency_level')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Current level: {{ $staffService->proficiency_text }}</p>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Assignment Notes (Optional)
                </label>
                <textarea name="notes" id="notes" rows="3" 
                          placeholder="Any special notes or instructions for this assignment..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">{{ old('notes', $staffService->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assignment History -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-900 mb-2">Assignment History</h3>
                <div class="text-xs text-blue-800">
                    <p>Created: {{ $staffService->created_at->format('M d, Y g:i A') }}</p>
                    @if($staffService->updated_at != $staffService->created_at)
                        <p>Last Updated: {{ $staffService->updated_at->format('M d, Y g:i A') }}</p>
                    @endif
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-4">
                <button type="submit" 
                        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Update Assignment
                </button>
                <a href="{{ route('admin.staff-services.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="button" onclick="confirmDelete()" 
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors ml-auto">
                    Delete Assignment
                </button>
            </div>
        </form>

        <!-- Hidden delete form -->
        <form id="delete-form" action="{{ route('admin.staff-services.destroy', $staffService) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- JavaScript for delete confirmation -->
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Delete Assignment?',
            text: 'This will permanently remove this service assignment from the staff member.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        });
    }
</script>
</div>
</div>
</x-app-layout>
