<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Slip #{{ $sale->invoice_no }}</title>
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
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .fs-5 { font-size: 16px; }
        .fs-6 { font-size: 14px; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .my-1 { margin-top: 4px; margin-bottom: 4px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .d-flex { display: flex; justify-content: space-between; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .table-items th, .table-items td { padding: 3px 0; font-size: 12px; }
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
<body>

    <div class="no-print" style="margin-bottom: 12px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 4px;">Print Delivery Slip</button>
    </div>

    <!-- Header -->
    <div class="text-center">
        <div class="fw-bold fs-5">{{ $branch->name ?? 'RESTAURANT' }}</div>
        <div style="font-size: 11px;">{{ $branch->address ?? '' }}</div>
        <div style="font-size: 11px;">Ph: {{ $branch->number ?? '' }}</div>
        <div class="border-top border-bottom py-1 my-1 fw-bold fs-6">
            *** DELIVERY SLIP / CHALLAN ***
        </div>
    </div>

    <!-- Order Info -->
    <div class="my-1">
        <div class="d-flex">
            <span>Invoice: <b>#{{ $sale->invoice_no }}</b></span>
            <span>{{ $sale->created_at ? $sale->created_at->format('d-M-y h:i A') : '' }}</span>
        </div>
    </div>

    <!-- Customer Details -->
    <div class="border-top py-1 my-1">
        <div style="font-size: 11px; font-weight: bold; text-transform: uppercase;">Customer Details:</div>
        <div><b>Name:</b> {{ $sale->walkin_name ?: ($sale->customer_relation->customer_name ?? 'Walk-in Customer') }}</div>
        <div><b>Phone:</b> {{ $sale->rider_phone ?: ($sale->customer_relation->phone_no ?? '&mdash;') }}</div>
        <div><b>Address:</b> {{ $sale->delivery_address ?: 'Counter Delivery' }}</div>
    </div>

    <!-- Rider Details -->
    <div class="border-top py-1 my-1">
        <div style="font-size: 11px; font-weight: bold; text-transform: uppercase;">Rider Information:</div>
        <div class="d-flex">
            <span><b>Rider:</b> {{ $sale->rider->name ?? ($sale->rider_name ?? 'Assigned Rider') }}</span>
            <span>{{ $sale->rider->phone ?? '' }}</span>
        </div>
        @if(!empty($sale->rider->vehicle_no))
            <div><b>Bike:</b> {{ $sale->rider->vehicle_no }}</div>
        @endif
    </div>

    <!-- Items -->
    <table class="table-items border-top border-bottom">
        <thead>
            <tr>
                <th style="text-align: left;">Item</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $it)
                @php
                    $qty = $it->qty > 0 ? $it->qty : ($it->total_pieces > 0 ? $it->total_pieces : 1);
                    $name = $it->product_name ?: ($it->product->item_name ?? 'Item');
                @endphp
                <tr>
                    <td>{{ $name }}</td>
                    <td style="text-align: center;">{{ $qty }}</td>
                    <td style="text-align: right;">{{ number_format($it->total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="my-1" style="font-size: 12px;">
        <div class="d-flex">
            <span>Items Subtotal:</span>
            <span>Rs. {{ number_format($sale->total_bill_amount - ($sale->delivery_charges ?? 0), 0) }}</span>
        </div>
        @if(($sale->delivery_charges ?? 0) > 0)
            <div class="d-flex">
                <span>Delivery Charges:</span>
                <span>Rs. {{ number_format($sale->delivery_charges, 0) }}</span>
            </div>
        @endif
    </div>

    <!-- COD Box -->
    <div class="cod-box">
        <div style="font-size: 11px; font-weight: bold; text-transform: uppercase;">Cash On Delivery (COD)</div>
        <div class="fw-bold fs-5">Rs. {{ number_format($sale->total_bill_amount, 0) }}</div>
        <div style="font-size: 10px; color: #555;">Please collect exact amount</div>
    </div>

    @if($sale->order_notes)
        <div class="border-top py-1 my-1" style="font-size: 11px;">
            <b>Instructions:</b> {{ $sale->order_notes }}
        </div>
    @endif

    <div class="text-center border-top pt-2 my-1" style="font-size: 10px;">
        <div>Thank You For Ordering!</div>
        <div style="margin-top: 15px; border-bottom: 1px solid #000; width: 60%; margin-left: auto; margin-right: auto;"></div>
        <div style="margin-top: 2px;">Customer Signature</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
