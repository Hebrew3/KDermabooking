<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->sale_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="text-center border-b border-gray-200 pb-4 mb-4">
            <h1 class="text-2xl font-bold text-gray-900">K-Derma</h1>
            <p class="text-sm text-gray-600">Point of Sale Receipt</p>
        </div>

        <!-- Sale Info -->
        <div class="mb-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Sale #:</span>
                <span class="font-medium">{{ $sale->sale_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Date:</span>
                <span class="font-medium">{{ $sale->created_at->format('M d, Y h:i A') }}</span>
            </div>
            @if($sale->client)
            <div class="flex justify-between">
                <span class="text-gray-600">Customer:</span>
                <span class="font-medium">{{ $sale->client->name }}</span>
            </div>
            @endif
            @if($sale->user)
            <div class="flex justify-between">
                <span class="text-gray-600">Staff:</span>
                <span class="font-medium">{{ $sale->user->name }}</span>
            </div>
            @endif
        </div>

        <!-- Items -->
        <div class="border-t border-b border-gray-200 py-4 mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 text-gray-600">Item</th>
                        <th class="text-right py-2 text-gray-600">Qty</th>
                        <th class="text-right py-2 text-gray-600">Price</th>
                        <th class="text-right py-2 text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td class="py-2">
                            <div class="font-medium">{{ $item->item_name }}</div>
                            <div class="text-xs text-gray-500">{{ $item->item_sku }}</div>
                        </td>
                        <td class="text-right py-2">{{ $item->quantity }}</td>
                        <td class="text-right py-2">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right py-2 font-medium">₱{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span>₱{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Discount:</span>
                <span>-₱{{ number_format($sale->discount, 2) }}</span>
            </div>
            @endif
            @if($sale->tax > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Tax:</span>
                <span>₱{{ number_format($sale->tax, 2) }}</span>
            </div>
            @endif
            <div class="border-t border-gray-200 pt-2 flex justify-between font-bold text-lg">
                <span>Total:</span>
                <span>₱{{ number_format($sale->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Payment Method:</span>
                <span class="font-medium">{{ ucfirst($sale->payment_method) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Amount Paid:</span>
                <span>₱{{ number_format($sale->amount_paid, 2) }}</span>
            </div>
            @if($sale->change > 0)
            <div class="flex justify-between">
                <span class="text-gray-600">Change:</span>
                <span>₱{{ number_format($sale->change, 2) }}</span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-gray-500 border-t border-gray-200 pt-4">
            <p>Thank you for your purchase!</p>
            <p class="mt-2">{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>

        <!-- Print Button -->
        <div class="mt-6 text-center">
            <button onclick="window.print()" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg">
                Print Receipt
            </button>
            <button onclick="window.close()" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                Close
            </button>
        </div>
    </div>
</body>
</html>

