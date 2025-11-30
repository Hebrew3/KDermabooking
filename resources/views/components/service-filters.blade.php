@props(['categories' => [], 'currentFilters' => []])

<form method="GET" action="{{ route('admin.services.index') }}" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text"
                   id="search"
                   name="search"
                   value="{{ $currentFilters['search'] ?? '' }}"
                   placeholder="Search services..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
        </div>

        <!-- Category Filter -->
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select id="category"
                    name="category"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                <option value="">All Categories</option>
                @php
                    $categoryMap = [
                        'complete_facial' => 'Complete Facial',
                        'laser_hair_removal' => 'Laser Hair Removal',
                        'laser_skin_treatment' => 'Laser Skin Treatment',
                        'skin_peeling' => 'Skin Peeling',
                        'tightening_contouring' => 'Tightening and Contouring',
                        'mesolipo' => 'Mesolipo',
                        'gluta_drip' => 'Gluta Drip',
                        'waxing' => 'Waxing',
                        'pathologic_non_invasive' => 'Pathologic / Non-Invasive',
                        'facial' => 'Complete Facial',
                        'laser' => 'Laser Skin Treatment',
                        'injection' => 'Mesolipo',
                        'peel' => 'Skin Peeling',
                        'consultation' => 'Pathologic / Non-Invasive',
                        'other' => 'Other',
                    ];
                @endphp
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ ($currentFilters['category'] ?? '') === $category ? 'selected' : '' }}>
                        {{ $categoryMap[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status"
                    name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                <option value="">All Status</option>
                <option value="active" {{ ($currentFilters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ ($currentFilters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Featured Filter -->
        <div>
            <label for="featured" class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
            <select id="featured"
                    name="featured"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                <option value="">All Services</option>
                <option value="1" {{ ($currentFilters['featured'] ?? '') === '1' ? 'selected' : '' }}>Featured Only</option>
            </select>
        </div>
    </div>

    <div class="flex justify-between items-center mt-4">
        <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
            Apply Filters
        </button>

        <a href="{{ route('admin.services.index') }}"
           class="text-gray-500 hover:text-gray-700 text-sm">
            Clear Filters
        </a>
    </div>
</form>
