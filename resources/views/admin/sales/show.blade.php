<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.sales.index') }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Sale Details</h1>
                        <p class="text-gray-600 mt-2">{{ $sale->sale_number }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.pos.receipt', $sale) }}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        View Receipt
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Sale Information -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Sale Information</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sale Number</label>
                            <p class="text-gray-900 font-medium">{{ $sale->sale_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                            <p class="text-gray-900">{{ $sale->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <p class="text-gray-900">{{ $sale->client ? $sale->client->name : 'Walk-in Customer' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Staff</label>
                            <p class="text-gray-900">{{ $sale->user ? $sale->user->name : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <p class="text-gray-900 capitalize">{{ $sale->payment_method }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                                @if($sale->status === 'completed') bg-green-100 text-green-800
                                @elseif($sale->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Items Sold</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->item_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->item_sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₱{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">₱{{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        @if($sale->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount:</span>
                            <span class="font-medium text-red-600">-₱{{ number_format($sale->discount, 2) }}</span>
                        </div>
                        @endif
                        @if($sale->tax > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-medium">₱{{ number_format($sale->tax, 2) }}</span>
                        </div>
                        @endif
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-pink-600">₱{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Amount Paid:</span>
                                <span class="font-medium">₱{{ number_format($sale->amount_paid, 2) }}</span>
                            </div>
                            @if($sale->change > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Change:</span>
                                <span class="font-medium">₱{{ number_format($sale->change, 2) }}</span>
                            </div>
                            @endif
                        </div>
                        @if($sale->notes)
                        <div class="border-t border-gray-200 pt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <p class="text-sm text-gray-600">{{ $sale->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

