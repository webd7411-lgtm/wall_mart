<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Order Slip #{{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 13px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px 14px;
            width: 80mm;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .fs-5 { font-size: 16px; }
        .fs-6 { font-size: 14px; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .my-1 { margin-top: 4px; margin-bottom: 4px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .d-flex { display: flex; justify-content: space-between; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .table-items th, .table-items td { padding: 4px 0; font-size: 12px; }
        .cod-box {
            border: 2px solid #000;
            padding: 6px 8px;
            margin: 8px 0;
            text-align: center;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 4px;">Print Delivery Slip</button>
    </div>

    <!-- Header -->
    <div class="text-center">
        <div class="fw-bold fs-5">{{ $companyName }}</div>
        <div style="font-size: 11px;">{{ $address }}</div>
        <div style="font-size: 11px;">Tel: {{ $phone }}</div>
        <div class="border-top border-bottom py-1 my-1 fw-bold fs-6">
            *** ONLINE ORDER DELIVERY SLIP ***
        </div>
    </div>

    <!-- Order Metadata -->
    <div style="margin-top: 6px;">
        <div class="d-flex">
            <span>Order #: <strong>{{ $order->order_number }}</strong></span>
            <span>Date: {{ $order->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="d-flex">
            <span>Time: {{ $order->created_at->format('h:i A') }}</span>
            <span>Type: <strong>DELIVERY</strong></span>
        </div>
    </div>

    <!-- Customer Details -->
    <div class="border-top my-1 py-1">
        <div>Customer: <strong>{{ $order->shipping_name }}</strong></div>
        <div>Phone: <strong>{{ $order->shipping_phone }}</strong></div>
        <div>Address: <strong>{{ $order->shipping_address }}</strong></div>
        @if($order->order_notes)
        <div style="font-style: italic; margin-top: 2px;">Note: {{ $order->order_notes }}</div>
        @endif
        @if(!empty($order->rider_name))
        <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dotted #666;">
            🛵 Assigned Rider: <strong>{{ $order->rider_name }}</strong>
            @if(!empty($order->rider_phone)) ({{ $order->rider_phone }}) @endif
        </div>
        @endif
    </div>

    <!-- Items Table -->
    <div class="border-top border-bottom my-1">
        <table class="table-items">
            <thead>
                <tr>
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: center; width: 35px;">Qty</th>
                    <th style="text-align: right; width: 55px;">Rate</th>
                    <th style="text-align: right; width: 60px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price) }}</td>
                    <td class="text-right">{{ number_format($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div>
        <div class="d-flex">
            <span>Subtotal:</span>
            <span>Rs. {{ number_format($order->subtotal) }}</span>
        </div>
        <div class="d-flex">
            <span>Delivery Fee:</span>
            <span>{{ $order->delivery_charges > 0 ? 'Rs. ' . number_format($order->delivery_charges) : 'FREE' }}</span>
        </div>
        <div class="d-flex fw-bold fs-6 border-top my-1 py-1">
            <span>NET PAYABLE:</span>
            <span>Rs. {{ number_format($order->total) }}</span>
        </div>
    </div>

    <!-- Cash on delivery banner -->
    <div class="cod-box">
        <div class="fw-bold fs-6">
            {{ strtoupper($order->payment_method ?: 'CASH ON DELIVERY') }}
        </div>
        <div style="font-size: 11px;">
            COLLECT CASH: Rs. {{ number_format($order->total) }}
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center" style="font-size: 11px; margin-top: 8px;">
        <div>Thank you for ordering with us!</div>
        <div class="fw-bold">Munch Fried Chicken</div>
    </div>

</body>
</html>
