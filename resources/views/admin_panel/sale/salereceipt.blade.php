<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt - {{ $sale->invoice_no }}</title>
    <style>
        @media print {
            @page {
                margin: 0 !important;
                size: auto;
            }
            * {
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            html, body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000000 !important;
                font-weight: 700 !important;
                -webkit-text-stroke: 0.12px #000;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 2mm 0 0 !important;
                width: 100% !important;
                max-width: 68mm !important;
            }
            .items-table td {
                border-bottom: 1px solid #000 !important;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 5px 0;
            color: #000000;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        .receipt-container {
            width: 100%;
            max-width: 68mm;
            margin: 0 auto;
            background: #fff;
            padding: 6px 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: #000;
        }

        h1, h2, h3, p { margin: 0; padding: 0; }

        .receipt-logo {
            text-align: center;
            margin-bottom: 6px;
        }

        .receipt-logo img {
            max-width: 140px;
            max-height: 70px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: inline-block;
            margin: 0 auto;
        }

        .company-name {
            font-size: 22px;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: #000;
        }

        .company-info {
            font-size: 12px;
            text-align: center;
            color: #000;
            font-weight: 700;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .company-info div {
            margin-bottom: 1px;
        }

        .policy-banner {
            background-color: #000;
            color: #fff;
            text-align: center;
            font-size: 11px;
            font-weight: 900;
            padding: 4px 6px;
            margin: 6px 0 2px 0;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .divider {
            border-top: 1.5px solid #000;
            margin: 5px 0;
        }

        /* Meta Grid */
        .meta-grid {
            display: flex;
            flex-direction: column;
            gap: 3px;
            margin-bottom: 2px;
            color: #000;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            color: #000;
        }

        .meta-label { font-weight: 700; color: #000; }
        .meta-value { font-weight: 800; color: #000; }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            color: #000;
            margin: 3px 0;
            table-layout: fixed;
            font-weight: 700;
        }

        .items-table th {
            border-bottom: 2px solid #000;
            padding: 4px 1px;
            text-align: left;
            font-weight: 900;
            font-size: 11px;
            text-transform: uppercase;
            color: #000;
        }

        .items-table td {
            padding: 4px 1px;
            vertical-align: top;
            border-bottom: 1px solid #000;
            color: #000;
            font-weight: 700;
        }

        .item-name {
            font-weight: 900;
            font-size: 13px;
            color: #000;
            display: block;
            line-height: 1.25;
        }

        .item-variant {
            font-size: 11px;
            color: #000;
            font-weight: 700;
            margin-top: 1px;
            display: block;
        }

        .text-end { 
            text-align: right !important; 
            white-space: nowrap !important;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.3px;
            font-weight: 800;
            color: #000;
        }
        .text-center { 
            text-align: center !important; 
            white-space: nowrap !important;
            font-weight: 800;
            color: #000;
        }

        /* Totals */
        .totals-section {
            font-size: 12px;
            margin-top: 5px;
            color: #000;
            font-weight: 700;
        }

        .tot-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 2px 0;
            color: #000;
        }

        .tot-row span:first-child { 
            font-weight: 700; 
            color: #000; 
            flex-shrink: 0;
            font-size: 12px;
        }
        .tot-row span:last-child { 
            font-weight: 800; 
            color: #000;
            white-space: nowrap !important;
            font-variant-numeric: tabular-nums;
            text-align: right;
            margin-left: 6px;
            font-size: 12.5px;
        }

        .tot-row.grand-total {
            font-size: 15px;
            font-weight: 900;
            margin: 4px 0;
            padding: 5px 0;
            border-top: 2px solid #000;
            border-bottom: 3px double #000;
            color: #000;
        }

        .tot-row.grand-total span:first-child { 
            font-weight: 900; 
            flex-shrink: 0;
            font-size: 15px;
            color: #000;
        }
        .tot-row.grand-total span:last-child { 
            font-weight: 900; 
            white-space: nowrap !important;
            font-variant-numeric: tabular-nums;
            text-align: right;
            margin-left: 6px;
            font-size: 16px;
            color: #000;
        }

        /* Balances */
        .balance-section {
            font-size: 12px;
            margin-top: 4px;
            border-top: 1.5px dashed #000;
            padding-top: 4px;
            color: #000;
            font-weight: 700;
        }

        .balance-section .tot-row.closing-bal {
            font-weight: 900;
            background-color: #f8f9fa;
            padding: 3px 2px;
            border: 2px solid #000;
            margin-top: 2px;
            color: #000;
        }

        .balance-section .tot-row.closing-bal span:first-child { font-weight: 900; flex-shrink: 0; color: #000; }
        .balance-section .tot-row.closing-bal span:last-child { 
            font-weight: 900; 
            white-space: nowrap !important;
            font-variant-numeric: tabular-nums;
            text-align: right;
            margin-left: 6px;
            color: #000;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 12px;
            color: #000;
        }

        .footer p {
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 2px;
            color: #000;
        }

        .footer .soft-credit {
            font-size: 10px;
            color: #000;
            margin-top: 3px;
            font-weight: 700;
        }

        /* Controls */
        .print-controls {
            width: 68mm;
            max-width: 100%;
            margin: 0 auto 15px auto;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary { background: #000; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
    </style>
</head>

    @php
        $fromPos = request()->query('from') === 'pos' || empty(request()->query('from'));
    @endphp

    <div class="print-controls no-print" style="max-width: 68mm; width: 100%; margin: 10px auto 14px auto; display: flex; flex-direction: column; gap: 8px;">
        @if($fromPos)
        <div id="countdownBanner" style="background: #0f172a; color: #fff; padding: 8px 12px; border-radius: 6px; font-size: 11.5px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 10px rgba(0,0,0,0.12);">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span>⚡</span>
                <span>Auto POS in <strong id="secondsLeft" style="color: #38bdf8; font-size: 13px;">3</strong>s</span>
            </div>
            <button type="button" id="btnPause" onclick="togglePause()" style="background: rgba(255,255,255,0.15); border: none; color: #fff; padding: 2px 7px; border-radius: 4px; font-size: 10.5px; cursor: pointer; font-weight: 600;">Stay Here</button>
        </div>
        @endif

        <div style="display: flex; gap: 6px;">
            <a href="javascript:void(0)" onclick="triggerPrint()" class="btn btn-primary" style="flex: 1; padding: 7px 4px; font-size: 11px; border-radius: 5px; text-decoration: none; text-align: center; background: #000; color: #fff; font-weight: 600;">🖨️ Print Receipt</a>
            <a href="{{ route('pos.index') }}" class="btn btn-secondary" style="flex: 1; padding: 7px 4px; font-size: 11px; border-radius: 5px; text-decoration: none; text-align: center; background: #2563eb; color: #fff; font-weight: 600;">⬅️ POS Next</a>
        </div>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        @php
            $branchId = $sale->branch_id ?? null;
            $companyLogo = \App\Models\Setting::get('company_logo', null, $branchId);
        @endphp
        @if(!empty($companyLogo))
            <div class="receipt-logo">
                <img src="{{ asset(ltrim($companyLogo, '/')) }}" alt="Company Logo">
            </div>
        @endif
        <div class="company-name">{{ \App\Models\Setting::get('company_name', 'Three Stars Medical', $branchId) }}</div>
        <div class="company-info">
            <div>{{ \App\Models\Setting::get('company_address', 'Hyderabad', $branchId) }}</div>
            <div>Ph: {{ \App\Models\Setting::get('company_phone', '0327-9226901', $branchId) }}</div>
        </div>
        @if(\App\Models\Setting::get('facebook_link', null, $branchId) || \App\Models\Setting::get('tiktok_link', null, $branchId) || \App\Models\Setting::get('instagram_link', null, $branchId) || \App\Models\Setting::get('website_link', null, $branchId))
        <div style="text-align: left; font-size: 10px; line-height: 1.4; word-wrap: break-word; overflow-wrap: break-word; margin-top: 4px;">
            @if(\App\Models\Setting::get('facebook_link', null, $branchId))
                <div style="margin-bottom: 2px;"><strong>Facebook:</strong> {{ \App\Models\Setting::get('facebook_link', null, $branchId) }}</div>
            @endif
            @if(\App\Models\Setting::get('tiktok_link', null, $branchId))
                <div style="margin-bottom: 2px;"><strong>TikTok:</strong> {{ \App\Models\Setting::get('tiktok_link', null, $branchId) }}</div>
            @endif
            @if(\App\Models\Setting::get('instagram_link', null, $branchId))
                <div style="margin-bottom: 2px;"><strong>Instagram:</strong> {{ \App\Models\Setting::get('instagram_link', null, $branchId) }}</div>
            @endif
            @if(\App\Models\Setting::get('website_link', null, $branchId))
                <div style="margin-bottom: 2px;"><strong>Website:</strong> {{ \App\Models\Setting::get('website_link', null, $branchId) }}</div>
            @endif
        </div>
        @endif

        <div class="divider"></div>

        <!-- Meta Grid -->
        @php
            $isWalkin = empty($sale->customer_id);
            $isDelivery = ($sale->order_type === 'delivery') || !empty($sale->delivery_address);

            // Resolve Customer details for delivery
            $custName = $sale->walkin_name ?: ($sale->customer_relation->customer_name ?? ($sale->ecommerceOrder->shipping_name ?? 'Walking Customer'));
            $custPhone = $sale->rider_phone ?? ($sale->customer_relation->mobile ?? ($sale->ecommerceOrder->shipping_phone ?? null));
            $custAddress = $sale->delivery_address ?: ($sale->customer_relation->address ?? ($sale->ecommerceOrder->shipping_address ?? null));

            // Resolve Rider details (if assigned)
            $riderObj = $sale->rider ?? null;
            $riderName = $riderObj ? $riderObj->name : ($sale->rider_name && !in_array(strtolower($sale->rider_name), ['delivery order', 'delivery']) ? $sale->rider_name : ($sale->ecommerceOrder->rider_name ?? null));
        @endphp

        @if(request()->has('provisional') || !empty($isProvisional) || ($sale->sale_status === 'booked' && $sale->order_status !== 'completed'))
            @if($isDelivery)
            <div style="border: 2px solid #000; padding: 4px; margin: 4px 0; text-align: center; border-radius: 4px; background: #fff;">
                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: #000;">*** DELIVERY BILL (CASH ON DELIVERY) ***</div>
                <div style="font-size: 11.5px; font-weight: 900; margin-top: 2px; color: #000;">COLLECT CASH: Rs. {{ number_format($sale->total_net, 0) }}</div>
            </div>
            @else
            <div style="border: 1.5px dashed #000; padding: 4px; margin: 4px 0; text-align: center; border-radius: 4px; background: #fff;">
                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: #000;">*** GUEST BILL / KACHA SLIP ***</div>
                <div style="font-size: 9.5px; font-weight: 700; margin-top: 1px; color: #000;">(AWAITING FINAL PAYMENT - UNPAID)</div>
            </div>
            @endif
            <div class="divider"></div>
        @endif
        <div class="meta-grid">
            <div class="meta-row">
                <span class="meta-label">Invoice #:</span>
                <span class="meta-value">{{ $sale->invoice_no }}</span>
            </div>
            @if(!empty($sale->order_type))
            <div class="meta-row" style="font-weight: 900; font-size: 12.5px; border: 1.5px solid #000; padding: 2px 4px; margin: 2px 0; border-radius: 3px;">
                <span class="meta-label">ORDER TYPE:</span>
                <span class="meta-value" style="text-transform: uppercase;">
                    @if($sale->order_type === 'dine_in')
                        DINE-IN (TABLE #{{ $sale->table_no ?? '-' }})
                    @elseif($sale->order_type === 'takeaway')
                        TAKEAWAY (TOKEN #{{ $sale->token_no ?? '-' }})
                    @else
                        DELIVERY
                    @endif
                </span>
            </div>
            @endif
            <div class="meta-row">
                <span class="meta-label">Date:</span>
                <span class="meta-value">{{ $sale->created_at->format('d/m/Y h:i A') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Customer:</span>
                <span class="meta-value" style="font-weight: 800;">{{ $custName }}</span>
            </div>
            @if(!empty($custPhone))
            <div class="meta-row">
                <span class="meta-label">Phone #:</span>
                <span class="meta-value" style="font-weight: 800;">{{ $custPhone }}</span>
            </div>
            @endif
            @if(!empty($custAddress))
            <div class="meta-row" style="align-items: flex-start; margin-top: 1px;">
                <span class="meta-label" style="white-space: nowrap; flex-shrink: 0; margin-right: 6px;">Address:</span>
                <span class="meta-value" style="font-weight: 800; text-align: right; word-break: break-word;">{{ $custAddress }}</span>
            </div>
            @endif
            @if(!empty($riderName))
            <div class="meta-row">
                <span class="meta-label">Rider:</span>
                <span class="meta-value" style="font-weight: 800;">{{ $riderName }}</span>
            </div>
            @endif
            @if (auth()->check())
            <div class="meta-row">
                <span class="meta-label">Salesperson:</span>
                <span class="meta-value">{{ auth()->user()->name }}</span>
            </div>
            @endif
            @if(!empty($sale->order_notes))
            <div class="meta-row" style="flex-direction: column; align-items: flex-start; margin-top: 2px;">
                <span class="meta-label">Note:</span>
                <span class="meta-value" style="font-style: italic;">{{ $sale->order_notes }}</span>
            </div>
            @endif
            @if($sale->reference && $sale->reference !== $sale->order_notes)
            <div class="meta-row">
                <span class="meta-label">Remarks:</span>
                <span class="meta-value">{{ $sale->reference }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 6%;">S.</th>
                    <th style="width: 38%;">Description</th>
                    <th style="width: 13%;" class="text-center">Qty</th>
                    <th style="width: 20%;" class="text-end">Rate</th>
                    <th style="width: 23%;" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($saleItems as $item)
                    @php
                        $sizeMode = $item['size_mode'] ?? 'std';
                        $totalPieces = (int) $item['total_pieces'];

                        $variantUnit = strtolower($item['variant_unit'] ?? (is_array($item['color'] ?? null) ? ($item['color']['unit'] ?? '') : ''));
                        $weightGrams = (float)($item['weight_per_piece'] ?? (is_array($item['color'] ?? null) ? ($item['color']['weight_per_piece'] ?? 0) : 0));

                        $qtyDisplay = $totalPieces . ' Pcs';
                        if ($variantUnit === 'pcs' || $variantUnit === 'piece' || $variantUnit === 'pieces') {
                            $qtyDisplay = $totalPieces . ' Pcs';
                            if ($weightGrams > 0 && in_array($sizeMode, ['by_kg', 'by_gm', 'by_ton'])) {
                                $qtyDisplay .= ' (' . ($weightGrams == (int)$weightGrams ? (int)$weightGrams : $weightGrams) . 'g)';
                            }
                        } elseif (in_array($sizeMode, ['by_kg', 'by_gm', 'by_feet', 'by_meter'])) {
                            $uomLabel = match($sizeMode) {
                                'by_kg' => 'Kg',
                                'by_gm' => 'Gm',
                                'by_feet' => 'Ft',
                                'by_meter' => 'Mtr',
                                default => '',
                            };
                            $qtyVal = (float)($item['qty_box'] ?? $item['qty'] ?? $totalPieces);
                            $qtyDisplay = ($qtyVal == (int)$qtyVal ? (int)$qtyVal : number_format($qtyVal, 3)) . ' ' . $uomLabel;
                        } elseif ($sizeMode == 'by_cartons' || $sizeMode == 'by_size') {
                            $piecesPerBox = (int)($item['pieces_per_box'] ?? 1);
                            if ($piecesPerBox <= 0) $piecesPerBox = 1;
                            $boxes = floor($totalPieces / $piecesPerBox);
                            $loose = $totalPieces % $piecesPerBox;

                            if ($boxes > 0 && $loose > 0) {
                                $qtyDisplay = "$boxes.$loose";
                            } elseif ($boxes > 0) {
                                $qtyDisplay = $boxes;
                            } else {
                                $qtyDisplay = $loose;
                            }
                        }

                        $sizeStr = '';
                        if (!empty($item['size_val']) && $item['size_val'] !== '-') {
                            $sizeStr = $item['size_val'];
                        } elseif (($item['size_mode'] ?? '') == 'by_size' && ($item['height'] ?? 0) > 0 && ($item['width'] ?? 0) > 0) {
                            $sizeStr = number_format($item['width'], 0) . 'x' . number_format($item['height'], 0);
                        }
                        $colorStr = '';
                        if (!empty($item['color_val']) && $item['color_val'] !== '-') {
                            $colorStr = $item['color_val'];
                        }
                        $variantStr = implode(' | ', array_filter([$sizeStr, $colorStr]));
                    @endphp
                    <tr>
                        <td style="width: 6%;">{{ $loop->iteration }}</td>
                        <td style="width: 38%;">
                            <span class="item-name">{{ $item['item_name'] }}</span>
                            @if($variantStr && $variantStr !== '{' && trim($variantStr) !== '')
                                <span class="item-variant">{{ $variantStr }}</span>
                            @endif
                        </td>
                        <td style="width: 13%;" class="text-center">{{ $qtyDisplay }}</td>
                        <td style="width: 20%;" class="text-end">{{ number_format($item['price'], 0) }}</td>
                        <td style="width: 23%;" class="text-end">{{ number_format($item['total'], 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $exchangeReturn = \App\Models\SaleReturn::with('items.product')->where('remarks', 'LIKE', '%Invoice #'.$sale->invoice_no.'%')->first();
            $exchangeReturnedAmount = 0;
            if ($exchangeReturn) {
                $exchangeReturnedAmount = $exchangeReturn->items->sum('line_total');
            }
        @endphp

        @if($exchangeReturn && $exchangeReturn->items->count() > 0)
        <div class="divider" style="border-top: 1px dashed #000; margin: 4px 0;"></div>
        <div style="font-weight: bold; font-size: 11px; margin-bottom: 2px; color: #000;">Returned Items:</div>
        <table class="items-table" style="margin-bottom: 4px;">
            <tbody>
                @foreach ($exchangeReturn->items as $retItem)
                    <tr>
                        <td style="width: 6%;">{{ $loop->iteration }}</td>
                        <td style="width: 38%;">
                            @if($retItem->is_manual || empty($retItem->product_id))
                                <span class="item-name">{{ $retItem->product_name }} (Manual)</span>
                                @if(!empty($retItem->color) && $retItem->color !== '-' && $retItem->color !== '{')
                                    <span class="item-variant">{{ $retItem->color }}</span>
                                @endif
                            @else
                                <span class="item-name">{{ $retItem->product->item_name ?? 'Unknown' }}</span>
                                @php
                                    $retColorStr = '';
                                    if (!empty($retItem->color)) {
                                        $decoded = base64_decode($retItem->color, true);
                                        if ($decoded !== false && is_string($decoded) && str_starts_with(trim($decoded), '{')) {
                                            $parsed = json_decode($decoded, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                                                $parts = [];
                                                if (!empty($parsed['size']) && $parsed['size'] !== '-') $parts[] = $parsed['size'];
                                                if (!empty($parsed['color']) && $parsed['color'] !== '-') $parts[] = $parsed['color'];
                                                $retColorStr = implode(' | ', $parts);
                                            } else {
                                                $retColorStr = $retItem->color;
                                            }
                                        } else {
                                            $retColorStr = $retItem->color;
                                        }
                                    }
                                @endphp
                                @if($retColorStr)
                                    <span class="item-variant">{{ $retColorStr }}</span>
                                @endif
                            @endif
                        </td>
                        <td style="width: 13%;" class="text-center">{{ (float)$retItem->qty }}</td>
                        <td style="width: 20%;" class="text-end">{{ number_format($retItem->price, 0) }}</td>
                        <td style="width: 23%;" class="text-end">-{{ number_format($retItem->line_total, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Totals -->
        <div class="totals-section">
            <div class="tot-row">
                <span>Sub Total:</span>
                <span>{{ number_format($sale->total_bill_amount, 0) }}</span>
            </div>

            @if ($sale->delivery_charges > 0)
                <div class="tot-row" style="font-weight: 600;">
                    <span>Delivery Charges:</span>
                    <span>+ {{ number_format($sale->delivery_charges, 0) }}</span>
                </div>
            @endif

            @if ($sale->total_extradiscount > 0)
                <div class="tot-row">
                    <span>Discount:</span>
                    <span>- {{ number_format($sale->total_extradiscount, 0) }}</span>
                </div>
            @endif

            @if ($exchangeReturnedAmount > 0)
                <div class="tot-row">
                    <span>Return Value:</span>
                    <span>- {{ number_format($exchangeReturnedAmount, 0) }}</span>
                </div>
            @endif

            @php
                $finalPayable = $sale->total_net - $exchangeReturnedAmount;
            @endphp
            <div class="tot-row grand-total">
                @if($finalPayable < 0)
                    <span>REFUND TO CUSTOMER:</span>
                    <span>{{ number_format(abs($finalPayable), 0) }}</span>
                @else
                    <span>TOTAL PAYABLE:</span>
                    <span>{{ number_format($finalPayable, 0) }}</span>
                @endif
            </div>
        </div>

        <!-- Ledger -->
        <div class="balance-section">
            @if($isDelivery && ($sale->cash == 0 || $sale->order_status !== 'completed' || request()->has('provisional')))
                <div class="tot-row" style="font-weight: 800; font-size: 11px; margin-top: 2px;">
                    <span>PAYMENT TYPE:</span>
                    <span>CASH ON DELIVERY (COD)</span>
                </div>
                <div class="tot-row" style="font-weight: 900; font-size: 13px; border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; padding: 3px 0; margin-top: 3px;">
                    <span>COLLECT FROM CUSTOMER:</span>
                    <span>Rs. {{ number_format($finalPayable, 0) }}</span>
                </div>
            @elseif(request()->has('provisional') || !empty($isProvisional) || ($sale->sale_status === 'booked' && $sale->order_status !== 'completed'))
                <div class="tot-row" style="font-weight: 800; font-size: 11px; margin-top: 2px;">
                    <span>STATUS:</span>
                    <span>UNPAID (PROVISIONAL)</span>
                </div>
                <div class="tot-row" style="font-weight: 900; font-size: 13px; border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; padding: 3px 0; margin-top: 3px;">
                    <span>AMOUNT DUE:</span>
                    <span>Rs. {{ number_format($finalPayable, 0) }}</span>
                </div>
            @else
                @if(!$isWalkin)
                <div class="tot-row">
                    <span>Prev Balance:</span>
                    <span>{{ number_format(abs($previousBalance), 0) }} {{ $previousBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                </div>
                @endif
                <div class="tot-row">
                    <span>Paid Amount:</span>
                    <span>{{ number_format($sale->cash, 0) }}</span>
                </div>
                @if($sale->change > 0)
                <div class="tot-row">
                    <span>Change:</span>
                    <span>{{ number_format($sale->change, 0) }}</span>
                </div>
                @endif

                @if(!$isWalkin)
                @php
                    $finalBalance = $previousBalance + $sale->total_net - $sale->cash;
                @endphp
                <div class="tot-row closing-bal">
                    <span>CLOSING BALANCE:</span>
                    <span>{{ number_format(abs($finalBalance), 0) }} {{ $finalBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                </div>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <div class="soft-credit">Powered by Prowave Technologies<br>📞 +92 317 3836 223</div>
        </div>
    </div>

    <script>
        let countdown = 3;
        let countdownInterval = null;
        let isPaused = false;
        const isFromPos = {{ $fromPos ? 'true' : 'false' }};

        function startCountdown() {
            if (!isFromPos || isPaused) return;
            const timerEl = document.getElementById('secondsLeft');
            if (countdownInterval) clearInterval(countdownInterval);

            countdownInterval = setInterval(() => {
                if (isPaused) return;
                countdown--;
                if (timerEl) {
                    timerEl.innerText = countdown;
                }
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    if (window.opener) {
                        try { window.close(); } catch(e){}
                    } else {
                        window.location.href = "{{ route('pos.index') }}";
                    }
                }
            }, 1000);
        }

        function togglePause() {
            isPaused = !isPaused;
            const btn = document.getElementById('btnPause');
            const banner = document.getElementById('countdownBanner');
            if (isPaused) {
                if (countdownInterval) clearInterval(countdownInterval);
                if (btn) btn.innerText = "Resume Timer";
                if (banner) banner.style.opacity = "0.7";
            } else {
                countdown = 3;
                if (btn) btn.innerText = "Stay Here";
                if (banner) banner.style.opacity = "1";
                startCountdown();
            }
        }

        function triggerPrint() {
            if (countdownInterval) clearInterval(countdownInterval);
            window.print();
        }

        window.addEventListener('DOMContentLoaded', () => {
            // Automatically open print dialog
            setTimeout(() => {
                try {
                    window.print();
                } catch(e){}
            }, 400);
        });

        // When print dialog closes (printed or cancelled)
        window.onafterprint = function() {
            if (window.opener) {
                setTimeout(() => {
                    try { window.close(); } catch(e){}
                }, 800);
            } else if (isFromPos && !isPaused) {
                countdown = 3;
                startCountdown();
            }
        };

        // Fallback: When window regains focus after print
        let printFired = false;
        window.addEventListener('beforeprint', () => {
            printFired = true;
        });
        window.addEventListener('focus', () => {
            if (printFired && window.opener) {
                setTimeout(() => {
                    try { window.close(); } catch(e){}
                }, 800);
            } else if (printFired && isFromPos && !isPaused && !countdownInterval) {
                countdown = 3;
                startCountdown();
            }
        });
    </script>
</body>

</html>
