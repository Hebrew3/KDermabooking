<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->sale_number }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }
        .total-row {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 10px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 style="margin: 0; font-size: 18px;">K-DERMA</h2>
        <p style="margin: 5px 0; font-size: 12px;">Point of Sale Receipt</p>
    </div>

    <div style="font-size: 11px; margin-bottom: 10px;">
        <div>Sale #: {{ $sale->sale_number }}</div>
        <div>Date: {{ $sale->created_at->format('M d, Y h:i A') }}</div>
        @if($sale->client)
        <div>Customer: {{ $sale->client->name }}</div>
        @endif
        @if($sale->user)
        <div>Staff: {{ $sale->user->name }}</div>
        @endif
    </div>

    <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin: 10px 0;">
        @foreach($sale->items as $item)
        <div class="item-row">
            <div style="flex: 1;">
                <div style="font-weight: bold;">{{ $item->item_name }}</div>
                <div style="font-size: 10px;">{{ $item->item_sku }}</div>
            </div>
            <div style="text-align: right; margin-left: 10px;">
                <div>{{ $item->quantity }} x ₱{{ number_format($item->unit_price, 2) }}</div>
                <div style="font-weight: bold;">₱{{ number_format($item->total, 2) }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="font-size: 12px;">
        <div class="item-row">
            <span>Subtotal:</span>
            <span>₱{{ number_format($sale->subtotal, 2) }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="item-row">
            <span>Discount:</span>
            <span>-₱{{ number_format($sale->discount, 2) }}</span>
        </div>
        @endif
        @if($sale->tax > 0)
        <div class="item-row">
            <span>Tax:</span>
            <span>₱{{ number_format($sale->tax, 2) }}</span>
        </div>
        @endif
        <div class="item-row total-row">
            <span>TOTAL:</span>
            <span>₱{{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="item-row">
            <span>Payment:</span>
            <span>{{ strtoupper($sale->payment_method) }}</span>
        </div>
        <div class="item-row">
            <span>Paid:</span>
            <span>₱{{ number_format($sale->amount_paid, 2) }}</span>
        </div>
        @if($sale->change > 0)
        <div class="item-row">
            <span>Change:</span>
            <span>₱{{ number_format($sale->change, 2) }}</span>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>{{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #ec4899; color: white; border: none; border-radius: 5px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>
</body>
</html>

