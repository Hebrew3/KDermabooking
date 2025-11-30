<x-app-layout>
    <x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl shadow-xl p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2 flex items-center space-x-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit Service</span>
                    </h1>
                    <p class="text-pink-100 text-lg">Update service information and settings</p>
                </div>
            </div>

            <!-- Messages are handled by SweetAlert in layout -->

            <!-- Service Form -->
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                        <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Service Information</span>
                    </h2>
                </div>
                <div class="p-6">
                <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Service Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $service->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('name') border-red-500 @enderror"
                                   placeholder="Enter service name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select id="category"
                                    name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('category') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                <option value="complete_facial" {{ old('category', $service->category) === 'complete_facial' ? 'selected' : '' }}>Complete Facial</option>
                                <option value="laser_hair_removal" {{ old('category', $service->category) === 'laser_hair_removal' ? 'selected' : '' }}>Laser Hair Removal</option>
                                <option value="laser_skin_treatment" {{ old('category', $service->category) === 'laser_skin_treatment' ? 'selected' : '' }}>Laser Skin Treatment</option>
                                <option value="skin_peeling" {{ old('category', $service->category) === 'skin_peeling' ? 'selected' : '' }}>Skin Peeling</option>
                                <option value="tightening_contouring" {{ old('category', $service->category) === 'tightening_contouring' ? 'selected' : '' }}>Tightening and Contouring</option>
                                <option value="mesolipo" {{ old('category', $service->category) === 'mesolipo' ? 'selected' : '' }}>Mesolipo</option>
                                <option value="gluta_drip" {{ old('category', $service->category) === 'gluta_drip' ? 'selected' : '' }}>Gluta Drip</option>
                                <option value="waxing" {{ old('category', $service->category) === 'waxing' ? 'selected' : '' }}>Waxing</option>
                                <option value="pathologic_non_invasive" {{ old('category', $service->category) === 'pathologic_non_invasive' ? 'selected' : '' }}>Pathologic / Non-Invasive</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (₱) *</label>
                            <input type="number"
                                   id="price"
                                   name="price"
                                   value="{{ old('price', $service->price) }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('price') border-red-500 @enderror"
                                   placeholder="0.00">
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                            <input type="number"
                                   id="duration"
                                   name="duration"
                                   value="{{ old('duration', $service->duration) }}"
                                   min="1"
                                   max="1440"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('duration') border-red-500 @enderror"
                                   placeholder="60">
                            @error('duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('description') border-red-500 @enderror"
                                  placeholder="Enter detailed service description">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Images -->
                    @if($service->image)
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-700">Current Images</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Main Image</label>
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $service->image_url }}"
                                         alt="{{ $service->name }}"
                                         class="w-32 h-32 object-cover rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-600">Current main image</p>
                                        <p class="text-xs text-gray-500">Upload a new image to replace this one</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Images -->
                    <div>
                        <!-- Main Image -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Main Image</label>
                            <input type="file"
                                   id="image"
                                   name="image"
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">JPEG, PNG, JPG, GIF, or WebP. Max 5MB. Leave empty to keep current image.</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Linked Products -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Linked Products</h3>
                        <p class="text-sm text-gray-600 mb-4">Select inventory products used in this service. Stock will be automatically deducted when an appointment is completed.</p>
                        
                        <div id="linked-products-container" class="space-y-3">
                            <!-- Product rows will be added here dynamically -->
                        </div>
                        
                        <button type="button" 
                                id="add-product-btn" 
                                class="mt-4 flex items-center space-x-2 px-4 py-2 text-sm font-medium text-pink-600 hover:text-pink-700 border border-pink-300 rounded-lg hover:bg-pink-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Product</span>
                        </button>
                    </div>

                    <!-- Status Options -->
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Active Service
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="is_featured"
                                   name="is_featured"
                                   value="1"
                                   {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                Featured Service
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.services.show', $service) }}"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <x-confirm-button action="update" title="Update Service" text="Are you sure you want to update this service?" class="px-6 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg">
                            Update Service
                        </x-confirm-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Linked Products Management
        const inventoryItems = @json($inventoryItemsJson);
        
        // Get existing treatment products
        const existingProducts = @json($service->treatmentProducts->map(function($product) {
            return [
                'product_id' => $product->id,
                'quantity' => $product->pivot->quantity,
                'volume_used_per_service' => $product->pivot->volume_used_per_service
            ];
        }));
        
        let productRowIndex = 0;
        const oldProducts = @json(old('treatment_products', []));

        // Add product row
        function addProductRow(productId = '', quantity = 1, volumeUsedPerService = '') {
            const container = document.getElementById('linked-products-container');
            const row = document.createElement('div');
            row.className = 'flex items-start space-x-4 p-4 bg-white border border-gray-200 rounded-lg';
            row.dataset.index = productRowIndex;
            
            row.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select name="treatment_products[${productRowIndex}][product_id]" 
                            class="product-select w-full px-3 py-2 border border-purple-500 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select Product</option>
                        ${inventoryItems.map(item => `
                            <option value="${item.id}" ${productId == item.id ? 'selected' : ''}>
                                ${item.name} (${item.category} - Stock: ${item.stock} ${item.unit})
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" 
                           name="treatment_products[${productRowIndex}][quantity]" 
                           value="${volumeUsedPerService ? 0 : quantity}"
                           min="0"
                           step="1"
                           class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 ${volumeUsedPerService ? 'bg-gray-100 cursor-not-allowed' : ''}"
                           ${volumeUsedPerService ? 'readonly' : 'required'}>
                    <p class="text-xs text-gray-500 mt-1">Auto-set to 0 when using mL</p>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Volume Used (mL)</label>
                    <input type="number" 
                           name="treatment_products[${productRowIndex}][volume_used_per_service]" 
                           value="${volumeUsedPerService}"
                           min="0"
                           step="0.01"
                           class="volume-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="e.g., 25">
                    <p class="text-xs text-gray-500 mt-1">Leave empty if not using mL</p>
                </div>
                <div class="flex items-end pt-6">
                    <button type="button" 
                            onclick="removeProductRow(this)"
                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(row);
            
            // Add event listeners for volume/quantity toggle
            const volumeInput = row.querySelector('.volume-input');
            const quantityInput = row.querySelector('.quantity-input');
            
            volumeInput.addEventListener('input', function() {
                if (this.value && parseFloat(this.value) > 0) {
                    // If volume is set, set quantity to 0 and disable it
                    quantityInput.value = 0;
                    quantityInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    quantityInput.setAttribute('readonly', 'readonly');
                    quantityInput.removeAttribute('required');
                } else {
                    // If volume is cleared, enable quantity
                    quantityInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    quantityInput.removeAttribute('readonly');
                    quantityInput.setAttribute('required', 'required');
                    if (quantityInput.value == 0) {
                        quantityInput.value = 1;
                    }
                }
            });
            
            // Initialize state on load
            if (volumeUsedPerService && parseFloat(volumeUsedPerService) > 0) {
                quantityInput.value = 0;
                quantityInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                quantityInput.setAttribute('readonly', 'readonly');
                quantityInput.removeAttribute('required');
            }
            
            productRowIndex++;
        }

        // Remove product row
        function removeProductRow(button) {
            button.closest('.flex.items-start').remove();
        }

        // Initialize with existing or old values
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('add-product-btn');
            
            // Priority: old input (validation errors) > existing products > empty row
            if (oldProducts && oldProducts.length > 0) {
                oldProducts.forEach(product => {
                    if (product && product.product_id) {
                        addProductRow(product.product_id, product.quantity || 1, product.volume_used_per_service || '');
                    }
                });
            } else if (existingProducts && existingProducts.length > 0) {
                existingProducts.forEach(product => {
                    addProductRow(product.product_id, product.quantity || 1, product.volume_used_per_service || '');
                });
            } else {
                // Add one empty row by default
                addProductRow();
            }
            
            addBtn.addEventListener('click', function() {
                addProductRow();
            });
        });
    </script>
</x-app-layout>
