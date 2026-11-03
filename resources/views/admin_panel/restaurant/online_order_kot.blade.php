<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT - #{{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 14px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px 14px;
            width: 80mm;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .fs-4 { font-size: 18px; }
        .fs-5 { font-size: 16px; }
        .border-top { border-top: 2px dashed #000; }
        .border-bottom { border-bottom: 2px dashed #000; }
        .my-1 { margin-top: 6px; margin-bottom: 6px; }
        .py-1 { padding-top: 6px; padding-bottom: 6px; }
        .d-flex { display: flex; justify-content: space-between; }
        .kot-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .kot-table th, .kot-table td { padding: 6px 0; font-size: 14px; }
        .kot-badge {
            background: #000;
            color: #fff;
            padding: 4px 8px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; cursor: pointer; background: #000; color: #fff; border: none; border-radius: 4px;">Print KOT</button>
    </div>

    <!-- KOT Header -->
    <div class="text-center">
        <div class="kot-badge">*** KITCHEN ORDER TICKET (KOT) ***</div>
        <div class="fs-4 fw-bold" style="margin-top: 6px;">ONLINE DELIVERY</div>
        <div class="fs-5 fw-bold">ORDER #{{ $order->order_number }}</div>
    </div>

    <div class="border-top my-1 py-1" style="font-size: 12px;">
        <div class="d-flex">
            <span>Date: {{ $order->created_at->format('d/m/Y') }}</span>
            <span>Time: <strong>{{ $order->created_at->format('h:i A') }}</strong></span>
        </div>
        <div>Customer: {{ $order->shipping_name }}</div>
        @if(!empty($order->rider_name))
        <div>Rider: <strong>{{ $order->rider_name }}</strong></div>
        @endif
    </div>

    <!-- Items to Cook -->
    <div class="border-top border-bottom my-1 py-1">
        <table class="kot-table">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="text-align: left; width: 75%;">ITEM NAME</th>
                    <th style="text-align: right; width: 25%;">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr style="border-bottom: 1px dotted #888;">
                    <td class="fw-bold" style="font-size: 15px; padding: 6px 0;">
                        {{ $item->product_name }}
                    </td>
                    <td style="text-align: right; font-size: 18px; font-weight: bold;">
                        [ {{ $item->quantity }} ]
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($order->order_notes)
    <!-- Special Kitchen Instruction Notes -->
    <div style="margin-top: 8px; padding: 6px; border: 2px solid #000; font-weight: bold;">
        <div style="font-size: 11px; text-decoration: underline;">SPECIAL COOKING NOTE:</div>
        <div style="font-size: 13px; color: #000;">{{ $order->order_notes }}</div>
    </div>
    @endif

    <div class="text-center" style="font-size: 11px; margin-top: 12px;">
        <div>*** PLEASE COOK FRESH &amp; PACK HOT ***</div>
    </div>

</body>
</html>
