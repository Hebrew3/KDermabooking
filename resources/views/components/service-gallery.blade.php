@props(['service', 'showMainImage' => true, 'showGallery' => true])

@if($service->image || ($service->gallery_images && count($service->gallery_images) > 0))
    <div class="space-y-6">
        @if($showMainImage && $service->image)
            <div>
                <h3 class="text-lg font-medium text-gray-700 mb-3">Main Image</h3>
                <div class="relative group">
                    <img src="{{ $service->image_url }}"
                         alt="{{ $service->name }}"
                         class="w-full h-64 md:h-80 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                        <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-sm font-medium">
                            Click to view full size
                        </span>
                    </div>
                </div>
            </div>
        @endif

        @if($showGallery && $service->gallery_images && count($service->gallery_images) > 0)
            <div>
                <h3 class="text-lg font-medium text-gray-700 mb-3">Gallery Images</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($service->gallery_image_urls as $index => $imageUrl)
                        <div class="relative group">
                            <img src="{{ $imageUrl }}"
                                 alt="Gallery image {{ $index + 1 }}"
                                 class="w-full h-32 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-sm">
                                    Image {{ $index + 1 }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@else
    <div class="text-center py-8">
        <div class="text-gray-400 text-4xl mb-2">📷</div>
        <p class="text-gray-500">No images available for this service</p>
    </div>
@endif
