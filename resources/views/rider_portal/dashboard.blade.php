<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rider Delivery Screen | {{ $rider->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --r-primary: #ea580c;
            --r-primary-dark: #c2410c;
            --r-primary-light: #fff7ed;
            --r-dark: #0f172a;
            --r-gray: #64748b;
            --r-bg: #f1f5f9;
            --r-card: #ffffff;
            --r-success: #16a34a;
        }
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            background-color: var(--r-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            padding-bottom: 80px;
        }

        /* Top Sticky Navbar */
        .rider-navbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .rider-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--r-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.4);
        }

        /* Stats Bar */
        .rider-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 12px 14px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .stat-card {
            background: var(--r-bg);
            border-radius: 12px;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .stat-val {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 2px;
        }
        .stat-lbl {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--r-gray);
        }

        /* Custom Tabs */
        .rider-tabs {
            display: flex;
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 0 14px;
        }
        .r-tab-btn {
            flex: 1;
            text-align: center;
            padding: 12px 6px;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--r-gray);
            border: none;
            background: transparent;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }
        .r-tab-btn.active {
            color: var(--r-primary);
        }
        .r-tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 15%;
            right: 15%;
            height: 3px;
            background: var(--r-primary);
            border-radius: 3px 3px 0 0;
        }

        /* Order Cards */
        .order-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            margin-bottom: 14px;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .order-card-header {
            padding: 10px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .order-card-body {
            padding: 14px;
        }
        .status-pill {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .pill-pending {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .pill-dispatched {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .pill-delivered {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .action-circle-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            text-decoration: none;
            transition: transform 0.1s;
        }
        .action-circle-btn:active {
            transform: scale(0.92);
        }
        .btn-call {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .btn-wa {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .btn-map {
            background: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .cash-banner {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1.5px dashed #f59e0b;
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 12px 0;
        }
        .btn-deliver-order {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            border: none;
            color: #ffffff;
            font-weight: 800;
            font-size: 15px;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s;
        }
        .btn-deliver-order:active {
            transform: scale(0.98);
        }
        .btn-pickup-order {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            border-radius: 12px;
            padding: 10px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        /* Floating Sync / Status Bar */
        .live-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse 1.6s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>
</head>
<body>

    @if(!empty($isAdminView))
    {{-- Super Admin Top Quick Navigation Bar --}}
    <div style="background: linear-gradient(135deg, #1e293b, #0f172a); border-bottom: 2px solid #f59e0b; padding: 7px 14px; color: #fff;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark font-weight-bold" style="font-size: 11px; padding: 4px 8px;">
                    <i class="fas fa-user-shield me-1"></i> SUPER ADMIN
                </span>
                <span class="small text-light fw-bold d-none d-sm-inline">Rider Live Screen Monitor</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('restaurant.riders.index') }}" class="btn btn-outline-warning btn-sm py-0 px-2 fw-bold" style="font-size: 11.5px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Riders List
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm py-0 px-2 fw-bold" style="font-size: 11.5px;">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Top Sticky Navbar --}}
    <nav class="rider-navbar">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rider-avatar">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    @if(!empty($isAdminView) && isset($allRiders) && $allRiders->count() > 0)
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <select class="form-select form-select-sm bg-dark text-white border-warning fw-bold py-1 px-2" 
                                    id="adminRiderDropdown" onchange="onSwitchRider(this.value)" 
                                    style="font-size: 13px; min-width: 170px; max-width: 220px; cursor: pointer; box-shadow: 0 0 10px rgba(245, 158, 11, 0.35);">
                                @foreach($allRiders as $rItem)
                                    <option value="{{ $rItem->id }}" {{ $rItem->id == $rider->id ? 'selected' : '' }}>
                                        🛵 {{ $rItem->name }} ({{ $rItem->vehicle_no ?: 'No Bike' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="fw-bold fs-6 lh-1 mb-1">{{ $rider->name }}</div>
                    @endif
                    <div class="d-flex align-items-center gap-1" style="font-size: 11px; opacity: 0.85;">
                        <span class="live-pulse"></span>
                        <span id="headerVehicleNo">{{ $rider->vehicle_no ?: 'Rider Fleet' }}</span>
                        <span>•</span>
                        <span>{{ optional($rider->branch)->name ?: 'Munch FC' }}</span>
                        @if(!empty($isAdminView))
                            <span class="badge bg-warning text-dark py-0 px-1" style="font-size: 9.5px; font-weight: 800;">Admin View</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Online / Offline Switch --}}
                <button type="button" class="btn btn-sm py-1 px-2.5 rounded-pill fw-bold {{ $rider->duty_status === 'available' ? 'btn-success' : 'btn-outline-light' }}" 
                        id="btnToggleDuty" onclick="toggleDutyStatus()" style="font-size: 11.5px;">
                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                    <span id="dutyText">{{ $rider->duty_status === 'available' ? 'On Duty' : 'Off Duty' }}</span>
                </button>

                @if(!empty($isAdminView))
                    {{-- Exit to Admin Panel --}}
                    <a href="{{ route('restaurant.riders.index') }}" class="btn btn-sm btn-outline-danger py-1 px-2 text-white border-0" title="Exit to Admin Panel" style="font-size: 14px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                @else
                    {{-- Logout Button for normal rider --}}
                    <form action="{{ route('rider.logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 text-white border-0" title="Logout" style="font-size: 14px;">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </nav>

    {{-- Stats Grid Bar --}}
    <div class="rider-stats-grid">
        <div class="stat-card">
            <div class="stat-val text-primary" id="statActiveCount">{{ count($activeOrders) }}</div>
            <div class="stat-lbl">Active Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-val text-success" id="statCompletedCount">{{ count($completedToday) }}</div>
            <div class="stat-lbl">Delivered Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-val text-danger" id="statCashInHand">Rs. {{ number_format($cashInHand) }}</div>
            <div class="stat-lbl">Cash in Pocket</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="rider-tabs">
        <button type="button" class="r-tab-btn active" id="tabActiveBtn" onclick="switchRiderTab('active')">
            <i class="fas fa-motorcycle me-1 text-warning"></i> Active Deliveries (<span id="tabActiveCountBadge">{{ count($activeOrders) }}</span>)
        </button>
        <button type="button" class="r-tab-btn" id="tabHistoryBtn" onclick="switchRiderTab('history')">
            <i class="fas fa-history me-1 text-success"></i> Completed Today (<span id="tabHistoryCountBadge">{{ count($completedToday) }}</span>)
        </button>
    </div>

    {{-- Main Container --}}
    <div class="p-3">

        {{-- TAB 1: ACTIVE ORDERS CONTAINER --}}
        <div id="activeOrdersPane">
            @if(count($activeOrders) === 0)
                <div class="text-center py-5" id="emptyActiveOrdersPlaceholder">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #fed7aa; color: #ea580c; display: inline-flex; align-items: center; justify-content: center; font-size: 36px; margin-bottom: 16px;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No Active Orders</h5>
                    <p class="text-muted small px-4 mb-3">Counter se jab koi delivery order assign hoga to yahan foran notification ke sath aa jayega.</p>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-bold" onclick="pollLiveOrders(true)">
                        <i class="fas fa-rotate me-1"></i> Refresh Screen
                    </button>
                </div>
            @endif

            <div id="activeCardsList">
                @foreach($activeOrders as $o)
                    @php
                        $phone = $o->rider_phone ?: (optional($o->customer_relation)->phone_number ?: optional($o->customer_relation)->mobile);
                        $cleanPhone = preg_replace('/[^0-9]/', '', $phone ?: '');
                        $address = $o->delivery_address ?: (optional($o->customer_relation)->address ?: 'No address provided');
                        $custName = optional($o->customer_relation)->name ?: ($o->walkin_name ?: 'Customer');
                        $billAmount = (float) ($o->total_net ?: $o->total_bill_amount);
                        $elapsedMins = $o->created_at ? round(now()->diffInMinutes($o->created_at)) : 0;
                    @endphp
                    <div class="order-card" id="cardOrder{{ $o->id }}">
                        <div class="order-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold fs-6 text-dark">#{{ $o->invoice_no }}</span>
                                @if($o->delivery_status === 'dispatched')
                                    <span class="status-pill pill-dispatched"><i class="fas fa-route"></i> Out On Road</span>
                                @else
                                    <span class="status-pill pill-pending"><i class="fas fa-clock"></i> Assigned</span>
                                @endif
                            </div>
                            <span class="text-muted small fw-bold" style="font-size: 11px;">
                                <i class="far fa-clock me-1"></i> {{ $elapsedMins }}m ago
                            </span>
                        </div>

                        <div class="order-card-body">
                            {{-- Customer Info & Actions --}}
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $custName }}</div>
                                    <div class="text-muted small">
                                        <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $phone ?: 'No phone' }}
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($cleanPhone)
                                        <a href="tel:{{ $cleanPhone }}" class="action-circle-btn btn-call" title="Call Customer">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        <a href="https://wa.me/92{{ ltrim($cleanPhone, '0') }}" target="_blank" class="action-circle-btn btn-wa" title="WhatsApp Chat">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Delivery Address --}}
                            <div class="p-2.5 rounded-3 bg-light border mb-3">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-map-marker-alt text-danger mt-1"></i>
                                    <div>
                                        <div class="small fw-bold text-dark lh-sm">{{ $address }}</div>
                                        @if($o->order_notes)
                                            <div class="small text-danger fw-semibold mt-1">
                                                <i class="fas fa-comment-dots me-1"></i> Note: {{ $o->order_notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Food Items Preview --}}
                            <div class="mb-3">
                                <div class="small text-muted fw-bold mb-1">Items To Deliver:</div>
                                <div class="p-2 rounded bg-light" style="font-size: 12.5px;">
                                    @foreach($o->items as $it)
                                        @php
                                            $qty = (int) ($it->total_pieces > 0 ? $it->total_pieces : ($it->qty > 0 ? $it->qty : 1));
                                            $iName = $it->product_name ?? optional($it->product)->item_name ?? 'Item';
                                        @endphp
                                        <div class="d-flex justify-content-between py-0.5">
                                            <span class="fw-semibold text-dark">{{ $qty }}x {{ $iName }}</span>
                                            <span class="text-muted">Rs. {{ number_format($it->total) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- COD Cash Collection Banner --}}
                            <div class="cash-banner">
                                <div>
                                    <small class="text-dark fw-bold d-block text-uppercase" style="font-size: 10px;">Cash On Delivery (COD)</small>
                                    <strong class="text-dark fs-5">Rs. {{ number_format($billAmount) }}</strong>
                                </div>
                                <span class="badge bg-warning text-dark border fw-bold px-2 py-1">Collect Cash</span>
                            </div>

                            {{-- Action Buttons --}}
                            @if($o->delivery_status === 'pending')
                                <button type="button" class="btn btn-pickup-order" onclick="markPickedUp({{ $o->id }}, '{{ $o->invoice_no }}')">
                                    <i class="fas fa-motorcycle"></i> Picked Up / Start Delivery
                                </button>
                            @endif

                            <button type="button" class="btn btn-deliver-order" onclick="openDeliverModal({{ $o->id }}, '{{ $o->invoice_no }}', {{ $billAmount }}, '{{ addslashes($custName) }}')">
                                <i class="fas fa-check-circle"></i> Mark as Delivered
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TAB 2: COMPLETED TODAY CONTAINER --}}
        <div id="completedOrdersPane" class="d-none">
            @if(count($completedToday) === 0)
                <div class="text-center py-5">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 12px;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h6 class="fw-bold text-dark">No Deliveries Completed Today Yet</h6>
                    <p class="text-muted small">Jab aap koi order deliver karenge to wo yahan hisaab me record ho jayega.</p>
                </div>
            @else
                @foreach($completedToday as $c)
                    @php
                        $cBill = (float) ($c->total_net ?: $c->total_bill_amount);
                        $cName = optional($c->customer_relation)->name ?: ($c->walkin_name ?: 'Customer');
                    @endphp
                    <div class="order-card p-3 mb-2.5">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold text-dark">#{{ $c->invoice_no }}</span>
                            <span class="status-pill pill-delivered"><i class="fas fa-check"></i> Delivered</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">{{ $cName }}</span>
                            <span class="fw-bold text-dark">Rs. {{ number_format($cBill) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="font-size: 11px;">
                            <span class="text-muted">{{ $c->updated_at ? $c->updated_at->format('h:i A') : '' }}</span>
                            @if($c->rider_cash_settled)
                                <span class="badge bg-light text-success border"><i class="fas fa-shield-alt me-1"></i> Cash Settled at Counter</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-hand-holding-usd me-1"></i> Cash in Hand (Unsettled)</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Delivery Confirmation Modal --}}
    <div class="modal fade" id="confirmDeliverModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white py-3 px-4 border-0">
                    <h6 class="modal-title fw-bold mb-0">
                        <i class="fas fa-hand-holding-usd me-1"></i> Confirm Food Handover
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 12px;">
                        <i class="fas fa-check"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="mDelivInvoice">#INV-0000</h5>
                    <p class="text-muted small mb-3">Customer: <strong id="mDelivCustName">-</strong></p>

                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Cash Collected from Customer:</div>
                        <div class="fs-3 fw-bold text-success" id="mDelivAmount">Rs. 0</div>
                    </div>

                    <input type="hidden" id="mDelivOrderId" value="">

                    <button type="button" class="btn btn-success fw-bold w-100 py-2.5 rounded-3 mb-2" id="btnConfirmDeliverSubmit" onclick="submitDeliverOrder()">
                        <i class="fas fa-check-circle me-1"></i> Yes, Cash Collected &amp; Delivered
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-3 py-2" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

    <script>
        const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        const currentRiderId = {{ $rider->id }};
        const isAdminView = {{ !empty($isAdminView) ? 'true' : 'false' }};
        let currentTab = 'active';
        let previousActiveOrderCount = {{ count($activeOrders) }};

        // Switch rider from header dropdown in Admin View
        function onSwitchRider(riderId) {
            if (!riderId) return;
            window.location.href = "{{ route('rider.portal') }}?rider_id=" + riderId;
        }

        // Play gentle chime sound when a new order lands
        function playChimeSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.15); // A5
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.6);
            } catch(e) {}
        }

        function switchRiderTab(tab) {
            currentTab = tab;
            if (tab === 'active') {
                $('#tabActiveBtn').addClass('active');
                $('#tabHistoryBtn').removeClass('active');
                $('#activeOrdersPane').removeClass('d-none');
                $('#completedOrdersPane').addClass('d-none');
            } else {
                $('#tabHistoryBtn').addClass('active');
                $('#tabActiveBtn').removeClass('active');
                $('#completedOrdersPane').removeClass('d-none');
                $('#activeOrdersPane').addClass('d-none');
            }
        }

        // Toggle Duty Status (Online / Offline)
        function toggleDutyStatus() {
            let curText = $('#dutyText').text().trim();
            let newStatus = (curText === 'On Duty') ? 'off_duty' : 'available';

            $.ajax({
                url: "{{ route('rider.duty_status') }}",
                type: "POST",
                data: {
                    _token: CSRF_TOKEN,
                    duty_status: newStatus,
                    rider_id: currentRiderId
                },
                success: function(res) {
                    if (res.success) {
                        if (res.duty_status === 'available') {
                            $('#btnToggleDuty').removeClass('btn-outline-light').addClass('btn-success');
                            $('#dutyText').text('On Duty');
                        } else {
                            $('#btnToggleDuty').removeClass('btn-success').addClass('btn-outline-light');
                            $('#dutyText').text('Off Duty');
                        }
                    }
                },
                error: function(xhr) {
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error changing duty status.';
                    alert(msg);
                }
            });
        }

        // Mark Picked up / Out for Delivery
        function markPickedUp(orderId, invoiceNo) {
            if (!confirm(`Mark Order #${invoiceNo} as Picked Up and Out on Road?`)) return;

            $.ajax({
                url: `{{ url('rider/order') }}/${orderId}/out-for-delivery`,
                type: "POST",
                data: { 
                    _token: CSRF_TOKEN,
                    rider_id: currentRiderId
                },
                success: function(res) {
                    pollLiveOrders(true);
                },
                error: function() {
                    alert('Could not update status. Please try again.');
                }
            });
        }

        // Open Delivery Confirmation Modal
        function openDeliverModal(orderId, invoiceNo, billAmount, custName) {
            $('#mDelivOrderId').val(orderId);
            $('#mDelivInvoice').text('#' + invoiceNo);
            $('#mDelivCustName').text(custName || 'Customer');
            $('#mDelivAmount').text('Rs. ' + Number(billAmount).toLocaleString());
            $('#confirmDeliverModal').modal('show');
        }

        // Submit Mark as Delivered
        function submitDeliverOrder() {
            let orderId = $('#mDelivOrderId').val();
            let btn = $('#btnConfirmDeliverSubmit');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: `{{ url('rider/order') }}/${orderId}/mark-delivered`,
                type: "POST",
                data: { 
                    _token: CSRF_TOKEN,
                    rider_id: currentRiderId
                },
                success: function(res) {
                    $('#confirmDeliverModal').modal('hide');
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Yes, Cash Collected & Delivered');
                    // Refresh data
                    location.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Yes, Cash Collected & Delivered');
                    let msg = 'Error updating delivery. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        }

        // Auto-Polling: checks every 10 seconds for assigned orders
        function pollLiveOrders(manual = false) {
            $.get("{{ route('rider.api.live_orders') }}?rider_id=" + currentRiderId, function(data) {
                if (!data || !data.success) return;

                $('#statActiveCount').text(data.active_count);
                $('#tabActiveCountBadge').text(data.active_count);
                $('#statCompletedCount').text(data.completed_count);
                $('#tabHistoryCountBadge').text(data.completed_count);
                $('#statCashInHand').text('Rs. ' + Number(data.cash_in_hand).toLocaleString());

                // If new order arrived, chime and reload
                if (data.active_count > previousActiveOrderCount) {
                    playChimeSound();
                    setTimeout(() => location.reload(), 800);
                } else if (data.active_count < previousActiveOrderCount || manual) {
                    location.reload();
                }
                previousActiveOrderCount = data.active_count;
            });
        }

        // Run poll every 10 seconds
        setInterval(pollLiveOrders, 10000);
    </script>
</body>
</html>
