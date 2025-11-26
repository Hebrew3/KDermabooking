<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.inventory.index') }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $inventoryItem->name }}</h1>
                        <p class="text-gray-600 mt-2">SKU: {{ $inventoryItem->sku }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.inventory.edit', $inventoryItem) }}" 
                       class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Edit Item
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Item Information -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Item Information</h2>
                        <div class="flex space-x-2">
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $inventoryItem->stock_status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $inventoryItem->stock_status)) }}
                            </span>
                            @if($inventoryItem->expiry_status && $inventoryItem->expiry_status !== 'good')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $inventoryItem->expiry_status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $inventoryItem->expiry_status)) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                            <p class="text-gray-900 font-medium">{{ $inventoryItem->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <p class="text-gray-900 font-medium">{{ $inventoryItem->sku }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <p class="text-gray-900">{{ ucfirst($inventoryItem->category) }}</p>
                        </div>

                        @if($inventoryItem->brand)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <p class="text-gray-900">{{ $inventoryItem->brand }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                            <p class="text-gray-900 font-medium text-lg">{{ $inventoryItem->formatted_cost_price }}</p>
                        </div>

                        @if($inventoryItem->selling_price)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                            <p class="text-gray-900 font-medium text-lg">{{ $inventoryItem->formatted_selling_price }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit (Packaging)</label>
                            <p class="text-gray-900 font-medium">{{ ucfirst($inventoryItem->unit) }}</p>
                        </div>

                        @if($inventoryItem->formatted_content_per_unit)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Content per Unit</label>
                            <p class="text-gray-900 font-medium">{{ $inventoryItem->formatted_content_per_unit }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                            <p class="text-gray-900 font-medium text-lg">
                                @if($inventoryItem->usesMlTracking())
                                    {{ $inventoryItem->full_containers }} {{ $inventoryItem->unit }}
                                    @if($inventoryItem->remaining_ml > 0)
                                        <span class="text-gray-600 text-base">+ {{ number_format($inventoryItem->remaining_ml, 2) }} mL</span>
                                    @endif
                                    <div class="text-sm text-gray-600 mt-1">
                                        Total Volume: {{ number_format($inventoryItem->total_volume_ml, 2) }} mL
                                        @if($inventoryItem->remaining_volume_per_container !== null)
                                            <br>Remaining in Current Container: {{ number_format($inventoryItem->remaining_volume_per_container, 2) }} mL
                                        @endif
                                    </div>
                                @else
                                    {{ $inventoryItem->current_stock }} {{ $inventoryItem->unit }}
                                    @if($inventoryItem->formatted_content_per_unit)
                                        <span class="text-gray-600 text-base">({{ $inventoryItem->formatted_content_per_unit }} each)</span>
                                    @endif
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Levels</label>
                            <div class="text-sm text-gray-600">
                                <p>Minimum: {{ $inventoryItem->minimum_stock }} {{ $inventoryItem->unit }}</p>
                                @if($inventoryItem->maximum_stock)
                                <p>Maximum: {{ $inventoryItem->maximum_stock }} {{ $inventoryItem->unit }}</p>
                                @endif
                            </div>
                        </div>

                        @if($inventoryItem->supplier)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                            <p class="text-gray-900">{{ $inventoryItem->supplier }}</p>
                        </div>
                        @endif

                        @if($inventoryItem->expiry_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                            <p class="text-gray-900">{{ $inventoryItem->expiry_date->format('M d, Y') }}</p>
                        </div>
                        @endif

                        @if($inventoryItem->storage_location)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Storage Location</label>
                            <p class="text-gray-900">{{ $inventoryItem->storage_location }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $inventoryItem->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $inventoryItem->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Added On</label>
                            <p class="text-gray-900">{{ $inventoryItem->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>

                    @if($inventoryItem->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $inventoryItem->description }}</p>
                    </div>
                    @endif

                    @if($inventoryItem->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <p class="text-gray-900 bg-yellow-50 p-3 rounded-lg">{{ $inventoryItem->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Stock Value Information -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock Value</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">₱{{ number_format($inventoryItem->current_stock * $inventoryItem->cost_price, 2) }}</div>
                            <div class="text-sm text-blue-600">Total Cost Value</div>
                        </div>
                        @if($inventoryItem->selling_price)
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">₱{{ number_format($inventoryItem->current_stock * $inventoryItem->selling_price, 2) }}</div>
                            <div class="text-sm text-green-600">Total Selling Value</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">₱{{ number_format(($inventoryItem->current_stock * $inventoryItem->selling_price) - ($inventoryItem->current_stock * $inventoryItem->cost_price), 2) }}</div>
                            <div class="text-sm text-purple-600">Potential Profit</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('admin.inventory.edit', $inventoryItem) }}" 
                           class="w-full bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Item
                        </a>

                        <button onclick="openStockModal()" 
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1h4zM3 7v10a2 2 0 002 2h10a2 2 0 002-2V7H3z"></path>
                            </svg>
                            Update Stock
                        </button>

                        <a href="{{ route('admin.inventory.index') }}" 
                           class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Inventory
                        </a>
                    </div>

                    <!-- Stock Status Alerts -->
                    @if($inventoryItem->needsReordering())
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-yellow-800">Reorder Alert</h4>
                                    <p class="text-sm text-yellow-700">Stock is below minimum level</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($inventoryItem->isExpiringSoon())
                    <div class="mt-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-red-800">Expiry Alert</h4>
                                    <p class="text-sm text-red-700">
                                        @if($inventoryItem->expiry_status === 'expired')
                                            Item has expired
                                        @else
                                            Expires {{ $inventoryItem->expiry_date->format('M d, Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Modal -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Stock</h3>
                <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="stockForm">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock: <span class="font-semibold">{{ $inventoryItem->current_stock }} {{ $inventoryItem->unit }}</span></label>
                </div>
                <div class="mb-4">
                    <label for="stockType" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select name="type" id="stockType" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        <option value="add">Add Stock</option>
                        <option value="subtract">Remove Stock</option>
                        <option value="set">Set Stock Level</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" min="0" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Reason for stock update..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStockModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openStockModal() {
        document.getElementById('stockModal').classList.remove('hidden');
    }

    function closeStockModal() {
        document.getElementById('stockModal').classList.add('hidden');
        document.getElementById('stockForm').reset();
    }

    // Handle stock update form submission
    document.getElementById('stockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route('admin.inventory.update-stock', $inventoryItem) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating stock: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating stock. Please try again.');
        });
    });

    // Close modal when clicking outside
    document.getElementById('stockModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeStockModal();
        }
    });
</script>
</x-app-layout>
