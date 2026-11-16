@extends('admin_panel.layout.app')

@section('content')
<style>
    .ro-header-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-bottom: 16px;
    }
    .ro-stat-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 14px 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: transform 0.15s;
    }
    .ro-stat-card:hover {
        transform: translateY(-2px);
    }
    .ro-stat-val {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .ro-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ro-tab-nav {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .ro-tab-btn {
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-weight: 700;
        font-size: 0.82rem;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .ro-tab-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
        text-decoration: none;
    }
    .ro-tab-btn.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .ro-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .ro-table {
        margin-bottom: 0;
        font-size: 0.84rem;
        vertical-align: middle;
    }
    .ro-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 14px;
        white-space: nowrap;
    }
    .ro-table td {
        padding: 12px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .ro-badge {
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        letter-spacing: 0.3px;
    }
    .badge-paid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-unpaid {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .badge-status-cooking {
        background: #ffedd5;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .badge-status-served {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-status-delivery {
        background: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.82rem;
        transition: all 0.12s;
        cursor: pointer;
    }
    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-action-complete {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        border: none;
        transition: background 0.15s;
    }
    .btn-action-complete:hover {
        background: #15803d;
        color: #ffffff;
    }
    .btn-action-serve {
        background: #0284c7;
        color: #ffffff;
        border: none;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 5px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-action-serve:hover {
        background: #0369a1;
        color: #ffffff;
    }
    .btn-action-dispatch {
        background: #8b5cf6;
        color: #ffffff;
        border: none;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 5px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-action-dispatch:hover {
        background: #7c3aed;
        color: #ffffff;
    }
    .type-pill {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .type-dine-in { background: #e0f2fe; color: #0369a1; }
    .type-takeaway { background: #fef3c7; color: #b45309; }
    .type-delivery { background: #dcfce7; color: #15803d; }
</style>

<div class="main-content-inner pt-3 pb-5">

    <!-- Top Header -->
    <div class="ro-header-card d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-clock text-danger me-2"></i>Active Running Orders</h4>
                <span class="badge bg-danger rounded-pill">{{ $stats['total'] }} Active</span>
            </div>
            <p class="text-muted small mb-0 mt-1">Live tracking of ongoing Dine-In tables, Takeaway tokens, and Deliveries.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3 py-2" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <a href="{{ route('restaurant.pos') }}" class="btn btn-primary btn-sm fw-bold px-3 py-2">
                <i class="fas fa-cash-register me-1"></i> Open Restaurant POS
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-2 mb-3">
        <div class="col-md-3 col-6">
            <div class="ro-stat-card border-start border-4 border-primary">
                <div class="ro-stat-label">Total Running</div>
                <div class="ro-stat-val text-primary mt-1">{{ $stats['total'] }}</div>
                <small class="text-muted">In-progress orders</small>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="ro-stat-card border-start border-4 border-info">
                <div class="ro-stat-label">Dine-In Tables</div>
                <div class="ro-stat-val text-info mt-1">{{ $stats['dine_in'] }}</div>
                <small class="text-muted">Occupied tables</small>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="ro-stat-card border-start border-4 border-warning">
                <div class="ro-stat-label">Takeaways</div>
                <div class="ro-stat-val text-warning mt-1">{{ $stats['takeaway'] }}</div>
                <small class="text-muted">Counter tokens</small>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="ro-stat-card border-start border-4 border-success">
                <div class="ro-stat-label">Deliveries</div>
                <div class="ro-stat-val text-success mt-1">{{ $stats['delivery'] }}</div>
                <small class="text-muted">Home deliveries</small>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="ro-stat-card border-start border-4 border-danger">
                <div class="ro-stat-label">Unpaid Total Amount</div>
                <div class="ro-stat-val text-danger mt-1">Rs. {{ number_format($stats['unpaid_amount']) }}</div>
                <small class="text-muted">Awaiting settlement</small>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="ro-header-card d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 mb-3">
        <div class="ro-tab-nav">
            <a href="{{ route('restaurant.running_orders', ['type' => 'all', 'search' => request('search')]) }}" class="ro-tab-btn {{ $typeFilter === 'all' ? 'active' : '' }}">
                <i class="fas fa-list"></i> All ({{ $stats['total'] }})
            </a>
            <a href="{{ route('restaurant.running_orders', ['type' => 'dine_in', 'search' => request('search')]) }}" class="ro-tab-btn {{ $typeFilter === 'dine_in' ? 'active' : '' }}">
                <i class="fas fa-chair"></i> Dine-In ({{ $stats['dine_in'] }})
            </a>
            <a href="{{ route('restaurant.running_orders', ['type' => 'takeaway', 'search' => request('search')]) }}" class="ro-tab-btn {{ $typeFilter === 'takeaway' ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i> Takeaway ({{ $stats['takeaway'] }})
            </a>
            <a href="{{ route('restaurant.running_orders', ['type' => 'delivery', 'search' => request('search')]) }}" class="ro-tab-btn {{ $typeFilter === 'delivery' ? 'active' : '' }}">
                <i class="fas fa-motorcycle"></i> Delivery ({{ $stats['delivery'] }})
            </a>
            <a href="{{ route('restaurant.running_orders', ['type' => 'unpaid', 'search' => request('search')]) }}" class="ro-tab-btn {{ $typeFilter === 'unpaid' ? 'active' : '' }}">
                <i class="fas fa-exclamation-circle text-danger"></i> Unpaid Only
            </a>
        </div>

        <form method="GET" action="{{ route('restaurant.running_orders') }}" class="d-flex align-items-center gap-1" style="max-width: 320px; width: 100%;">
            <input type="hidden" name="type" value="{{ $typeFilter }}">
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Search Invoice, Table, Name, Rider..." value="{{ request('search') }}">
                <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('restaurant.running_orders', ['type' => $typeFilter]) }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Running Orders Table (Simple Clean List) -->
    <div class="ro-table-card">
        <div class="table-responsive">
            <table class="table ro-table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Type & Target</th>
                        <th>Customer / Contact</th>
                        <th>Items Preview</th>
                        <th>Total Bill</th>
                        <th>Payment</th>
                        <th>Kitchen / Delivery Status</th>
                        <th>Time</th>
                        <th class="text-end" style="min-width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        @php
                            $isPaid = (float)$o->cash >= (float)$o->total_net && (float)$o->total_net > 0;
                            $orderStatus = $o->order_status ?: 'running';
                            if ($o->order_type === 'delivery' && $o->delivery_status === 'dispatched') {
                                $orderStatus = 'on_delivery';
                            }
                            $custName = $o->walkin_name ?: (optional($o->customer_relation)->customer_name ?: 'Walk-in');
                            $custPhone = optional($o->customer_relation)->phone_number ?: ($o->rider_phone ?: '-');
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold text-dark cursor-pointer text-decoration-underline" onclick="viewOrderDetails({{ $o->id }})">#{{ $o->invoice_no }}</span>
                            </td>
                            <td>
                                @if($o->order_type === 'dine_in')
                                    <span class="type-pill type-dine-in"><i class="fas fa-chair"></i> Table {{ $o->table_no ?: 'T-1' }}</span>
                                @elseif($o->order_type === 'takeaway')
                                    <span class="type-pill type-takeaway"><i class="fas fa-shopping-bag"></i> Token #{{ $o->token_no ?: '1' }}</span>
                                @else
                                    <span class="type-pill type-delivery"><i class="fas fa-motorcycle"></i> Delivery</span>
                                    @if($o->rider_name)
                                        <div class="small text-muted mt-1"><i class="fas fa-user-tag"></i> {{ $o->rider_name }}</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $custName }}</div>
                                @if($custPhone && $custPhone !== '-')
                                    <small class="text-muted"><i class="fas fa-phone-alt" style="font-size: 10px;"></i> {{ $custPhone }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $o->items->pluck('product.item_name')->implode(', ') }}">
                                    <span class="badge bg-light text-dark border me-1">{{ $o->items->count() }} items</span>
                                    <small class="text-muted">
                                        @foreach($o->items->take(2) as $it)
                                            {{ (int)($it->total_pieces ?: $it->qty) }}x {{ $it->product->item_name ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                        @if($o->items->count() > 2)...@endif
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">Rs. {{ number_format($o->total_net ?: $o->total_bill_amount) }}</span>
                            </td>
                            <td>
                                @if($isPaid)
                                    <span class="ro-badge badge-paid"><i class="fas fa-check-circle"></i> PAID</span>
                                @else
                                    <span class="ro-badge badge-unpaid"><i class="fas fa-clock"></i> UNPAID</span>
                                @endif
                            </td>
                            <td>
                                @if($orderStatus === 'served')
                                    <span class="ro-badge badge-status-served"><i class="fas fa-concierge-bell"></i> Served</span>
                                @elseif($orderStatus === 'on_delivery')
                                    <span class="ro-badge badge-status-delivery"><i class="fas fa-shipping-fast"></i> On Delivery</span>
                                @else
                                    <span class="ro-badge badge-status-cooking"><i class="fas fa-fire"></i> Cooking / Prep</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ $o->created_at ? $o->created_at->diffForHumans(null, true) : '-' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- View Details Modal -->
                                    <button type="button" class="btn-action-icon" onclick="viewOrderDetails({{ $o->id }})" title="View Details">
                                        <i class="fas fa-eye text-primary"></i>
                                    </button>

                                    <!-- Edit / Recall into POS -->
                                    <a href="{{ route('restaurant.pos') }}?recall_id={{ $o->id }}" class="btn-action-icon" title="Edit / Add Items in POS">
                                        <i class="fas fa-edit text-info"></i>
                                    </a>

                                    <!-- Kitchen Slip (KOT) -->
                                    <button type="button" class="btn-action-icon" onclick="printThermalWindow('{{ route('sales.kot', $o->id) }}')" title="Re-print Kitchen KOT">
                                        <i class="fas fa-utensils text-warning"></i>
                                    </button>

                                    <!-- Provisional Bill / Slip Print (Pre-payment) -->
                                    <button type="button" class="btn-action-icon" onclick="printThermalWindow('{{ route('restaurant.bill_slip', $o->id) }}')" title="Print Provisional Bill (Guest Check)">
                                        <i class="fas fa-file-invoice text-secondary"></i>
                                    </button>

                                    <!-- Dine-In Serve Action (No waiter required) -->
                                    @if($o->order_type === 'dine_in' && $orderStatus !== 'served')
                                        <button type="button" class="btn-action-serve" onclick="markOrderServed({{ $o->id }})" title="Mark Served to Table">
                                            <i class="fas fa-check"></i> Serve
                                        </button>
                                    @endif

                                    <!-- Delivery Assign Rider Action -->
                                    @if($o->order_type === 'delivery' && $orderStatus !== 'on_delivery')
                                        <button type="button" class="btn-action-dispatch" onclick="openAssignRiderModal({{ $o->id }}, '{{ $o->invoice_no }}', '{{ $o->rider_id }}')" title="Assign Rider & Dispatch">
                                            <i class="fas fa-motorcycle"></i> Deliver
                                        </button>
                                    @endif

                                    <!-- Unified Complete & Settle Action -->
                                    <button type="button" class="btn-action-complete" onclick="openCompleteSettleModal({{ $o->id }}, '{{ $o->invoice_no }}', {{ (float)($o->total_net ?: $o->total_bill_amount) }})" title="Complete Order & Receive Payment">
                                        <i class="fas fa-dollar-sign"></i> Complete
                                    </button>

                                    <!-- Cancel Order -->
                                    <button type="button" class="btn-action-icon text-danger" onclick="cancelOrderAction({{ $o->id }}, '{{ $o->invoice_no }}')" title="Cancel Order">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle text-success fs-1 mb-2 d-block"></i>
                                <h6 class="fw-bold mb-1">No Active Running Orders Found</h6>
                                <p class="small text-muted mb-0">All tables are clear, takeaways handed over, and deliveries completed!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<!-- =========================================================
     MODAL 1: ORDER DETAILS MODAL
     ========================================================= -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-receipt fs-5 text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0" id="odModalTitle">Order Details</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="odModalBody">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 mb-0">Loading order details...</p>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" id="btnOdPrintKot">
                        <i class="fas fa-utensils me-1"></i> Kitchen KOT
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" id="btnOdPrintBill">
                        <i class="fas fa-print me-1"></i> Provisional Slip
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL 2: ASSIGN RIDER MODAL (DIRECT RIDERS LIST)
     ========================================================= -->
<div class="modal fade" id="assignRiderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-motorcycle fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white">Select Delivery Rider</h6>
                        <small class="text-white-50" id="arOrderInvoice">Order #</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-light" style="max-height: 65vh; overflow-y: auto;">
                <input type="hidden" id="arOrderId">
                <p class="small text-muted mb-2 fw-semibold">Click any rider below to assign & dispatch order immediately:</p>
                <div class="d-flex flex-column gap-2" id="arRidersListContainer">
                    @forelse($riders as $r)
                        @php
                            $isAvail = strtolower($r->duty_status ?? 'available') === 'available';
                        @endphp
                        <div class="p-2 px-3 rounded-3 bg-white border d-flex justify-content-between align-items-center shadow-sm rider-item-card" 
                             style="cursor: pointer; transition: all 0.15s; border-color: {{ $isAvail ? '#86efac' : '#fed7aa' }} !important;"
                             onclick="submitDirectRiderAssign({{ $r->id }}, '{{ addslashes($r->name) }}')">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: {{ $isAvail ? '#dcfce7' : '#ffedd5' }}; color: {{ $isAvail ? '#16a34a' : '#c2410c' }}; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                    <i class="fas fa-motorcycle"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $r->name }}</div>
                                    <small class="text-muted"><i class="fas fa-phone-alt" style="font-size: 10px;"></i> {{ $r->phone ?: 'No phone' }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($isAvail)
                                    <span class="badge bg-success" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i> Available</span>
                                @else
                                    <span class="badge bg-warning text-dark" style="font-size: 0.72rem;"><i class="fas fa-clock me-1"></i> On Delivery</span>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary fw-bold py-1 px-3" style="font-size: 0.75rem; border-radius: 6px;">
                                    <i class="fas fa-paper-plane me-1"></i> Dispatch
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-motorcycle fs-2 text-secondary mb-2"></i>
                            <p class="mb-0 fw-bold">No Riders Found</p>
                            <small>Please create riders in Rider Management.</small>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer bg-white py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL 3: UNIFIED COMPLETE & SETTLE MODAL (ALL ORDERS)
     ========================================================= -->
<div class="modal fade" id="completeSettleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Complete & Settle Order</h6>
                        <small style="opacity: 0.9;" id="csModalSubtitle">Invoice: #</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCompleteSettle" onsubmit="submitCompleteSettle(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="csOrderId">

                    <!-- Total Amount Display Banner -->
                    <div class="p-3 rounded-3 bg-light border text-center mb-3">
                        <span class="text-muted small text-uppercase fw-bold">Total Payable Amount</span>
                        <div class="fs-2 fw-black text-success mt-1" id="csTotalPayable">Rs. 0</div>
                    </div>

                    <!-- PROMINENT ACCOUNT SELECTION CONTAINER -->
                    <div class="p-3 rounded-3 mb-3" style="background: #eff6ff; border: 2px solid #3b82f6;">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-6"><i class="fas fa-wallet text-primary me-2"></i>Payment Receiving Account</span>
                            <span class="badge bg-primary text-white" style="font-size: 0.72rem;">Active Mode</span>
                        </label>
                        <select class="form-select form-select-lg fw-bold shadow-sm" id="csAccountSelect" required style="border: 2px solid #2563eb; background-color: #ffffff; color: #1e3a8a; font-size: 1rem; height: 48px; border-radius: 8px;">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>
                                    💵 {{ $acc->title }} ({{ $acc->account_number ?: 'General Account' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">
                            <i class="fas fa-info-circle text-primary me-1"></i>Select which Cash Drawer or Bank Account receives this payment.
                        </small>
                    </div>

                    <!-- Cash Given & Change Calculation -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Cash Received (Rs.)</label>
                            <input type="number" step="any" min="0" class="form-control form-control-lg fw-bold text-primary" id="csCashReceived" placeholder="0" oninput="calculateCsChange()" style="font-size: 1.1rem;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Change Return (Rs.)</label>
                            <input type="text" class="form-control form-control-lg fw-bold bg-white text-danger" id="csChangeReturn" value="0.00" readonly style="font-size: 1.1rem;">
                        </div>
                    </div>

                    <!-- Optional Extra Discount -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Extra Discount (if any)</label>
                        <input type="number" step="any" min="0" class="form-control" id="csDiscount" placeholder="0.00" oninput="calculateCsChange()">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 py-2" id="btnSubmitCs">
                        <i class="fas fa-check-double me-1"></i> Confirm & Print Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentPayable = 0;

    // Thermal Print Helper (Silent / Centered Window)
    function printThermalWindow(url) {
        let left = (window.screen.width - 520) / 2;
        let top = (window.screen.height - 750) / 2;
        window.open(url, '_blank', `width=520,height=750,left=${left},top=${top},toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes`);
    }

    // 1. ORDER DETAILS MODAL
    function viewOrderDetails(id) {
        $('#odModalTitle').text(`Loading Order #${id}...`);
        $('#odModalBody').html(`
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 mb-0">Loading order details...</p>
            </div>
        `);
        $('#orderDetailsModal').modal('show');

        $.get(`{{ url('restaurant/orders') }}/${id}/details`, function(res) {
            if (res.success && res.order) {
                let o = res.order;
                $('#odModalTitle').text(`Order Details - #${o.invoice_no}`);

                $('#btnOdPrintKot').attr('onclick', `printThermalWindow('{{ url("sales") }}/${o.id}/kot')`);
                $('#btnOdPrintBill').attr('onclick', `printThermalWindow('{{ url("restaurant/orders") }}/${o.id}/bill-slip')`);

                let itemsHtml = '';
                o.items.forEach((it, idx) => {
                    itemsHtml += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td>
                                <div class="fw-bold">${it.name}</div>
                                ${it.variant ? `<small class="text-muted">Size/Var: ${it.variant}</small>` : ''}
                                ${it.notes ? `<div class="small text-danger fst-italic">Note: ${it.notes}</div>` : ''}
                            </td>
                            <td class="text-center fw-bold">${it.qty}</td>
                            <td class="text-end">Rs. ${parseFloat(it.price).toFixed(2)}</td>
                            <td class="text-end fw-bold">Rs. ${parseFloat(it.total).toFixed(2)}</td>
                        </tr>
                    `;
                });

                let paymentBadge = o.payment_status === 'paid' ? 
                    `<span class="badge bg-success"><i class="fas fa-check"></i> PAID</span>` : 
                    `<span class="badge bg-danger"><i class="fas fa-clock"></i> UNPAID</span>`;

                let orderTypeDisplay = o.order_type === 'dine_in' ? `Dine-In (Table: ${o.table_no || '-'})` :
                    (o.order_type === 'takeaway' ? `Takeaway (Token: ${o.token_no || '-'})` : `Home Delivery`);

                let html = `
                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-md-6">
                            <div class="small text-muted">Order Type & Target</div>
                            <div class="fw-bold fs-6 text-primary">${orderTypeDisplay}</div>
                            <div class="small text-muted mt-2">Customer Name & Phone</div>
                            <div class="fw-bold text-dark">${o.customer_name} ${o.customer_phone ? `(${o.customer_phone})` : ''}</div>
                            ${o.delivery_address ? `<div class="small text-muted mt-1"><i class="fas fa-map-marker-alt text-danger"></i> ${o.delivery_address}</div>` : ''}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="mb-1">${paymentBadge} <span class="badge bg-secondary text-uppercase">${o.order_status}</span></div>
                            <div class="small text-muted mt-1">Order Time: ${o.created_at} (${o.elapsed})</div>
                            ${o.rider_name ? `<div class="small text-info mt-1"><i class="fas fa-motorcycle"></i> Rider: <b>${o.rider_name}</b></div>` : ''}
                            ${o.order_notes ? `<div class="p-2 bg-light rounded text-start small mt-2"><b>Instructions:</b> ${o.order_notes}</div>` : ''}
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2">Ordered Items (${o.items.length})</h6>
                    <div class="table-responsive mb-3 border rounded">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold">Rs. ${parseFloat(o.subtotal).toFixed(2)}</span>
                            </div>
                            ${parseFloat(o.delivery_charges) > 0 ? `
                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="text-muted">Delivery Charges:</span>
                                    <span class="fw-bold">Rs. ${parseFloat(o.delivery_charges).toFixed(2)}</span>
                                </div>
                            ` : ''}
                            ${parseFloat(o.discount) > 0 ? `
                                <div class="d-flex justify-content-between py-1 small text-danger">
                                    <span>Discount:</span>
                                    <span class="fw-bold">- Rs. ${parseFloat(o.discount).toFixed(2)}</span>
                                </div>
                            ` : ''}
                            <div class="d-flex justify-content-between py-2 border-top border-bottom fs-6 fw-black text-dark">
                                <span>Net Total:</span>
                                <span class="text-success">Rs. ${parseFloat(o.net_total).toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 small mt-1">
                                <span class="text-muted">Paid:</span>
                                <span class="fw-bold text-primary">Rs. ${parseFloat(o.cash_paid).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;

                $('#odModalBody').html(html);
            } else {
                $('#odModalBody').html(`<div class="alert alert-danger mb-0">${res.message || 'Error loading details.'}</div>`);
            }
        }).fail(function() {
            $('#odModalBody').html(`<div class="alert alert-danger mb-0">Server connection failed.</div>`);
        });
    }

    // 2. DINE-IN SERVE (NO WAITER REQUIRED)
    function markOrderServed(id) {
        Swal.fire({
            title: 'Serve Order?',
            text: 'Mark this table order as served to guests?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            confirmButtonText: 'Yes, Mark Served!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.post(`{{ url('restaurant/orders') }}/${id}/serve`, {
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Served!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to serve order.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }
        });
    }

    // 3. ASSIGN RIDER MODAL (DIRECT 1-CLICK RIDER LIST)
    function openAssignRiderModal(orderId, invoiceNo, currentRiderId) {
        $('#arOrderId').val(orderId);
        $('#arOrderInvoice').text('#' + invoiceNo);
        $('#assignRiderModal').modal('show');
    }

    function submitDirectRiderAssign(riderId, riderName) {
        let orderId = $('#arOrderId').val();
        if (!orderId || !riderId) return;

        Swal.fire({
            title: `Assign ${riderName}?`,
            text: `Dispatch order #${$('#arOrderInvoice').text()} with rider ${riderName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Yes, Dispatch Order!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.post(`{{ url('restaurant/orders') }}/${orderId}/assign-rider`, {
                    _token: '{{ csrf_token() }}',
                    rider_id: riderId
                }, function(res) {
                    $('#assignRiderModal').modal('hide');
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dispatched!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to assign rider.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server connection failed.', 'error');
                });
            }
        });
    }

    // 4. UNIFIED COMPLETE & SETTLE MODAL (ALL ORDERS)
    function openCompleteSettleModal(orderId, invoiceNo, payable) {
        $('#csOrderId').val(orderId);
        $('#csModalSubtitle').text('Invoice: #' + invoiceNo);
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

    function submitCompleteSettle(e) {
        e.preventDefault();
        let orderId = $('#csOrderId').val();
        let accountId = $('#csAccountSelect').val();
        let cashReceived = parseFloat($('#csCashReceived').val()) || 0;
        let discount = parseFloat($('#csDiscount').val()) || 0;

        if (!accountId) {
            Swal.fire('Select Account', 'Please choose receiving payment account.', 'warning');
            return;
        }

        $('#btnSubmitCs').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Settling...');

        $.post(`{{ url('restaurant/orders') }}/${orderId}/complete-settle`, {
            _token: '{{ csrf_token() }}',
            account_id: accountId,
            cash_received: cashReceived,
            discount: discount
        }, function(res) {
            $('#completeSettleModal').modal('hide');
            $('#btnSubmitCs').prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Confirm & Print Receipt');

            if (res.success) {
                if (res.receipt_url) {
                    printThermalWindow(res.receipt_url);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Completed & Settled!',
                    text: res.message,
                    timer: 1400,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', res.message || 'Failed to settle order.', 'error');
            }
        }).fail(function(xhr) {
            $('#btnSubmitCs').prop('disabled', false).html('<i class="fas fa-check-double me-1"></i> Confirm & Print Receipt');
            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred.';
            Swal.fire('Error', msg, 'error');
        });
    }

    // 5. CANCEL ORDER ACTION
    function cancelOrderAction(orderId, invoiceNo) {
        Swal.fire({
            title: `Cancel Order #${invoiceNo}?`,
            text: 'Are you sure you want to cancel and void this order?',
            icon: 'warning',
            input: 'text',
            inputPlaceholder: 'Reason for cancellation (optional)...',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, Cancel Order'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                $.post(`{{ url('restaurant/order') }}/${orderId}/cancel`, {
                    _token: '{{ csrf_token() }}',
                    reason: result.value || 'Cancelled by manager'
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Cancelled',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to cancel order.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server connection failed.', 'error');
                });
            }
        });
    }
</script>
@endsection
