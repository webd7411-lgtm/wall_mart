@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid py-3">

    {{-- Header & Controls --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-list-alt text-primary mr-2"></i> Website Online Orders & Rider Dispatch
            </h4>
            <p class="text-muted small mb-0">
                Manage website orders, assign delivery riders, collect COD cash, and sync live with restaurant sales & inventory.
            </p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            {{-- Auto-refresh indicator --}}
            <div class="bg-white border rounded px-3 py-1 d-flex align-items-center shadow-sm">
                <span class="spinner-grow spinner-grow-sm text-success mr-2" role="status" style="width: 10px; height: 10px;"></span>
                <span class="small font-weight-bold text-muted mr-2">Live Sync:</span>
                <span class="badge badge-light border font-weight-bold text-dark" id="refreshTimer">15s</span>
            </div>
            
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm font-weight-bold" onclick="location.reload()">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>

            <a href="{{ route('restaurant.riders.index') }}" class="btn btn-outline-dark btn-sm px-3 shadow-sm font-weight-bold" title="Manage Riders and Settle Cash">
                <i class="fas fa-motorcycle text-warning mr-1"></i> Riders & Cash Settlement
            </a>

            <a href="{{ route('restaurant.deliveries.index') }}" class="btn btn-outline-info btn-sm px-3 shadow-sm font-weight-bold" title="Restaurant Deliveries Dispatch Dashboard">
                <i class="fas fa-shipping-fast mr-1"></i> Deliveries
            </a>

            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold">
                <i class="fas fa-cash-register mr-1"></i> POS Counter
            </a>
        </div>
    </div>

    {{-- Top Summary Stats Cards --}}
    <div class="row g-2 mb-3">
        {{-- Total Online Orders --}}
        <div class="col-xl-2 col-md-4 col-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg p-3 text-center bg-white" style="border-left: 4px solid #6366f1 !important;">
                <div class="small text-muted font-weight-bold text-uppercase">Total Orders</div>
                <div class="h3 font-weight-bold text-dark my-1">{{ $allOrdersCount }}</div>
                <div class="small text-muted">All-Time Web Orders</div>
            </div>
        </div>

        {{-- New / Pending --}}
        <div class="col-xl-2 col-md-4 col-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg p-3 text-center bg-white {{ $pendingCount > 0 ? 'border-warning shadow' : '' }}" style="border-left: 4px solid #f59e0b !important;">
                <div class="small text-warning font-weight-bold text-uppercase">
                    @if($pendingCount > 0)
                        <span class="spinner-grow spinner-grow-sm mr-1" style="width: 8px; height: 8px;"></span>
                    @endif
                    New / Pending
                </div>
                <div class="h3 font-weight-bold text-warning my-1" id="statPendingCount">{{ $pendingCount }}</div>
                <div class="small text-muted">Awaiting Acceptance</div>
            </div>
        </div>

        {{-- In Kitchen (Cooking) --}}
        <div class="col-xl-2 col-md-4 col-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg p-3 text-center bg-white" style="border-left: 4px solid #3b82f6 !important;">
                <div class="small text-primary font-weight-bold text-uppercase">In Kitchen</div>
                <div class="h3 font-weight-bold text-primary my-1">{{ $processingCount }}</div>
                <div class="small text-muted">Cooking Hot</div>
            </div>
        </div>

        {{-- Out for Delivery (With Rider) --}}
        <div class="col-xl-2 col-md-4 col-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg p-3 text-center bg-white" style="border-left: 4px solid #f97316 !important;">
                <div class="small text-danger font-weight-bold text-uppercase">With Rider</div>
                <div class="h3 font-weight-bold text-danger my-1">{{ $shippedCount }}</div>
                <div class="small text-muted">Rider on Delivery</div>
            </div>
        </div>

        {{-- Delivered & Paid --}}
        <div class="col-xl-2 col-md-4 col-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg p-3 text-center bg-white" style="border-left: 4px solid #10b981 !important;">
                <div class="small text-success font-weight-bold text-uppercase">Delivered</div>
                <div class="h3 font-weight-bold text-success my-1">{{ $deliveredCount }}</div>
                <div class="small text-muted">Completed & Logged</div>
            </div>
        </div>

        {{-- Today's Web Revenue --}}
        <div class="col-xl-2 col-md-4 col-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg p-3 text-center bg-white" style="border-left: 4px solid #14b8a6 !important;">
                <div class="small text-muted font-weight-bold text-uppercase">Today's Sales</div>
                <div class="h4 font-weight-bold text-dark my-1">Rs. {{ number_format($todayRevenue) }}</div>
                <div class="small text-success font-weight-bold">Web Sales in Till</div>
            </div>
        </div>
    </div>

    {{-- Filter Nav Tabs & Search Form --}}
    <div class="card border-0 shadow-sm rounded-lg mb-3 bg-white">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                {{-- Status Pills --}}
                <div class="btn-group btn-group-sm flex-wrap" role="group">
                    <a href="{{ route('restaurant.online_orders', array_merge(request()->query(), ['status' => 'all'])) }}" class="btn {{ $activeStatus === 'all' ? 'btn-dark' : 'btn-outline-secondary' }} font-weight-bold px-3">
                        All Orders ({{ $allOrdersCount }})
                    </a>
                    <a href="{{ route('restaurant.online_orders', array_merge(request()->query(), ['status' => 'pending'])) }}" class="btn {{ $activeStatus === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} font-weight-bold px-3">
                        🟡 Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('restaurant.online_orders', array_merge(request()->query(), ['status' => 'processing'])) }}" class="btn {{ $activeStatus === 'processing' ? 'btn-primary' : 'btn-outline-primary' }} font-weight-bold px-3">
                        🔵 In Kitchen ({{ $processingCount }})
                    </a>
                    <a href="{{ route('restaurant.online_orders', array_merge(request()->query(), ['status' => 'shipped'])) }}" class="btn {{ $activeStatus === 'shipped' ? 'btn-info text-white' : 'btn-outline-info' }} font-weight-bold px-3">
                        🛵 With Rider ({{ $shippedCount }})
                    </a>
                    <a href="{{ route('restaurant.online_orders', array_merge(request()->query(), ['status' => 'delivered'])) }}" class="btn {{ $activeStatus === 'delivered' ? 'btn-success' : 'btn-outline-success' }} font-weight-bold px-3">
                        🟢 Delivered ({{ $deliveredCount }})
                    </a>
                    <a href="{{ route('restaurant.online_orders', array_merge(request()->query(), ['status' => 'cancelled'])) }}" class="btn {{ $activeStatus === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' }} font-weight-bold px-3">
                        🔴 Cancelled ({{ $cancelledCount }})
                    </a>
                </div>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('restaurant.online_orders') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="status" value="{{ $activeStatus }}">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <input type="text" name="search" class="form-control" placeholder="Search Order #, Rider, Phone..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    @if(request()->filled('search'))
                        <a href="{{ route('restaurant.online_orders', ['status' => $activeStatus]) }}" class="btn btn-sm btn-light border text-danger" title="Clear Search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- ORDERS LIST TABLE --}}
    <div class="card border-0 shadow-sm rounded-lg bg-white mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-table mr-2 text-primary"></i> Live Orders List ({{ $orders->total() }} Total)
            </h6>
            <div class="small text-muted">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="ordersListTable">
                <thead class="thead-light">
                    <tr class="text-uppercase small font-weight-bold text-muted border-bottom">
                        <th style="width: 130px;">Order #</th>
                        <th style="width: 140px;">Time / Date</th>
                        <th style="width: 250px;">Customer & Address</th>
                        <th style="width: 180px;">Assigned Rider</th>
                        <th style="min-width: 240px;">Ordered Items</th>
                        <th style="width: 150px;">Amount & Status</th>
                        <th style="width: 190px;" class="text-center">Dispatch & Delivery</th>
                        <th style="width: 120px;" class="text-right">Receipts</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php
                        $rawPhone = $order->shipping_phone ?? '';
                        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $waNumber = '92' . substr($cleanPhone, 1);
                        } else {
                            $waNumber = $cleanPhone;
                        }
                        $waText = urlencode("Hello {$order->shipping_name}, regarding your Munch Fried Chicken Order #{$order->order_number}");
                        $totalItemsCount = $order->items->sum('quantity');
                    @endphp
                    <tr class="order-table-row {{ $order->order_status === 'pending' ? 'bg-warning-soft' : '' }}" id="order-row-{{ $order->id }}">
                        
                        {{-- 1. Order Number & Linked Sale --}}
                        <td class="align-top py-3">
                            <span class="font-weight-bold text-dark h6 mb-1 d-block">
                                #{{ $order->order_number }}
                            </span>
                            <span class="badge badge-light border text-muted px-2 py-1 small font-weight-normal d-inline-block mb-1">
                                <i class="fas fa-globe text-primary mr-1"></i> Website
                            </span>
                            @if($order->sale)
                                <a href="{{ route('sale.index', ['search' => $order->order_number]) }}" class="badge badge-success text-white px-2 py-1 small d-block" title="View linked Sale in POS/Sales Ledger">
                                    <i class="fas fa-link mr-1"></i> Sale #{{ $order->sale->id }}
                                </a>
                            @endif
                            <button class="btn btn-xs btn-outline-primary py-0 px-2 mt-1 font-weight-bold d-inline-block" style="font-size: 11px;" onclick="openEditOrderModal({{ $order->id }})" title="Edit Order Details">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                        </td>

                        {{-- 2. Time & Date --}}
                        <td class="align-top py-3">
                            <div class="font-weight-bold text-dark">
                                <i class="far fa-clock text-muted mr-1"></i> {{ $order->created_at->format('h:i A') }}
                            </div>
                            <div class="small text-muted">
                                {{ $order->created_at->format('d M, Y') }}
                            </div>
                            <div class="badge badge-light border text-info px-1 mt-1 small">
                                {{ $order->created_at->diffForHumans() }}
                            </div>
                        </td>

                        {{-- 3. Customer Info & Address --}}
                        <td class="align-top py-3">
                            <div class="font-weight-bold text-dark mb-1">
                                <i class="fas fa-user-circle text-primary mr-1"></i> {{ $order->shipping_name }}
                            </div>
                            <div class="small mb-1">
                                <i class="fas fa-phone-alt text-success mr-1"></i>
                                <a href="tel:{{ $rawPhone }}" class="text-dark font-weight-bold text-decoration-none mr-2">
                                    {{ $rawPhone }}
                                </a>
                                @if(!empty($waNumber))
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="badge badge-success px-2 py-1 text-decoration-none" title="Chat on WhatsApp">
                                        <i class="fab fa-whatsapp"></i> Chat
                                    </a>
                                @endif
                            </div>
                            <div class="small text-muted" style="max-width: 250px; white-space: normal;">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                <span>{{ $order->shipping_address ?: 'Store Pickup / Walk-in' }}</span>
                            </div>
                            @if(!empty($order->order_notes))
                                <div class="mt-1 small bg-warning-light text-dark p-1 rounded border border-warning" style="max-width: 250px; white-space: normal;">
                                    <i class="fas fa-sticky-note text-warning mr-1"></i> <strong>Note:</strong> {{ $order->order_notes }}
                                </div>
                            @endif
                        </td>

                        {{-- 4. Assigned Rider --}}
                        <td class="align-top py-3">
                            @if(!empty($order->rider_name))
                                <div class="p-2 rounded bg-light border">
                                    <div class="font-weight-bold text-dark small">
                                        <i class="fas fa-motorcycle text-primary mr-1"></i> {{ $order->rider_name }}
                                    </div>
                                    @if(!empty($order->rider_phone))
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-phone mr-1"></i>
                                            <a href="tel:{{ $order->rider_phone }}" class="text-dark">{{ $order->rider_phone }}</a>
                                        </div>
                                    @endif
                                    @if($order->order_status !== 'delivered' && $order->order_status !== 'cancelled')
                                        <button class="btn btn-xs btn-outline-secondary font-weight-bold w-100 mt-2 py-0" style="font-size: 11px;" onclick="openAssignRiderModal({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->rider_id }}')">
                                            <i class="fas fa-exchange-alt mr-1"></i> Change Rider
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-user-slash mr-1"></i> No Rider Assigned
                                </div>
                                @if($order->order_status !== 'delivered' && $order->order_status !== 'cancelled')
                                    <button class="btn btn-warning btn-sm font-weight-bold text-dark shadow-sm py-1 px-2" onclick="openAssignRiderModal({{ $order->id }}, '{{ $order->order_number }}')">
                                        <i class="fas fa-motorcycle mr-1"></i> Assign Rider
                                    </button>
                                @endif
                            @endif
                        </td>

                        {{-- 5. Ordered Items --}}
                        <td class="align-top py-3">
                            <div class="small font-weight-bold text-muted mb-1">
                                {{ $totalItemsCount }} {{ Str::plural('item', $totalItemsCount) }}:
                            </div>
                            <ul class="list-unstyled mb-0" style="max-width: 280px; white-space: normal;">
                                @foreach($order->items as $item)
                                <li class="mb-1 d-flex justify-content-between align-items-center border-bottom pb-1">
                                    <span>
                                        <span class="badge badge-light border font-weight-bold text-dark px-2 mr-1">
                                            {{ $item->quantity }}x
                                        </span>
                                        <strong class="text-dark small">{{ $item->product_name }}</strong>
                                    </span>
                                    <span class="text-muted small font-weight-bold ml-2">
                                        Rs. {{ number_format($item->total) }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </td>

                        {{-- 6. Amount & Status Badge --}}
                        <td class="align-top py-3">
                            <div class="h5 font-weight-bold text-success mb-1">
                                Rs. {{ number_format($order->total) }}
                            </div>
                            <div class="small mb-1">
                                <span class="badge badge-light border font-weight-bold text-dark px-2 py-1">
                                    <i class="fas fa-money-bill-wave text-success mr-1"></i> {{ $order->payment_method ?: 'COD' }}
                                </span>
                            </div>
                            <div class="small text-muted mb-2">
                                Delivery: <strong>{{ $order->delivery_charges > 0 ? 'Rs. ' . number_format($order->delivery_charges) : 'FREE' }}</strong>
                            </div>

                            {{-- Status Badge --}}
                            @if($order->order_status === 'pending')
                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1 d-inline-block">
                                    <i class="fas fa-clock mr-1"></i> New Pending
                                </span>
                            @elseif($order->order_status === 'processing')
                                <span class="badge badge-primary font-weight-bold px-2 py-1 d-inline-block">
                                    <i class="fas fa-fire mr-1"></i> In Kitchen
                                </span>
                            @elseif($order->order_status === 'shipped')
                                <span class="badge badge-info text-white font-weight-bold px-2 py-1 d-inline-block">
                                    <i class="fas fa-motorcycle mr-1"></i> With Rider
                                </span>
                            @elseif($order->order_status === 'delivered')
                                <span class="badge badge-success font-weight-bold px-2 py-1 d-inline-block">
                                    <i class="fas fa-check-circle mr-1"></i> Delivered & Paid
                                </span>
                            @else
                                <span class="badge badge-danger font-weight-bold px-2 py-1 d-inline-block">
                                    <i class="fas fa-times-circle mr-1"></i> Cancelled
                                </span>
                            @endif
                        </td>

                        {{-- 7. Status & Dispatch Actions --}}
                        <td class="align-top py-3 text-center">
                            <div class="d-flex flex-column gap-1 align-items-center">
                                @if($order->order_status === 'pending')
                                    <button class="btn btn-primary btn-sm font-weight-bold w-100 shadow-sm" onclick="updateOrderStatus({{ $order->id }}, 'processing')">
                                        <i class="fas fa-fire mr-1"></i> Send to Kitchen
                                    </button>
                                    <button class="btn btn-outline-warning text-dark btn-sm font-weight-bold w-100 mt-1" onclick="openAssignRiderModal({{ $order->id }}, '{{ $order->order_number }}')">
                                        <i class="fas fa-motorcycle mr-1"></i> Assign Rider
                                    </button>

                                @elseif($order->order_status === 'processing')
                                    <button class="btn btn-warning btn-sm font-weight-bold text-dark w-100 shadow-sm" onclick="openAssignRiderModal({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->rider_id }}')">
                                        <i class="fas fa-motorcycle mr-1"></i> Dispatch with Rider
                                    </button>

                                @elseif($order->order_status === 'shipped')
                                    <button class="btn btn-success btn-sm font-weight-bold w-100 shadow-sm" onclick="openCompleteSettleModal({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->total }}', '{{ addslashes($order->rider_name ?? '') }}')">
                                        <i class="fas fa-check-double mr-1"></i> Mark Delivered & Collect Rs. {{ number_format($order->total) }}
                                    </button>

                                @elseif($order->order_status === 'delivered')
                                    <div class="text-success small font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i> Logged into Sales & Cashbook
                                    </div>
                                    <span class="badge badge-light border text-muted small mt-1">
                                        Stock Deducted
                                    </span>
                                @else
                                    <span class="text-danger small font-weight-bold">
                                        <i class="fas fa-ban mr-1"></i> Order Cancelled
                                    </span>
                                @endif

                                <button class="btn btn-outline-primary btn-sm font-weight-bold w-100 mt-1" onclick="openEditOrderModal({{ $order->id }})" title="Edit Order items, customer, discount or charges">
                                    <i class="fas fa-edit mr-1"></i> Edit Order
                                </button>

                                @if($order->order_status !== 'delivered' && $order->order_status !== 'cancelled')
                                    <button class="btn btn-outline-danger btn-sm font-weight-bold w-100 mt-1 py-0" style="font-size: 11px;" onclick="updateOrderStatus({{ $order->id }}, 'cancelled')">
                                        <i class="fas fa-times mr-1"></i> Cancel Order
                                    </button>
                                @endif
                            </div>
                        </td>

                        {{-- 8. Print Actions --}}
                        <td class="align-top py-3 text-right">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-secondary font-weight-bold" onclick="printSlip({{ $order->id }})" title="Print 80mm Delivery Thermal Slip">
                                    <i class="fas fa-receipt mr-1"></i> Slip
                                </button>
                                <button class="btn btn-outline-dark font-weight-bold" onclick="printKot({{ $order->id }})" title="Print Kitchen Order Ticket (KOT)">
                                    <i class="fas fa-print mr-1"></i> KOT
                                </button>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="display-4 text-muted mb-3"><i class="fas fa-shopping-bag"></i></div>
                            <h5 class="text-dark font-weight-bold">No online orders found</h5>
                            <p class="text-muted small mb-0">Customer orders placed from the website will automatically appear here live.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>

{{-- MODAL: ASSIGN RIDER --}}
<div class="modal fade" id="assignRiderModal" tabindex="-1" role="dialog" aria-labelledby="assignRiderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="assignRiderModalLabel">
                    <i class="fas fa-motorcycle text-warning mr-2"></i> Assign Delivery Rider
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="$('#assignRiderModal').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assignRiderForm" onsubmit="submitAssignRider(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="modalOrderId" name="order_id" value="">

                    <div class="alert alert-light border mb-3">
                        <span class="small text-muted font-weight-bold d-block">ORDER NUMBER:</span>
                        <span class="h6 font-weight-bold text-primary mb-0" id="modalOrderNumber">#ORD-0000</span>
                    </div>

                    {{-- Rider Selection Dropdown --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">
                            Select Existing Rider:
                        </label>
                        <select class="form-control" id="modalRiderSelect" name="rider_id" onchange="toggleNewRiderInputs(this.value)">
                            <option value="">-- Choose Active Rider --</option>
                            @foreach($riders as $r)
                                <option value="{{ $r->id }}">
                                    🛵 {{ $r->name }} ({{ $r->phone ?: 'No Phone' }}) &bull; [{{ ucfirst(str_replace('_', ' ', $r->duty_status)) }}]
                                </option>
                            @endforeach
                            <option value="new" class="font-weight-bold text-primary">➕ Register & Assign New Rider...</option>
                        </select>
                    </div>

                    {{-- Dynamic New Rider Fields --}}
                    <div id="newRiderFields" style="display: none;" class="p-3 bg-light rounded border mb-3">
                        <div class="small font-weight-bold text-primary mb-2">
                            <i class="fas fa-user-plus mr-1"></i> New Rider Quick Registration:
                        </div>
                        <div class="form-group mb-2">
                            <label class="small text-muted font-weight-bold mb-1">Rider Full Name *</label>
                            <input type="text" class="form-control form-control-sm" id="newRiderName" name="new_rider_name" placeholder="e.g. Muhammad Ali">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small text-muted font-weight-bold mb-1">Rider Phone / WhatsApp *</label>
                            <input type="text" class="form-control form-control-sm" id="newRiderPhone" name="new_rider_phone" placeholder="e.g. 0300-1234567">
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle text-info mr-1"></i>
                        Assigning a rider will mark the order as <strong>With Rider (Dispatched)</strong>, update rider duty, and sync with the Restaurant Deliveries system.
                    </p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" onclick="$('#assignRiderModal').modal('hide')">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4" id="btnConfirmAssignRider">
                        <i class="fas fa-check mr-1"></i> Assign & Dispatch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: UNIFIED COMPLETE & SETTLE MODAL (DELIVERY COLLECTION WITH ACCOUNT SELECTION) --}}
<div class="modal fade" id="completeSettleModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3 px-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-5 mr-2"></i>
                    <div>
                        <h6 class="modal-title font-weight-bold mb-0 text-white">Complete & Settle Delivery Order</h6>
                        <small style="opacity: 0.9;" id="csModalSubtitle">Invoice: #</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" onclick="$('#completeSettleModal').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCompleteSettle" onsubmit="submitCompleteSettle(event)">
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" id="csOrderId">

                    <!-- Total Amount Display Banner -->
                    <div class="p-3 rounded bg-light border text-center mb-3">
                        <span class="text-muted small text-uppercase font-weight-bold">Total Cash to Collect</span>
                        <div class="h2 font-weight-bold text-success mt-1 mb-0" id="csTotalPayable">Rs. 0</div>
                    </div>

                    <!-- PROMINENT ACCOUNT SELECTION CONTAINER -->
                    <div class="p-3 rounded mb-3" style="background: #eff6ff; border: 2px solid #3b82f6;">
                        <label class="form-label font-weight-bold text-dark d-flex align-items-center justify-content-between mb-2">
                            <span class="h6 mb-0 font-weight-bold text-primary"><i class="fas fa-wallet mr-2"></i>Payment Receiving Account</span>
                            <span class="badge badge-primary text-white" style="font-size: 0.72rem;">Active Mode</span>
                        </label>
                        <select class="form-control form-control-lg font-weight-bold shadow-sm" id="csAccountSelect" required style="border: 2px solid #2563eb; background-color: #ffffff; color: #1e3a8a; font-size: 1rem; height: 48px; border-radius: 8px;">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>
                                    💵 {{ $acc->title }} ({{ $acc->account_number ?: 'General Account' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">
                            <i class="fas fa-info-circle text-primary mr-1"></i>Select which Cash Drawer or Bank Account receives this delivery cash.
                        </small>
                    </div>

                    <!-- Cash Given & Change Calculation -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small font-weight-bold text-dark mb-1">Cash Received (Rs.)</label>
                            <input type="number" step="any" min="0" class="form-control form-control-lg font-weight-bold text-primary" id="csCashReceived" placeholder="0" oninput="calculateCsChange()" style="font-size: 1.1rem;">
                        </div>
                        <div class="col-6">
                            <label class="small font-weight-bold text-dark mb-1">Change Return (Rs.)</label>
                            <input type="text" class="form-control form-control-lg font-weight-bold bg-white text-danger" id="csChangeReturn" value="0.00" readonly style="font-size: 1.1rem;">
                        </div>
                    </div>

                    <!-- Optional Extra Discount -->
                    <div class="mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Extra Discount (if any)</label>
                        <input type="number" step="any" min="0" class="form-control" id="csDiscount" placeholder="0.00" oninput="calculateCsChange()">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" onclick="$('#completeSettleModal').modal('hide')">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4 py-2" id="btnSubmitCs">
                        <i class="fas fa-check-double mr-1"></i> Confirm & Settle Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: EDIT ONLINE ORDER --}}
<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <h5 class="modal-title font-weight-bold mb-0" id="editOrderModalLabel">
                        <i class="fas fa-edit mr-2"></i> Edit Online Order
                    </h5>
                    <span class="badge badge-light text-primary font-weight-bold ml-2 px-2 py-1" id="editModalOrderBadge" style="font-size: 14px;">#ORD-0000</span>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="$('#editOrderModal').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="editModalOrderId" value="">

                <div class="row">
                    {{-- Left Column: Customer & Delivery Details --}}
                    <div class="col-lg-5 col-md-12 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-2 border-bottom">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-user text-primary mr-1"></i> Customer & Delivery Details
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="form-group mb-2">
                                    <label class="small text-muted font-weight-bold mb-1">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="editShippingName" placeholder="Customer full name">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small text-muted font-weight-bold mb-1">Phone / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="editShippingPhone" placeholder="0300-1234567">
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small text-muted font-weight-bold mb-1">Delivery Address</label>
                                    <textarea class="form-control form-control-sm" id="editShippingAddress" rows="2" placeholder="Full street address, house/flat #..."></textarea>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="small text-muted font-weight-bold mb-1">Payment Method</label>
                                        <select class="form-control form-control-sm" id="editPaymentMethod">
                                            <option value="COD">Cash on Delivery (COD)</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="JazzCash / EasyPaisa">JazzCash / EasyPaisa</option>
                                            <option value="Card">Credit / Debit Card</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted font-weight-bold mb-1">Order Status</label>
                                        <select class="form-control form-control-sm" id="editOrderStatus">
                                            <option value="pending">Pending (New)</option>
                                            <option value="processing">In Kitchen</option>
                                            <option value="shipped">With Rider</option>
                                            <option value="delivered">Delivered & Paid</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="small text-muted font-weight-bold mb-1">Order Notes / Instructions</label>
                                    <input type="text" class="form-control form-control-sm" id="editOrderNotes" placeholder="e.g. Extra ketchup, Less spicy...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Items & Billing Summary --}}
                    <div class="col-lg-7 col-md-12 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-utensils text-primary mr-1"></i> Ordered Items (<span id="editItemsCountBadge">0</span>)
                                </h6>
                            </div>

                            <div class="card-body p-3">
                                {{-- Add Item Bar --}}
                                <div class="p-2 mb-3 bg-light rounded border">
                                    <div class="small font-weight-bold text-muted mb-1">
                                        <i class="fas fa-plus-circle text-success mr-1"></i> Add Dish / Item to Order:
                                    </div>
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-5 col-12 mb-1 mb-md-0">
                                            <label class="small text-muted mb-0">Select Item:</label>
                                            <select class="form-control form-control-sm" id="editProductSelect" onchange="onEditProductSelect(this)">
                                                <option value="">-- Choose Menu Item --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <label class="small text-muted mb-0">Price (Rs):</label>
                                            <input type="number" class="form-control form-control-sm" id="editNewItemPrice" placeholder="Price" min="0">
                                        </div>
                                        <div class="col-md-2 col-3">
                                            <label class="small text-muted mb-0">Qty:</label>
                                            <input type="number" class="form-control form-control-sm" id="editNewItemQty" value="1" min="1">
                                        </div>
                                        <div class="col-md-2 col-3">
                                            <button type="button" class="btn btn-primary btn-sm font-weight-bold w-100" onclick="addEditItemRow()">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Items Table --}}
                                <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered mb-0 align-middle">
                                        <thead class="thead-light">
                                            <tr class="small font-weight-bold text-muted">
                                                <th>Item Name</th>
                                                <th style="width: 100px;">Price (Rs)</th>
                                                <th style="width: 110px;" class="text-center">Qty</th>
                                                <th style="width: 105px;" class="text-right">Total</th>
                                                <th style="width: 45px;" class="text-center">Del</th>
                                            </tr>
                                        </thead>
                                        <tbody id="editOrderItemsTableBody">
                                            {{-- Rendered via JS --}}
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Calculations Summary --}}
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <div class="row align-items-center">
                                        <div class="col-6 small font-weight-bold text-muted">Subtotal (Items):</div>
                                        <div class="col-6 text-right font-weight-bold text-dark h6 mb-1">
                                            Rs. <span id="editSubtotalText">0</span>
                                        </div>

                                        <div class="col-6 small font-weight-bold text-muted mt-2">Delivery Charges:</div>
                                        <div class="col-6 text-right mt-2">
                                            <div class="input-group input-group-sm justify-content-end" style="width: 130px; margin-left: auto;">
                                                <div class="input-group-prepend"><span class="input-group-text">Rs</span></div>
                                                <input type="number" class="form-control form-control-sm text-right font-weight-bold" id="editDeliveryCharges" value="0" min="0" oninput="recalcEditTotals()">
                                            </div>
                                        </div>

                                        <div class="col-6 small font-weight-bold text-muted mt-2">Discount:</div>
                                        <div class="col-6 text-right mt-2">
                                            <div class="input-group input-group-sm justify-content-end" style="width: 130px; margin-left: auto;">
                                                <div class="input-group-prepend"><span class="input-group-text">Rs</span></div>
                                                <input type="number" class="form-control form-control-sm text-right font-weight-bold text-danger" id="editDiscount" value="0" min="0" oninput="recalcEditTotals()">
                                            </div>
                                        </div>

                                        <div class="col-12"><hr class="my-2"></div>

                                        <div class="col-6 font-weight-bold text-dark h5 mb-0">Total Payable:</div>
                                        <div class="col-6 text-right font-weight-bold text-success h4 mb-0">
                                            Rs. <span id="editTotalText">0</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between align-items-center">
                <span class="small text-muted">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Saving will update Order, recalculate bill & live sync with Sales & KOT.
                </span>
                <div>
                    <button type="button" class="btn btn-secondary font-weight-bold px-3 mr-2" data-dismiss="modal" onclick="$('#editOrderModal').modal('hide')">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success font-weight-bold px-4" id="btnSaveOrderEdit" onclick="submitEditOrder()">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Audio chime for new incoming orders --}}
<audio id="orderChime" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
</audio>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let lastKnownOrderId = {{ $latestOrderId }};
    let refreshSeconds = 15;

    // Countdown and background poll
    setInterval(() => {
        refreshSeconds--;
        const timerEl = document.getElementById('refreshTimer');
        if (timerEl) timerEl.innerText = refreshSeconds + 's';

        if (refreshSeconds <= 0) {
            refreshSeconds = 15;
            checkNewOrdersLive();
        }
    }, 1000);

    // Live AJAX check for new incoming orders
    async function checkNewOrdersLive() {
        try {
            const res = await fetch("{{ route('restaurant.online_orders.stats') }}");
            const data = await res.json();

            // Check if there is a newer order ID
            if (data.latest_order_id > lastKnownOrderId) {
                lastKnownOrderId = data.latest_order_id;
                
                // Play audio chime
                const chime = document.getElementById('orderChime');
                if (chime) {
                    chime.play().catch(e => console.log('Audio autoplay prevented'));
                }

                // Show Toast and reload
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '🔔 New Online Order Received!',
                        text: `Order #${data.latest_order_number} by ${data.latest_customer} (${data.latest_total})`,
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                }

                // Reload order list table to show new order
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } catch (e) {
            console.log('Background poll error:', e);
        }
    }

    // Open Assign Rider Modal
    function openAssignRiderModal(orderId, orderNumber, currentRiderId = '') {
        document.getElementById('modalOrderId').value = orderId;
        document.getElementById('modalOrderNumber').innerText = '#' + orderNumber;
        
        const riderSelect = document.getElementById('modalRiderSelect');
        if (currentRiderId && riderSelect) {
            riderSelect.value = currentRiderId;
        } else if (riderSelect) {
            riderSelect.value = '';
        }
        
        toggleNewRiderInputs(riderSelect ? riderSelect.value : '');
        $('#assignRiderModal').modal('show');
    }

    // Toggle new rider inputs when selecting "Register New Rider"
    function toggleNewRiderInputs(value) {
        const fields = document.getElementById('newRiderFields');
        if (!fields) return;
        if (value === 'new') {
            fields.style.display = 'block';
            document.getElementById('newRiderName').focus();
        } else {
            fields.style.display = 'none';
        }
    }

    // Submit Assign Rider AJAX
    async function submitAssignRider(e) {
        e.preventDefault();
        const orderId = document.getElementById('modalOrderId').value;
        const riderId = document.getElementById('modalRiderSelect').value;
        const newName = document.getElementById('newRiderName').value;
        const newPhone = document.getElementById('newRiderPhone').value;

        if (!riderId) {
            alert('Please select a rider or choose "Register New Rider".');
            return;
        }

        if (riderId === 'new' && !newName.trim()) {
            alert('Please enter the new rider name.');
            return;
        }

        const btn = document.getElementById('btnConfirmAssignRider');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Assigning...';

        try {
            const res = await fetch(`{{ url('restaurant/online-orders') }}/${orderId}/assign-rider`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rider_id: riderId,
                    new_rider_name: newName,
                    new_rider_phone: newPhone
                })
            });

            const data = await res.json();
            if (data.success) {
                $('#assignRiderModal').modal('hide');
                location.reload();
            } else {
                alert(data.message || 'Error assigning rider');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i> Assign & Dispatch';
            }
        } catch (err) {
            alert('Network error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Assign & Dispatch';
        }
    }

    // UNIFIED COMPLETE & SETTLE MODAL (DELIVERY COLLECTION)
    let currentPayable = 0;

    function openCompleteSettleModal(orderId, orderNumber, payable, riderName) {
        $('#csOrderId').val(orderId);
        let riderInfo = riderName ? ` &bull; Rider: ${riderName}` : '';
        $('#csModalSubtitle').html(`Invoice: #${orderNumber}${riderInfo}`);
        currentPayable = parseFloat(payable) || 0;
        $('#csTotalPayable').text('Rs. ' + currentPayable.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#csCashReceived').val(currentPayable);
        $('#csDiscount').val('');
        $('#csChangeReturn').val('0.00');
        $('#completeSettleModal').modal('show');
        setTimeout(() => {
            $('#csCashReceived').focus().select();
        }, 400);
    }

    function calculateCsChange() {
        let disc = parseFloat($('#csDiscount').val()) || 0;
        let adjustedPayable = Math.max(0, currentPayable - disc);
        $('#csTotalPayable').text('Rs. ' + adjustedPayable.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        let given = parseFloat($('#csCashReceived').val()) || 0;
        let change = Math.max(0, given - adjustedPayable);
        $('#csChangeReturn').val(change.toFixed(2));
    }

    async function submitCompleteSettle(e) {
        e.preventDefault();
        let orderId = $('#csOrderId').val();
        let accountId = $('#csAccountSelect').val();
        let cashReceived = parseFloat($('#csCashReceived').val()) || 0;
        let discount = parseFloat($('#csDiscount').val()) || 0;

        if (!accountId) {
            Swal.fire('Select Account', 'Please choose payment receiving account.', 'warning');
            return;
        }

        $('#btnSubmitCs').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Settling...');

        try {
            const res = await fetch(`{{ url('restaurant/online-orders') }}/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: 'delivered',
                    account_id: accountId,
                    cash_received: cashReceived,
                    discount: discount
                })
            });

            const data = await res.json();
            $('#completeSettleModal').modal('hide');
            $('#btnSubmitCs').prop('disabled', false).html('<i class="fas fa-check-double mr-1"></i> Confirm & Settle Order');

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Delivered & Settled!',
                    text: data.message,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print mr-1"></i> Print Receipt',
                    cancelButtonText: 'Done',
                    confirmButtonColor: '#10b981',
                }).then((result) => {
                    if (result.isConfirmed && data.receipt_url) {
                        window.open(data.receipt_url, '_blank', 'width=420,height=650,top=100,left=100,toolbar=no,scrollbars=yes');
                    }
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Error settling order', 'error');
            }
        } catch (err) {
            $('#btnSubmitCs').prop('disabled', false).html('<i class="fas fa-check-double mr-1"></i> Confirm & Settle Order');
            Swal.fire('Error', 'Network error: ' + err.message, 'error');
        }
    }

    // Fallback for markOrderDelivered
    function markOrderDelivered(orderId, orderNumber, amount, riderName) {
        openCompleteSettleModal(orderId, orderNumber, amount, riderName);
    }

    // Update Status
    async function updateOrderStatus(orderId, status) {
        if (status === 'cancelled') {
            const conf = confirm('Are you sure you want to cancel this order?');
            if (!conf) return;
        }

        try {
            const res = await fetch(`{{ url('restaurant/online-orders') }}/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            });

            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error updating order');
            }
        } catch (e) {
            alert('Network error: ' + e.message);
        }
    }

    // Print Receipt Popup
    function printSlip(orderId) {
        const url = `{{ url('restaurant/online-orders') }}/${orderId}/slip`;
        window.open(url, '_blank', 'width=420,height=650,top=100,left=100,toolbar=no,scrollbars=yes');
    }

    // Print KOT Popup
    function printKot(orderId) {
        const url = `{{ url('restaurant/online-orders') }}/${orderId}/kot`;
        window.open(url, '_blank', 'width=420,height=600,top=100,left=100,toolbar=no,scrollbars=yes');
    }

    // ==========================================
    // EDIT ONLINE ORDER JAVASCRIPT LOGIC
    // ==========================================
    let editModalItems = [];
    let editAvailableProducts = [];

    async function openEditOrderModal(orderId) {
        try {
            const res = await fetch(`{{ url('restaurant/online-orders') }}/${orderId}/details`);
            const data = await res.json();

            if (!data.success) {
                alert(data.message || 'Error fetching order details');
                return;
            }

            const order = data.order;
            editAvailableProducts = data.products || [];

            // Set inputs
            document.getElementById('editModalOrderId').value = order.id;
            document.getElementById('editModalOrderBadge').innerText = '#' + order.order_number;
            document.getElementById('editShippingName').value = order.shipping_name || '';
            document.getElementById('editShippingPhone').value = order.shipping_phone || '';
            document.getElementById('editShippingAddress').value = order.shipping_address || '';
            document.getElementById('editPaymentMethod').value = order.payment_method || 'COD';
            document.getElementById('editOrderStatus').value = order.order_status || 'pending';
            document.getElementById('editOrderNotes').value = order.order_notes || '';
            document.getElementById('editDeliveryCharges').value = order.delivery_charges || 0;
            document.getElementById('editDiscount').value = order.discount || 0;

            // Populate Products Select
            const prodSelect = document.getElementById('editProductSelect');
            prodSelect.innerHTML = '<option value="">-- Choose Menu Item --</option>';
            editAvailableProducts.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.dataset.price = p.retail_price;
                opt.dataset.name = p.item_name;
                opt.textContent = `${p.item_name} (Rs. ${parseFloat(p.retail_price).toLocaleString()})`;
                prodSelect.appendChild(opt);
            });

            // Populate Items
            editModalItems = (order.items || []).map(it => ({
                product_id: it.product_id,
                product_name: it.product_name,
                price: parseFloat(it.price) || 0,
                quantity: parseInt(it.quantity) || 1,
                total: (parseFloat(it.price) || 0) * (parseInt(it.quantity) || 1)
            }));

            renderEditItemsTable();
            recalcEditTotals();

            $('#editOrderModal').modal('show');
        } catch (err) {
            alert('Network error loading order: ' + err.message);
        }
    }

    function onEditProductSelect(select) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            document.getElementById('editNewItemPrice').value = selectedOption.dataset.price || 0;
        } else {
            document.getElementById('editNewItemPrice').value = '';
        }
    }

    function addEditItemRow() {
        const prodSelect = document.getElementById('editProductSelect');
        const selectedOption = prodSelect.options[prodSelect.selectedIndex];
        const priceInput = document.getElementById('editNewItemPrice');
        const qtyInput = document.getElementById('editNewItemQty');

        let prodId = null;
        let prodName = '';
        let price = parseFloat(priceInput.value) || 0;
        let qty = parseInt(qtyInput.value) || 1;

        if (!selectedOption || !selectedOption.value) {
            alert('Please select a menu item to add.');
            return;
        }

        prodId = selectedOption.value;
        prodName = selectedOption.dataset.name;

        // Check if already in items
        const existingIndex = editModalItems.findIndex(i => i.product_id == prodId);
        if (existingIndex >= 0) {
            editModalItems[existingIndex].quantity += qty;
            editModalItems[existingIndex].total = editModalItems[existingIndex].quantity * editModalItems[existingIndex].price;
        } else {
            editModalItems.push({
                product_id: prodId,
                product_name: prodName,
                price: price,
                quantity: qty,
                total: price * qty
            });
        }

        // Reset add inputs
        prodSelect.value = '';
        priceInput.value = '';
        qtyInput.value = '1';

        renderEditItemsTable();
        recalcEditTotals();
    }

    function removeEditItemRow(index) {
        if (editModalItems.length <= 1) {
            alert('An order must have at least one item.');
            return;
        }
        editModalItems.splice(index, 1);
        renderEditItemsTable();
        recalcEditTotals();
    }

    function updateEditItemQty(index, newQty) {
        newQty = parseInt(newQty) || 1;
        if (newQty < 1) newQty = 1;
        editModalItems[index].quantity = newQty;
        editModalItems[index].total = editModalItems[index].quantity * editModalItems[index].price;
        renderEditItemsTable();
        recalcEditTotals();
    }

    function updateEditItemPrice(index, newPrice) {
        newPrice = parseFloat(newPrice) || 0;
        if (newPrice < 0) newPrice = 0;
        editModalItems[index].price = newPrice;
        editModalItems[index].total = editModalItems[index].quantity * editModalItems[index].price;
        renderEditItemsTable();
        recalcEditTotals();
    }

    function renderEditItemsTable() {
        const tbody = document.getElementById('editOrderItemsTableBody');
        document.getElementById('editItemsCountBadge').innerText = editModalItems.length;

        if (editModalItems.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No items in order</td></tr>';
            return;
        }

        let html = '';
        editModalItems.forEach((item, idx) => {
            html += `
                <tr>
                    <td class="align-middle">
                        <input type="text" class="form-control form-control-sm font-weight-bold" value="${item.product_name}" onchange="editModalItems[${idx}].product_name = this.value">
                    </td>
                    <td class="align-middle">
                        <input type="number" class="form-control form-control-sm text-right" value="${item.price}" min="0" onchange="updateEditItemPrice(${idx}, this.value)">
                    </td>
                    <td class="align-middle text-center">
                        <div class="input-group input-group-sm" style="width: 100px; margin: 0 auto;">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary px-2" type="button" onclick="updateEditItemQty(${idx}, ${item.quantity - 1})">-</button>
                            </div>
                            <input type="number" class="form-control form-control-sm text-center p-0 font-weight-bold" value="${item.quantity}" min="1" onchange="updateEditItemQty(${idx}, this.value)">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary px-2" type="button" onclick="updateEditItemQty(${idx}, ${item.quantity + 1})">+</button>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-right font-weight-bold text-dark">
                        Rs. ${(item.quantity * item.price).toLocaleString()}
                    </td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2" onclick="removeEditItemRow(${idx})" title="Remove item">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function recalcEditTotals() {
        let subtotal = 0;
        editModalItems.forEach(item => {
            subtotal += (item.quantity * item.price);
        });

        const deliveryCharges = parseFloat(document.getElementById('editDeliveryCharges').value) || 0;
        const discount = parseFloat(document.getElementById('editDiscount').value) || 0;
        const total = Math.max(0, subtotal + deliveryCharges - discount);

        document.getElementById('editSubtotalText').innerText = subtotal.toLocaleString();
        document.getElementById('editTotalText').innerText = total.toLocaleString();
    }

    async function submitEditOrder() {
        const orderId = document.getElementById('editModalOrderId').value;
        const shippingName = document.getElementById('editShippingName').value.trim();
        const shippingPhone = document.getElementById('editShippingPhone').value.trim();
        const shippingAddress = document.getElementById('editShippingAddress').value.trim();
        const paymentMethod = document.getElementById('editPaymentMethod').value;
        const orderStatus = document.getElementById('editOrderStatus').value;
        const orderNotes = document.getElementById('editOrderNotes').value.trim();
        const deliveryCharges = parseFloat(document.getElementById('editDeliveryCharges').value) || 0;
        const discount = parseFloat(document.getElementById('editDiscount').value) || 0;

        if (!shippingName) {
            alert('Customer Name is required.');
            return;
        }
        if (!shippingPhone) {
            alert('Customer Phone Number is required.');
            return;
        }
        if (editModalItems.length === 0) {
            alert('Order must contain at least one item.');
            return;
        }

        const btn = document.getElementById('btnSaveOrderEdit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Saving...';

        try {
            const res = await fetch(`{{ url('restaurant/online-orders') }}/${orderId}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    shipping_name: shippingName,
                    shipping_phone: shippingPhone,
                    shipping_address: shippingAddress,
                    order_notes: orderNotes,
                    payment_method: paymentMethod,
                    order_status: orderStatus,
                    delivery_charges: deliveryCharges,
                    discount: discount,
                    items: editModalItems
                })
            });

            const data = await res.json();
            if (data.success) {
                $('#editOrderModal').modal('hide');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Updated!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    alert(data.message);
                    location.reload();
                }
            } else {
                alert(data.message || 'Error updating order');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Changes';
            }
        } catch (err) {
            alert('Network error updating order: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Changes';
        }
    }
</script>

<style>
    .bg-warning-soft {
        background-color: #fffbeb !important;
    }
    .bg-warning-light {
        background-color: #fef3c7;
    }
    .order-table-row {
        transition: background-color 0.15s ease;
    }
    .order-table-row:hover {
        background-color: #f8fafc !important;
    }
    #ordersListTable th {
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    #ordersListTable td {
        vertical-align: top;
    }
</style>
@endsection
