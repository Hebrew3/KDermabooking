<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.inventory.show', $inventoryItem) }}" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Inventory Item</h1>
                    <p class="text-gray-600 mt-2">{{ $inventoryItem->name }} - {{ $inventoryItem->sku }}</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="max-w-4xl">
            <div class="bg-white rounded-xl shadow-sm p-8">
                <form method="POST" action="{{ route('admin.inventory.update', $inventoryItem) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Item Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Item Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $inventoryItem->name) }}" required 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                                SKU <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $inventoryItem->sku) }}" required 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category" id="category" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category', $inventoryItem->category) == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                                @endforeach
                                <option value="skincare" {{ old('category', $inventoryItem->category) == 'skincare' ? 'selected' : '' }}>Skincare</option>
                                <option value="equipment" {{ old('category', $inventoryItem->category) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="supplies" {{ old('category', $inventoryItem->category) == 'supplies' ? 'selected' : '' }}>Supplies</option>
                                <option value="tools" {{ old('category', $inventoryItem->category) == 'tools' ? 'selected' : '' }}>Tools</option>
                                <option value="consumables" {{ old('category', $inventoryItem->category) == 'consumables' ? 'selected' : '' }}>Consumables</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Brand -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand', $inventoryItem->brand) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cost Price -->
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                                Cost Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $inventoryItem->cost_price) }}" 
                                       step="0.01" min="0" required 
                                       class="w-full pl-8 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            @error('cost_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">Selling Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $inventoryItem->selling_price) }}" 
                                       step="0.01" min="0" 
                                       class="w-full pl-8 border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            @error('selling_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Stock -->
                        <div>
                            <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Stock <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', $inventoryItem->current_stock) }}" 
                                   min="0" required 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('current_stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minimum Stock -->
                        <div>
                            <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Stock <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $inventoryItem->minimum_stock) }}" 
                                   min="0" required 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('minimum_stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Maximum Stock -->
                        <div>
                            <label for="maximum_stock" class="block text-sm font-medium text-gray-700 mb-2">Maximum Stock</label>
                            <input type="number" name="maximum_stock" id="maximum_stock" value="{{ old('maximum_stock', $inventoryItem->maximum_stock) }}" 
                                   min="0" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('maximum_stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit (Packaging Type) -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit (Packaging Type) <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" id="unit" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="piece" {{ old('unit', $inventoryItem->unit) == 'piece' ? 'selected' : '' }}>Piece</option>
                                <option value="bottle" {{ old('unit', $inventoryItem->unit) == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                <option value="box" {{ old('unit', $inventoryItem->unit) == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="pack" {{ old('unit', $inventoryItem->unit) == 'pack' ? 'selected' : '' }}>Pack</option>
                                <option value="tube" {{ old('unit', $inventoryItem->unit) == 'tube' ? 'selected' : '' }}>Tube</option>
                                <option value="jar" {{ old('unit', $inventoryItem->unit) == 'jar' ? 'selected' : '' }}>Jar</option>
                                <option value="set" {{ old('unit', $inventoryItem->unit) == 'set' ? 'selected' : '' }}>Set</option>
                                <option value="mL" {{ old('unit', $inventoryItem->unit) == 'mL' ? 'selected' : '' }}>mL</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">The packaging type (e.g., Bottle, Box, Pack)</p>
                            @error('unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content per Unit -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Content per Unit <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="content_per_unit" class="block text-xs text-gray-600 mb-1">Quantity</label>
                                    <input type="number" name="content_per_unit" id="content_per_unit" 
                                           value="{{ old('content_per_unit', $inventoryItem->content_per_unit) }}" 
                                           step="0.01" min="0" required
                                           class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                                           placeholder="e.g., 20">
                                    @error('content_per_unit')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="content_unit" class="block text-xs text-gray-600 mb-1">Unit</label>
                                    <select name="content_unit" id="content_unit" required 
                                            class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">Select unit</option>
                                        <option value="mL" {{ old('content_unit', $inventoryItem->content_unit) == 'mL' ? 'selected' : '' }}>mL</option>
                                        <option value="L" {{ old('content_unit', $inventoryItem->content_unit) == 'L' ? 'selected' : '' }}>L</option>
                                        <option value="g" {{ old('content_unit', $inventoryItem->content_unit) == 'g' ? 'selected' : '' }}>g</option>
                                        <option value="kg" {{ old('content_unit', $inventoryItem->content_unit) == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="pc" {{ old('content_unit', $inventoryItem->content_unit) == 'pc' ? 'selected' : '' }}>pc</option>
                                        <option value="pcs" {{ old('content_unit', $inventoryItem->content_unit) == 'pcs' ? 'selected' : '' }}>pcs</option>
                                        <option value="oz" {{ old('content_unit', $inventoryItem->content_unit) == 'oz' ? 'selected' : '' }}>oz</option>
                                        <option value="fl oz" {{ old('content_unit', $inventoryItem->content_unit) == 'fl oz' ? 'selected' : '' }}>fl oz</option>
                                    </select>
                                    @error('content_unit')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">The actual quantity inside the container (e.g., 20 mL, 250 g, 1 L)</p>
                        </div>

                        <!-- Volume per Container (for mL tracking) -->
                        <div>
                            <label for="volume_per_container" class="block text-sm font-medium text-gray-700 mb-2">
                                Volume per Container (mL)
                            </label>
                            <input type="number" name="volume_per_container" id="volume_per_container" 
                                   value="{{ old('volume_per_container', $inventoryItem->volume_per_container) }}" 
                                   step="0.01" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="e.g., 20">
                            <p class="mt-1 text-xs text-gray-500">Volume in mL per container/bottle (e.g., 20 mL per bottle). Leave empty if not using mL-based tracking.</p>
                            @error('volume_per_container')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier -->
                        <div>
                            <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier', $inventoryItem->supplier) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('supplier')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $inventoryItem->expiry_date?->format('Y-m-d')) }}" 
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('expiry_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Storage Location -->
                        <div>
                            <label for="storage_location" class="block text-sm font-medium text-gray-700 mb-2">Storage Location</label>
                            <input type="text" name="storage_location" id="storage_location" value="{{ old('storage_location', $inventoryItem->storage_location) }}" 
                                   placeholder="e.g., Shelf A1, Room 2, Refrigerator"
                                   class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            @error('storage_location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="is_active" id="is_active" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="1" {{ old('is_active', $inventoryItem->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $inventoryItem->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  placeholder="Detailed description of the item..."
                                  class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('description', $inventoryItem->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  placeholder="Additional notes or special instructions..."
                                  class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('notes', $inventoryItem->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 mt-8">
                        <a href="{{ route('admin.inventory.show', $inventoryItem) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                            Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
