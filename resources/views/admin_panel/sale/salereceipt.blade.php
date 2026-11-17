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

            // Ensure delivery charges are counted in collectable amount
            $deliveryCharges = (float)($sale->delivery_charges ?? 0);
            $itemsNet = (float)$sale->total_bill_amount - (float)($sale->total_extradiscount ?? 0);
            $netPayable = (float)$sale->total_net;
            // If historical or draft sale was saved without adding delivery charges to total_net:
            if ($deliveryCharges > 0 && abs($netPayable - $itemsNet) < 0.01) {
                $netPayable += $deliveryCharges;
            }
        @endphp

        @if(request()->has('provisional') || !empty($isProvisional) || ($sale->sale_status === 'booked' && $sale->order_status !== 'completed'))
            @if($isDelivery)
            <div style="border: 2px solid #000; padding: 4px; margin: 4px 0; text-align: center; border-radius: 4px; background: #fff;">
                <div style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: #000;">*** DELIVERY BILL (CASH ON DELIVERY) ***</div>
                <div style="font-size: 11.5px; font-weight: 900; margin-top: 2px; color: #000;">COLLECT CASH: Rs. {{ number_format($netPayable, 0) }}</div>
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
            <div class="meta-row" style="align-items: center;">
                <span class="meta-label">Invoice #:</span>
                <span class="meta-value" style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                    <span>{{ $sale->invoice_no }}</span>
                    @if(!empty($sale->order_type))
                        @if($sale->order_type === 'dine_in')
                            <span title="Dine-In" style="display: inline-flex; align-items: center; gap: 3px;">
                                <svg width="15" height="15" viewBox="0 0 416 512" fill="#000" style="vertical-align: middle;"><path d="M207.9 15.2c.8 4.7 16.1 94.5 16.1 128.8 0 52.3-27.8 89.6-68.9 97.2V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V241.2C49.9 233.6 22.1 196.3 22.1 144c0-34.3 15.3-124.1 16.1-128.8.6-3.8 3.9-6.6 7.8-6.6h17.9c4 0 7.4 3 7.8 7 1.8 17.7 8.1 79.5 13.9 104.4 1.1 4.7 5.2 8 10 8s8.9-3.3 10-8C111.4 95.1 117.7 33.3 119.5 15.6c.4-4 3.8-7 7.8-7h15.8c4 0 7.4 3 7.8 7 1.8 17.7 8.1 79.5 13.9 104.4 1.1 4.7 5.2 8 10 8s8.9-3.3 10-8c5.8-24.9 12.1-86.7 13.9-104.4.4-4 3.8-7 7.8-7h17.9c3.9 0 7.2 2.8 7.8 6.6zM271.7 16c21.8 0 39.5 17.7 39.5 39.5V288h-32c-17.7 0-32 14.3-32 32v160c0 17.7 14.3 32 32 32h32c17.7 0 32-14.3 32-32V55.5C343.2 24.9 311.2 0 271.7 0h-16c-8.8 0-16 7.2-16 16s7.2 16 16 16h16z"/></svg>
                                @if(!empty($sale->table_no))
                                    <span style="font-size: 11px;">(T-{{ $sale->table_no }})</span>
                                @endif
                            </span>
                        @elseif($sale->order_type === 'takeaway')
                            <span title="Takeaway" style="display: inline-flex; align-items: center; gap: 3px;">
                                <svg width="15" height="15" viewBox="0 0 448 512" fill="#000" style="vertical-align: middle;"><path d="M352 160V128C352 57.3 294.7 0 224 0S96 57.3 96 128v32H16c-8.8 0-16 7.2-16 16v288c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V176c0-8.8-7.2-16-16-16h-80zm-192-32c0-35.3 28.7-64 64-64s64 28.7 64 64v32H160v-32zm224 320H64V224h32v32c0 8.8 7.2 16 16 16s16-7.2 16-16v-32h192v32c0 8.8 7.2 16 16 16s16-7.2 16-16v-32h32v224z"/></svg>
                                @if(!empty($sale->token_no))
                                    <span style="font-size: 11px;">(#{{ $sale->token_no }})</span>
                                @endif
                            </span>
                        @else
                            <span title="Delivery" style="display: inline-flex; align-items: center;">
                                <svg width="20" height="16" viewBox="0 0 640 512" fill="#000" style="vertical-align: middle;"><path d="M575.8 245.8l-40.3-80.6A48 48 0 0 0 492.6 138H448v-26c0-14.3-11.7-26-26-26h-36c-14.3 0-26 11.7-26 26v26h-24c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24h18.2l-28.7 57.4C307 274.6 279.7 272 256 272c-20.7 0-40.5 4.1-59 11.1L244 192h52c13.3 0 24-10.7 24-24s-10.7-24-24-24H216c-8.9 0-17.1 5-21.2 12.8L151.2 240H96c-53 0-96 43-96 96 0 48.9 36.7 89.2 84.1 95.3-2.7 8-4.1 16.5-4.1 25.3 0 44.2 35.8 80 80 80s80-35.8 80-80c0-9.6-1.8-18.9-5-27.5 1.7-.3 3.3-.8 5-1.2 24.3-6.1 47.1-15.6 67.8-27.9l40.7 40.7c-5.8 10.8-9.5 22.9-9.5 35.9 0 44.2 35.8 80 80 80s80-35.8 80-80c0-14.7-4-28.4-11-40.2l-47.5-80.8 45.4-90.8h69.1l30.6 61.2c5.8 11.6 17.6 18.8 30.6 18.8h8c17.7 0 32-14.3 32-32 0-9.2-3.9-17.5-10.2-23.4zM160 496c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z"/></svg>
                            </span>
                        @endif
                    @endif
                </span>
            </div>
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
                $finalPayable = $netPayable - $exchangeReturnedAmount;
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
                    $finalBalance = $previousBalance + $netPayable - $sale->cash;
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
