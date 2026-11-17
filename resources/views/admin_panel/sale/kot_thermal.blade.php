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
            html, body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000000 !important;
                font-weight: 800 !important;
                -webkit-text-stroke: 0.15px #000;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 10px 0;
            color: #000000;
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            -webkit-font-smoothing: antialiased;
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
            border-bottom: 2px solid #000;
            padding: 4px 0;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000;
        }

        .kot-table td {
            padding: 6px 0;
            border-bottom: 1.5px dashed #000;
            vertical-align: top;
            color: #000;
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
                <div class="order-type-title" style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                    <svg width="15" height="15" viewBox="0 0 416 512" fill="#000" style="vertical-align: middle;"><path d="M207.9 15.2c.8 4.7 16.1 94.5 16.1 128.8 0 52.3-27.8 89.6-68.9 97.2V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V241.2C49.9 233.6 22.1 196.3 22.1 144c0-34.3 15.3-124.1 16.1-128.8.6-3.8 3.9-6.6 7.8-6.6h17.9c4 0 7.4 3 7.8 7 1.8 17.7 8.1 79.5 13.9 104.4 1.1 4.7 5.2 8 10 8s8.9-3.3 10-8C111.4 95.1 117.7 33.3 119.5 15.6c.4-4 3.8-7 7.8-7h15.8c4 0 7.4 3 7.8 7 1.8 17.7 8.1 79.5 13.9 104.4 1.1 4.7 5.2 8 10 8s8.9-3.3 10-8c5.8-24.9 12.1-86.7 13.9-104.4.4-4 3.8-7 7.8-7h17.9c3.9 0 7.2 2.8 7.8 6.6zM271.7 16c21.8 0 39.5 17.7 39.5 39.5V288h-32c-17.7 0-32 14.3-32 32v160c0 17.7 14.3 32 32 32h32c17.7 0 32-14.3 32-32V55.5C343.2 24.9 311.2 0 271.7 0h-16c-8.8 0-16 7.2-16 16s7.2 16 16 16h16z"/></svg>
                    <span>DINE-IN ORDER</span>
                </div>
                <div class="order-target-id">TABLE #{{ $sale->table_no ?? '-' }}</div>
            @elseif(($sale->order_type ?? '') === 'takeaway')
                <div class="order-type-title" style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                    <svg width="15" height="15" viewBox="0 0 448 512" fill="#000" style="vertical-align: middle;"><path d="M352 160V128C352 57.3 294.7 0 224 0S96 57.3 96 128v32H16c-8.8 0-16 7.2-16 16v288c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V176c0-8.8-7.2-16-16-16h-80zm-192-32c0-35.3 28.7-64 64-64s64 28.7 64 64v32H160v-32zm224 320H64V224h32v32c0 8.8 7.2 16 16 16s16-7.2 16-16v-32h192v32c0 8.8 7.2 16 16 16s16-7.2 16-16v-32h32v224z"/></svg>
                    <span>TAKEAWAY ORDER</span>
                </div>
                <div class="order-target-id">TOKEN #{{ $sale->token_no ?? '-' }}</div>
            @else
                <div class="order-type-title" style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                    <svg width="20" height="15" viewBox="0 0 640 512" fill="#000" style="vertical-align: middle;"><path d="M575.8 245.8l-40.3-80.6A48 48 0 0 0 492.6 138H448v-26c0-14.3-11.7-26-26-26h-36c-14.3 0-26 11.7-26 26v26h-24c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24h18.2l-28.7 57.4C307 274.6 279.7 272 256 272c-20.7 0-40.5 4.1-59 11.1L244 192h52c13.3 0 24-10.7 24-24s-10.7-24-24-24H216c-8.9 0-17.1 5-21.2 12.8L151.2 240H96c-53 0-96 43-96 96 0 48.9 36.7 89.2 84.1 95.3-2.7 8-4.1 16.5-4.1 25.3 0 44.2 35.8 80 80 80s80-35.8 80-80c0-9.6-1.8-18.9-5-27.5 1.7-.3 3.3-.8 5-1.2 24.3-6.1 47.1-15.6 67.8-27.9l40.7 40.7c-5.8 10.8-9.5 22.9-9.5 35.9 0 44.2 35.8 80 80 80s80-35.8 80-80c0-14.7-4-28.4-11-40.2l-47.5-80.8 45.4-90.8h69.1l30.6 61.2c5.8 11.6 17.6 18.8 30.6 18.8h8c17.7 0 32-14.3 32-32 0-9.2-3.9-17.5-10.2-23.4zM160 496c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z"/></svg>
                    <span>DELIVERY ORDER</span>
                </div>
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
