<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT - {{ $sale->invoice_no }} ({{ strtoupper($sale->order_type ?? 'Order') }})</title>
    <style>
        @media print {
            @page {
                margin: 2mm 3mm 2mm 2mm !important;
            }
            body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
            }
            .no-print {
                display: none !important;
            }
            .kot-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 4px 0 0 !important;
                width: 96% !important;
                max-width: 96% !important;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Helvetica Neue', Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 10px 0;
            color: #000;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
        }

        .kot-container {
            width: 100%;
            max-width: 76mm;
            margin: 0 auto;
            background: #fff;
            padding: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #ddd;
        }

        .kot-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .restaurant-title {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .kot-badge {
            display: inline-block;
            background: #000;
            color: #fff;
            font-size: 14px;
            font-weight: 900;
            padding: 2px 10px;
            border-radius: 4px;
            margin: 4px 0;
            letter-spacing: 1px;
        }

        .order-highlight {
            border: 2px solid #000;
            border-radius: 6px;
            padding: 6px 4px;
            margin: 6px 0;
            text-align: center;
            background-color: #f8fafc;
        }

        .order-type-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-target-id {
            font-size: 22px;
            font-weight: 900;
            margin-top: 2px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .kot-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        .kot-table th {
            border-bottom: 1.5px solid #000;
            padding: 4px 0;
            font-size: 12px;
            text-transform: uppercase;
        }

        .kot-table td {
            padding: 6px 0;
            border-bottom: 1px dashed #bbb;
            vertical-align: top;
        }

        .item-name {
            font-size: 14px;
            font-weight: 800;
            color: #000;
        }

        .item-note {
            display: block;
            font-size: 11px;
            font-style: italic;
            font-weight: 700;
            background: #eee;
            padding: 2px 4px;
            border-radius: 3px;
            margin-top: 3px;
            border-left: 3px solid #000;
        }

        .item-qty {
            font-size: 18px;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .order-notes-box {
            border: 1px solid #000;
            padding: 4px 6px;
            margin-top: 6px;
            background-color: #fafafa;
            font-size: 11px;
        }

        .kot-footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1.5px solid #000;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .print-controls {
            width: 76mm;
            margin: 0 auto 12px auto;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 12px;
        }

        .btn-print { background: #2563eb; color: #fff; }
        .btn-close { background: #64748b; color: #fff; }
    </style>
</head>
<body>

    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-print">Print KOT</button>
        <button onclick="window.close()" class="btn btn-close">Close</button>
    </div>

    <div class="kot-container">
        @php
            $branchId = $sale->branch_id ?? null;
            $branchName = \App\Models\Branch::find($branchId)->name ?? \App\Models\Setting::get('company_name', 'Restaurant', $branchId);
        @endphp

        <div class="kot-header">
            <div class="restaurant-title">{{ $branchName }}</div>
            <div class="kot-badge">KITCHEN TICKET (KOT)</div>
        </div>

        <!-- Highlight Table or Token -->
        <div class="order-highlight">
            @if(($sale->order_type ?? '') === 'dine_in')
                <div class="order-type-title">DINE-IN ORDER</div>
                <div class="order-target-id">TABLE #{{ $sale->table_no ?? '-' }}</div>
            @elseif(($sale->order_type ?? '') === 'takeaway')
                <div class="order-type-title">TAKEAWAY ORDER</div>
                <div class="order-target-id">TOKEN #{{ $sale->token_no ?? '-' }}</div>
            @else
                <div class="order-type-title">DELIVERY ORDER</div>
                <div class="order-target-id">{{ $sale->walkin_name ?? 'Customer' }}</div>
                @if($sale->rider_name)
                    <div style="font-size: 11px; margin-top: 2px;">Rider: {{ $sale->rider_name }}</div>
                @endif
            @endif
        </div>

        <div class="meta-info">
            <span><b>Order #:</b> {{ $sale->invoice_no }}</span>
            <span><b>Time:</b> {{ now()->format('h:i A') }}</span>
        </div>
        <div class="meta-info">
            <span><b>Date:</b> {{ now()->format('d/m/Y') }}</span>
            <span><b>Server:</b> {{ $sale->user->name ?? 'Cashier' }}</span>
        </div>

        <div class="divider"></div>

        <table class="kot-table">
            <thead>
                <tr>
                    <th style="text-align: left;">ITEM</th>
                    <th style="text-align: right; width: 45px;">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saleItems as $item)
                    @php
                        $isArr = is_array($item);
                        $qty = (int) ($isArr ? ($item['qty'] ?? $item['total_pieces'] ?? 1) : ($item->qty ?: ($item->total_pieces ?: 1)));
                        $displayName = $isArr ? ($item['item_name'] ?? 'Item') : ($item->product_name ?? ($item->product->item_name ?? 'Item'));
                        $itemNotes = $isArr ? ($item['item_notes'] ?? '') : ($item->item_notes ?? '');

                        if ($isArr) {
                            $size = $item['size_val'] ?? '';
                            if ($size && $size !== '-' && !str_contains($displayName, $size)) {
                                $displayName .= ' (' . $size . ')';
                            }
                        } else {
                            if (!empty($item->color)) {
                                $decoded = base64_decode($item->color, true);
                                $vData = $decoded !== false ? json_decode($decoded, true) : json_decode($item->color, true);
                                if (is_array($vData)) {
                                    if (!empty($vData['name'])) {
                                        $displayName = $vData['name'];
                                    } elseif (!empty($vData['size']) && $vData['size'] !== '-') {
                                        $displayName .= ' (' . $vData['size'] . ')';
                                    }
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="item-name">{{ $displayName }}</div>
                            @if(!empty($itemNotes))
                                <span class="item-note">Note: {{ $itemNotes }}</span>
                            @endif
                        </td>
                        <td class="item-qty">
                            {{ $qty }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!empty($sale->order_notes))
            <div class="order-notes-box">
                <b>SPECIAL INSTRUCTIONS:</b><br>
                {{ $sale->order_notes }}
            </div>
        @endif

        <div class="kot-footer">
            <div>*** PLEASE PREPARE FRESH ***</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });

        window.onafterprint = function() {
            if (window.opener) {
                setTimeout(function() {
                    try { window.close(); } catch(e){}
                }, 800);
            }
        };
    </script>
</body>
</html>
