@props(['service', 'showActions' => true, 'size' => 'default'])

@php
    $sizeClasses = [
        'small' => 'p-4',
        'default' => 'p-6',
        'large' => 'p-8'
    ];

    $imageHeightClasses = [
        'small' => 'h-32',
        'default' => 'h-48',
        'large' => 'h-64'
    ];
@endphp

<div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl border border-pink-100 hover:shadow-lg transition-shadow duration-200 {{ $sizeClasses[$size] }}">
    <!-- Service Image -->
    @if($service->image)
        <div class="mb-4">
            <img src="{{ $service->image_url }}"
                 alt="{{ $service->name }}"
                 class="w-full {{ $imageHeightClasses[$size] }} object-cover rounded-lg">
        </div>
    @endif

    <!-- Service Info -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">{{ $service->name }}</h3>
        <span class="text-2xl font-bold text-pink-600">{{ $service->formatted_price }}</span>
    </div>

    <p class="text-gray-600 text-sm mb-4">
        {{ $service->short_description ?: Str::limit($service->description, 100) }}
    </p>

    <!-- Service Details -->
    <div class="space-y-2 mb-4">
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Duration:</span>
            <span class="text-gray-700">{{ $service->formatted_duration }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Category:</span>
            <span class="text-gray-700">{{ ucfirst($service->category) }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Status:</span>
            <span class="px-2 py-1 rounded-full text-xs {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $service->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        @if($service->is_featured)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Featured:</span>
                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">⭐ Featured</span>
            </div>
        @endif
    </div>

    @if($showActions)
        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <div class="flex space-x-2">
                <a href="{{ route('admin.services.show', $service) }}"
                   class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                <a href="{{ route('admin.services.edit', $service) }}"
                   class="text-pink-600 hover:text-pink-800 text-sm">Edit</a>
            </div>

            <div class="flex space-x-1">
                <!-- Toggle Status -->
                <form method="POST" action="{{ route('admin.services.toggle-status', $service) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <x-confirm-button action="update" title="{{ $service->is_active ? 'Deactivate Service' : 'Activate Service' }}" text="Are you sure you want to {{ $service->is_active ? 'deactivate' : 'activate' }} this service?" class="text-sm {{ $service->is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}">
                        {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                    </x-confirm-button>
                </form>

                <!-- Toggle Featured -->
                <form method="POST" action="{{ route('admin.services.toggle-featured', $service) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <x-confirm-button action="update" title="{{ $service->is_featured ? 'Unfeature Service' : 'Feature Service' }}" text="Are you sure you want to {{ $service->is_featured ? 'unfeature' : 'feature' }} this service?" class="text-sm {{ $service->is_featured ? 'text-gray-600 hover:text-gray-800' : 'text-yellow-600 hover:text-yellow-800' }}">
                        {{ $service->is_featured ? 'Unfeature' : 'Feature' }}
                    </x-confirm-button>
                </form>

                <!-- Delete -->
                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-confirm-button action="delete" title="Delete Service" text="Are you sure you want to delete this service? This action cannot be undone!" class="text-red-600 hover:text-red-800 text-sm">
                        Delete
                    </x-confirm-button>
                </form>
            </div>
        </div>
    @endif
</div>
