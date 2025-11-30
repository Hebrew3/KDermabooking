<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
                    <p class="text-gray-600 mt-2">Track and manage all products and supplies</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.inventory-activity-log.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Activity Log
                    </a>
                    <a href="{{ route('admin.inventory.create') }}" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Items</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_items'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['out_of_stock'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Low Stock</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['low_stock'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Expiring Soon</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['expiring_soon'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Value</p>
                        <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($stats['total_value'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-wrap gap-4 items-end">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, brand..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>

                <!-- Category Filter -->
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All Categories</option>
                        <option value="Treatment Products" {{ request('category') == 'Treatment Products' ? 'selected' : '' }}>Treatment Products</option>
                        <option value="Aftercare Products" {{ request('category') == 'Aftercare Products' ? 'selected' : '' }}>Aftercare Products</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All Status</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                    </select>
                </div>

                <!-- Active Status Filter -->
                <div class="min-w-[120px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Active Status</label>
                    <select name="active_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All</option>
                        <option value="active" {{ request('active_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('active_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.inventory.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Inventory Items</h2>
                <span class="text-sm text-gray-600">Showing {{ $items->total() }} item(s)</span>
            </div>

            @if($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    @if($item->brand)
                                    <div class="text-sm text-gray-500">{{ $item->brand }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst($item->category) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($item->usesMlTracking())
                                        {{ $item->full_containers }} {{ $item->unit }}
                                        @if($item->remaining_ml > 0)
                                            <span class="text-gray-500">+ {{ number_format($item->remaining_ml, 2) }} mL</span>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            Total: {{ number_format($item->total_volume_ml, 2) }} mL
                                        </div>
                                    @else
                                        {{ $item->current_stock }} {{ $item->unit }}
                                        @if($item->formatted_content_per_unit)
                                            <span class="text-gray-500">({{ $item->formatted_content_per_unit }} each)</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">Min: {{ $item->minimum_stock }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->formatted_cost_price }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->stock_status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->stock_status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Icon -->
                                    <a href="{{ route('admin.inventory.show', $item) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 transition-colors duration-200" 
                                       title="View Item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Edit Icon -->
                                    <a href="{{ route('admin.inventory.edit', $item) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-pink-100 hover:bg-pink-200 text-pink-600 hover:text-pink-700 transition-colors duration-200" 
                                       title="Edit Item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Toggle Status Icon -->
                                    <form method="POST" action="{{ route('admin.inventory.toggle-status', $item) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $item->is_active ? 'bg-orange-100 hover:bg-orange-200 text-orange-600 hover:text-orange-700' : 'bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700' }} transition-colors duration-200" 
                                                title="{{ $item->is_active ? 'Deactivate Item' : 'Activate Item' }}"
                                                onclick="return confirm('Are you sure you want to {{ $item->is_active ? 'deactivate' : 'activate' }} this item?')">
                                            @if($item->is_active)
                                                <!-- Deactivate Icon (Power Off) -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @else
                                                <!-- Activate Icon (Check Circle) -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->withQueryString()->links() }}
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No inventory items found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first inventory item.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.inventory.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Item
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only show alert if user clicked the inventory link from sidebar (has show_alert parameter)
    const urlParams = new URLSearchParams(window.location.search);
    const showAlert = urlParams.get('show_alert') === '1';
    
    if (!showAlert) {
        return; // Don't show alert if not from sidebar click
    }
    
    @if($outOfStockItems->count() > 0 || $lowStockItems->count() > 0)
        let alertTitle = '';
        let alertHtml = '';
        let alertIcon = 'warning';
        
        @if($outOfStockItems->count() > 0)
            alertTitle = '⚠️ Out of Stock Alert';
            alertHtml = `
                <div class="text-left">
                    <p class="mb-3 text-gray-700"><strong>{{ $outOfStockItems->count() }} item(s) are out of stock:</strong></p>
                    <div class="max-h-60 overflow-y-auto mb-3">
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            @foreach($outOfStockItems->take(10) as $item)
                                <li><strong>{{ $item->name }}</strong> (SKU: {{ $item->sku }})</li>
                            @endforeach
                            @if($outOfStockItems->count() > 10)
                                <li class="text-gray-500">... and {{ $outOfStockItems->count() - 10 }} more</li>
                            @endif
                        </ul>
                    </div>
                    @if($lowStockItems->count() > 0)
                        <p class="mt-4 mb-2 text-gray-700"><strong>{{ $lowStockItems->count() }} item(s) are running low:</strong></p>
                        <div class="max-h-40 overflow-y-auto">
                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                                @foreach($lowStockItems->take(5) as $item)
                                    <li><strong>{{ $item->name }}</strong> - Current: {{ $item->current_stock }}, Min: {{ $item->minimum_stock }}</li>
                                @endforeach
                                @if($lowStockItems->count() > 5)
                                    <li class="text-gray-500">... and {{ $lowStockItems->count() - 5 }} more</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <a href="{{ route('admin.inventory.index', ['status' => 'out_of_stock']) }}" class="text-pink-600 hover:text-pink-800 font-semibold text-sm">
                            View All Out of Stock Items →
                        </a>
                    </div>
                </div>
            `;
            alertIcon = 'error';
        @elseif($lowStockItems->count() > 0)
            alertTitle = '⚠️ Low Stock Alert';
            alertHtml = `
                <div class="text-left">
                    <p class="mb-3 text-gray-700"><strong>{{ $lowStockItems->count() }} item(s) are running low on stock:</strong></p>
                    <div class="max-h-60 overflow-y-auto mb-3">
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            @foreach($lowStockItems->take(10) as $item)
                                <li>
                                    <strong>{{ $item->name }}</strong> (SKU: {{ $item->sku }})<br>
                                    <span class="text-xs text-gray-500 ml-4">Current: {{ $item->current_stock }} {{ $item->unit }}, Minimum: {{ $item->minimum_stock }} {{ $item->unit }}</span>
                                </li>
                            @endforeach
                            @if($lowStockItems->count() > 10)
                                <li class="text-gray-500">... and {{ $lowStockItems->count() - 10 }} more</li>
                            @endif
                        </ul>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" class="text-pink-600 hover:text-pink-800 font-semibold text-sm">
                            View All Low Stock Items →
                        </a>
                    </div>
                </div>
            `;
            alertIcon = 'warning';
        @endif
        
        Swal.fire({
            title: alertTitle,
            html: alertHtml,
            icon: alertIcon,
            width: '600px',
            confirmButtonText: 'OK',
            confirmButtonColor: '#EC4899',
            allowOutsideClick: true,
            allowEscapeKey: true,
            showCloseButton: true,
            customClass: {
                popup: 'text-left'
            }
        });
        
        // Remove the show_alert parameter from URL after showing alert
        const url = new URL(window.location);
        url.searchParams.delete('show_alert');
        window.history.replaceState({}, '', url);
    @endif
});
</script>
</x-app-layout>
