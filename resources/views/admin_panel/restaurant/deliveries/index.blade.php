@extends('admin_panel.layout.app')

@section('content')
<style>
    .delivery-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .delivery-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    .status-tab-btn {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }
    .status-tab-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .status-tab-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
</style>

<div class="main-content-inner">
    <div class="container-fluid pt-3">

        <!-- Top Header & Actions -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div>
                <h4 class="font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-shipping-fast text-primary"></i> Delivery Dispatch Dashboard
                    <span class="badge bg-primary text-white fs-6 px-2 py-1 rounded" style="font-size: 0.75rem;">
                        {{ $activeBranch->name ?? 'All Branches' }}
                    </span>
                </h4>
                <p class="text-muted small mb-0">Track live restaurant home deliveries, assign riders, and monitor delivery cash.</p>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0 flex-wrap">
                <a href="{{ route('restaurant.riders.index') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                    <i class="fas fa-motorcycle me-1"></i> Manage Riders
                </a>
                <a href="{{ route('restaurant.pos') }}" class="btn btn-primary btn-sm font-weight-bold">
                    <i class="fas fa-cash-register me-1"></i> POS Counter
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="delivery-stat-card">
                    <div class="delivery-stat-icon" style="background: #fefce8; color: #ca8a04;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-warning">{{ $pendingCount }}</div>
                        <div class="small text-muted font-weight-bold">Pending Dispatch</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="delivery-stat-card">
                    <div class="delivery-stat-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-primary">{{ $dispatchedCount }}</div>
                        <div class="small text-muted font-weight-bold">Out on the Road</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="delivery-stat-card">
                    <div class="delivery-stat-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-success">{{ $deliveredTodayCount }}</div>
                        <div class="small text-muted font-weight-bold">Delivered Today</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="delivery-stat-card">
                    <div class="delivery-stat-icon" style="background: #f8fafc; color: #0f172a;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-dark">Rs. {{ number_format($deliveredTodayRevenue, 0) }}</div>
                        <div class="small text-muted font-weight-bold">Today's Delivery Sales</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <a href="{{ route('restaurant.deliveries.index', ['status' => 'all']) }}" class="status-tab-btn {{ $currentStatus === 'all' ? 'active' : '' }}">
                        All Orders
                    </a>
                    <a href="{{ route('restaurant.deliveries.index', ['status' => 'pending']) }}" class="status-tab-btn {{ $currentStatus === 'pending' ? 'active' : '' }}">
                        🟡 Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('restaurant.deliveries.index', ['status' => 'dispatched']) }}" class="status-tab-btn {{ $currentStatus === 'dispatched' ? 'active' : '' }}">
                        🔵 Out on Road ({{ $dispatchedCount }})
                    </a>
                    <a href="{{ route('restaurant.deliveries.index', ['status' => 'delivered']) }}" class="status-tab-btn {{ $currentStatus === 'delivered' ? 'active' : '' }}">
                        🟢 Delivered
                    </a>
                    <a href="{{ route('restaurant.deliveries.index', ['status' => 'cancelled']) }}" class="status-tab-btn {{ $currentStatus === 'cancelled' ? 'active' : '' }}">
                        🔴 Cancelled
                    </a>
                </div>

                <form action="{{ route('restaurant.deliveries.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0" style="max-width: 280px;">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search invoice, customer, phone..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        @if(request('search'))
                            <a href="{{ route('restaurant.deliveries.index', request()->except('search')) }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Delivery Orders Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3 text-secondary" style="font-size: 0.8rem;">Invoice # / Time</th>
                                <th class="py-3 text-secondary" style="font-size: 0.8rem;">Customer & Contact</th>
                                <th class="py-3 text-secondary" style="font-size: 0.8rem;">Delivery Address</th>
                                <th class="py-3 text-secondary" style="font-size: 0.8rem;">Dishes / Items</th>
                                <th class="py-3 text-secondary" style="font-size: 0.8rem;">Assigned Rider</th>
                                <th class="py-3 text-secondary text-center" style="font-size: 0.8rem;">Status</th>
                                <th class="py-3 text-secondary text-end" style="font-size: 0.8rem;">Bill / COD</th>
                                <th class="py-3 text-secondary text-center" style="width: 180px; font-size: 0.8rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $ord)
                                @php
                                    $st = $ord->delivery_status ?: 'pending';
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-primary fs-6">#{{ $ord->invoice_no }}</div>
                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $ord->created_at ? $ord->created_at->format('h:i A') : '' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $ord->walkin_name ?: ($ord->customer_relation->customer_name ?? 'Customer') }}</div>
                                        <small class="text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $ord->rider_phone ?: ($ord->customer_relation->phone_no ?? '&mdash;') }}</small>
                                    </td>
                                    <td style="max-width: 200px;">
                                        @if($ord->delivery_address)
                                            <div class="small text-dark text-truncate" title="{{ $ord->delivery_address }}">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $ord->delivery_address }}
                                            </div>
                                        @else
                                            <span class="text-muted small">No address entered</span>
                                        @endif
                                    </td>
                                    <td style="max-width: 180px;">
                                        <div class="small text-muted text-truncate" title="{{ $ord->items->map(function($i){ return ($i->qty ?: $i->total_pieces).'x '.($i->product_name ?: $i->product->item_name ?? ''); })->implode(', ') }}">
                                            {{ $ord->items->map(function($i){ return ($i->qty ?: $i->total_pieces).'x '.($i->product_name ?: $i->product->item_name ?? ''); })->implode(', ') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($ord->rider)
                                            <div class="fw-bold text-dark"><i class="fas fa-motorcycle text-primary me-1"></i> {{ $ord->rider->name }}</div>
                                            <small class="text-muted">{{ $ord->rider->phone }}</small>
                                        @elseif($ord->rider_name)
                                            <div class="fw-bold text-dark"><i class="fas fa-motorcycle text-secondary me-1"></i> {{ $ord->rider_name }}</div>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-1 px-2">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($st === 'pending')
                                            <span class="badge bg-warning text-dark px-2 py-1">Pending</span>
                                        @elseif($st === 'dispatched')
                                            <span class="badge bg-primary text-white px-2 py-1">Out for Delivery</span>
                                        @elseif($st === 'delivered')
                                            <span class="badge bg-success text-white px-2 py-1">Delivered</span>
                                        @else
                                            <span class="badge bg-danger text-white px-2 py-1">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-dark fs-6">Rs. {{ number_format($ord->total_bill_amount, 0) }}</div>
                                        @if($ord->delivery_charges > 0)
                                            <small class="text-muted">(incl. Rs. {{ number_format($ord->delivery_charges, 0) }} del)</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if($st === 'pending' || $st === 'dispatched')
                                                <button type="button" class="btn btn-xs btn-primary fw-bold py-1 px-2"
                                                        onclick="openAssignRiderModal({{ $ord->id }}, '{{ $ord->invoice_no }}', {{ $ord->rider_id ?: 'null' }})"
                                                        title="{{ $st === 'pending' ? 'Assign Rider & Dispatch' : 'Change Rider' }}" style="font-size: 0.72rem;">
                                                    <i class="fas fa-motorcycle me-1"></i> {{ $st === 'pending' ? 'Dispatch' : 'Reassign' }}
                                                </button>
                                            @endif

                                            @if($st === 'dispatched')
                                                <button type="button" class="btn btn-xs btn-success fw-bold py-1 px-2"
                                                        onclick="confirmMarkDelivered({{ $ord->id }}, '{{ $ord->invoice_no }}')"
                                                        title="Mark as Delivered & Collected" style="font-size: 0.72rem;">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif

                                            <a href="{{ route('restaurant.deliveries.slip', $ord->id) }}" target="_blank"
                                               class="btn btn-xs btn-outline-secondary py-1 px-2" title="Print Delivery Slip" style="font-size: 0.72rem;">
                                                <i class="fas fa-print"></i>
                                            </a>

                                            @if($st !== 'delivered' && $st !== 'cancelled')
                                                <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2"
                                                        onclick="confirmCancelDelivery({{ $ord->id }}, '{{ $ord->invoice_no }}')"
                                                        title="Cancel Delivery" style="font-size: 0.72rem;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open mb-2" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                        <div class="fw-bold text-dark">No Delivery Orders Found</div>
                                        <p class="small text-muted mb-0">Delivery orders punched from POS will appear here for dispatch.</p>
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

    </div>
</div>

<!-- ASSIGN RIDER MODAL -->
<div class="modal fade" id="assignRiderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="assignRiderForm" onsubmit="submitAssignRider(event)">
                @csrf
                <input type="hidden" id="assign_order_id" value="">

                <div class="modal-header bg-dark text-white py-3 px-4">
                    <h5 class="modal-title font-weight-bold" id="assignModalTitle">
                        <i class="fas fa-motorcycle text-warning me-1"></i> Assign Rider & Dispatch
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold small text-dark">Select Delivery Rider <span class="text-danger">*</span></label>
                        <select name="rider_id" id="assign_rider_select" class="form-select" required>
                            <option value="">-- Choose Active Rider --</option>
                            @foreach($riders as $rd)
                                <option value="{{ $rd->id }}">
                                    {{ $rd->name }} &mdash; {{ $rd->phone }} ({{ ucfirst(str_replace('_', ' ', $rd->duty_status)) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted">Rider status will automatically update to "On Delivery Road".</div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="btnConfirmDispatch">
                        <i class="fas fa-paper-plane me-1"></i> Dispatch Order Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function openAssignRiderModal(orderId, invoiceNo, currentRiderId) {
        $('#assign_order_id').val(orderId);
        $('#assignModalTitle').html(`<i class="fas fa-motorcycle text-warning me-1"></i> Dispatch Order #${invoiceNo}`);
        if (currentRiderId) {
            $('#assign_rider_select').val(currentRiderId);
        } else {
            $('#assign_rider_select').val('');
        }
        $('#assignRiderModal').modal('show');
    }

    function submitAssignRider(e) {
        e.preventDefault();
        const orderId = $('#assign_order_id').val();
        const riderId = $('#assign_rider_select').val();
        const btn = $('#btnConfirmDispatch');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Dispatching...');

        $.ajax({
            url: `{{ url('restaurant/deliveries') }}/${orderId}/assign-rider`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                rider_id: riderId
            },
            success: function(res) {
                $('#assignRiderModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Dispatched!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err) {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Dispatch Order Now');
                Swal.fire('Error', 'Failed to dispatch order.', 'error');
            }
        });
    }

    function confirmMarkDelivered(orderId, invoiceNo) {
        Swal.fire({
            title: `Mark Order #${invoiceNo} as Delivered?`,
            text: "Confirm that customer has received food and COD payment is collected by rider.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Delivered'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('restaurant/deliveries') }}/${orderId}/mark-delivered`,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Delivered!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to update delivery status.', 'error');
                    }
                });
            }
        });
    }

    function confirmCancelDelivery(orderId, invoiceNo) {
        Swal.fire({
            title: `Cancel Delivery #${invoiceNo}?`,
            input: 'text',
            inputLabel: 'Reason for cancellation / return',
            inputPlaceholder: 'e.g. Customer unreachable, Wrong address...',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Cancel Delivery'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('restaurant/deliveries') }}/${orderId}/cancel`,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        reason: result.value || 'Cancelled'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cancelled',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to cancel delivery.', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
