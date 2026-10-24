@extends('admin_panel.layout.app')

@section('content')
<style>
    .rider-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .rider-metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    .duty-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="main-content-inner">
    <div class="container-fluid pt-3">

        <!-- Top Header & Actions -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div>
                <h4 class="font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-motorcycle text-primary"></i> Rider Management
                    <span class="badge bg-primary text-white fs-6 px-2 py-1 rounded" style="font-size: 0.75rem;">
                        {{ $activeBranch->name ?? 'All Branches' }}
                    </span>
                </h4>
                <p class="text-muted small mb-0">Manage delivery boys, vehicle details, duty status, and daily cash settlements.</p>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0 flex-wrap">
                <a href="{{ route('restaurant.deliveries.index') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                    <i class="fas fa-shipping-fast me-1"></i> Delivery Dispatch Orders
                </a>
                <a href="{{ route('restaurant.pos') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
                    <i class="fas fa-cash-register me-1"></i> POS Counter
                </a>
                <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openAddRiderModal()">
                    <i class="fas fa-plus-circle me-1"></i> Add New Rider
                </button>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="rider-metric-card">
                    <div class="rider-metric-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-dark">{{ $totalRiders }}</div>
                        <div class="small text-muted font-weight-bold">Total Riders</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rider-metric-card">
                    <div class="rider-metric-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-success">{{ $availableRiders }}</div>
                        <div class="small text-muted font-weight-bold">Available Now</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rider-metric-card">
                    <div class="rider-metric-icon" style="background: #fefce8; color: #ca8a04;">
                        <i class="fas fa-route"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-warning">{{ $onDeliveryRiders }}</div>
                        <div class="small text-muted font-weight-bold">On Delivery Road</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rider-metric-card">
                    <div class="rider-metric-icon" style="background: #fef2f2; color: #dc2626;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-danger">Rs. {{ number_format($totalCashInHand, 0) }}</div>
                        <div class="small text-muted font-weight-bold">Unsettled Cash (In Hand)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <a href="{{ route('restaurant.riders.index') }}" class="btn btn-sm {{ !request('duty_status') || request('duty_status') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 8px;">
                        All Duty Status
                    </a>
                    <a href="{{ route('restaurant.riders.index', ['duty_status' => 'available']) }}" class="btn btn-sm {{ request('duty_status') === 'available' ? 'btn-success' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 8px;">
                        🟢 Available
                    </a>
                    <a href="{{ route('restaurant.riders.index', ['duty_status' => 'on_delivery']) }}" class="btn btn-sm {{ request('duty_status') === 'on_delivery' ? 'btn-warning' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 8px;">
                        🟡 On Delivery
                    </a>
                    <a href="{{ route('restaurant.riders.index', ['duty_status' => 'off_duty']) }}" class="btn btn-sm {{ request('duty_status') === 'off_duty' ? 'btn-secondary' : 'btn-outline-secondary' }} fw-bold" style="border-radius: 8px;">
                        ⚪ Off Duty
                    </a>
                </div>

                <form action="{{ route('restaurant.riders.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0" style="max-width: 280px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search rider name, phone, bike..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        @if(request('search'))
                            <a href="{{ route('restaurant.riders.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Riders Table Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3 text-secondary" style="font-size: 0.8rem;">Rider Profile</th>
                                <th class="py-3 text-secondary" style="font-size: 0.8rem;">Contact & CNIC</th>
                                <th class="py-3 text-secondary" style="font-size: 0.8rem;">Vehicle / Bike</th>
                                <th class="py-3 text-secondary text-center" style="font-size: 0.8rem;">Duty Status</th>
                                <th class="py-3 text-secondary text-center" style="font-size: 0.8rem;">Active Deliveries</th>
                                <th class="py-3 text-secondary text-center" style="font-size: 0.8rem;">Today's Delivered</th>
                                <th class="py-3 text-secondary text-end" style="font-size: 0.8rem;">Cash In Hand</th>
                                <th class="py-3 text-secondary text-center" style="width: 170px; font-size: 0.8rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riders as $r)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 38px; height: 38px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem;">
                                                {{ strtoupper(substr($r->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $r->name }}</div>
                                                <small class="text-muted">{{ $r->branch->name ?? 'Main Branch' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="fas fa-phone-alt text-muted me-1" style="font-size: 0.75rem;"></i> {{ $r->phone }}</div>
                                        @if($r->cnic)
                                            <small class="text-muted"><i class="far fa-id-card me-1"></i> {{ $r->cnic }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($r->vehicle_no)
                                            <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                                <i class="fas fa-motorcycle text-secondary me-1"></i> {{ $r->vehicle_no }}
                                            </span>
                                        @else
                                            <span class="text-muted small">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($r->duty_status === 'available')
                                            <span class="duty-badge bg-success-subtle text-success border border-success-subtle">Available</span>
                                        @elseif($r->duty_status === 'on_delivery')
                                            <span class="duty-badge bg-warning-subtle text-warning border border-warning-subtle">On Delivery</span>
                                        @else
                                            <span class="duty-badge bg-secondary-subtle text-secondary border border-secondary-subtle">Off Duty</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($r->active_deliveries_count > 0)
                                            <span class="badge bg-warning text-dark fw-bold rounded-pill px-2 py-1">
                                                {{ $r->active_deliveries_count }} Active
                                            </span>
                                        @else
                                            <span class="text-muted small">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                            {{ $r->today_completed_count }} Orders
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($r->unsettled_cash > 0)
                                            <div class="fw-bold text-danger fs-6">Rs. {{ number_format($r->unsettled_cash, 0) }}</div>
                                            <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 fw-bold mt-1"
                                                    onclick="confirmSettleCash({{ $r->id }}, '{{ $r->name }}', {{ $r->unsettled_cash }})"
                                                    title="Collect and settle cash from rider" style="font-size: 0.7rem;">
                                                <i class="fas fa-hand-holding-usd me-1"></i> Settle Cash
                                            </button>
                                        @else
                                            <span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> Cleared</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-xs btn-outline-primary"
                                                    onclick="openEditRiderModal({{ json_encode($r) }})"
                                                    title="Edit Rider" style="padding: 3px 8px;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-danger"
                                                    onclick="confirmDeleteRider({{ $r->id }}, '{{ $r->name }}', {{ $r->active_deliveries_count }})"
                                                    title="Delete Rider" style="padding: 3px 8px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-motorcycle mb-2" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                        <div class="fw-bold text-dark">No Riders Found</div>
                                        <p class="small text-muted mb-2">Click "Add New Rider" to register your delivery staff.</p>
                                        <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="openAddRiderModal()">
                                            <i class="fas fa-plus me-1"></i> Register First Rider
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ==========================================
     MODAL: ADD / EDIT RIDER
     ========================================== -->
<div class="modal fade" id="riderModal" tabindex="-1" aria-labelledby="riderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="riderForm" onsubmit="submitRiderForm(event)">
                @csrf
                <input type="hidden" name="edit_id" id="rider_edit_id" value="">

                <div class="modal-header bg-dark text-white py-3 px-4">
                    <h5 class="modal-title font-weight-bold" id="riderModalLabel">
                        <i class="fas fa-motorcycle text-warning me-1"></i> Add New Rider
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    @if(\App\Helpers\BranchHelper::canSwitchBranch())
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-dark">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" id="modal_rider_branch_id" class="form-select form-select-sm" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $b->id == $activeBranchId ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="branch_id" value="{{ $activeBranchId }}">
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Rider Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="modal_rider_name" class="form-control form-control-sm" placeholder="e.g. Usman Ali" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="modal_rider_phone" class="form-control form-control-sm" placeholder="0300-1234567" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">CNIC Number</label>
                            <input type="text" name="cnic" id="modal_rider_cnic" class="form-control form-control-sm" placeholder="42101-xxxxxxx-x">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Vehicle / Bike Number</label>
                            <input type="text" name="vehicle_no" id="modal_rider_vehicle" class="form-control form-control-sm" placeholder="e.g. KHI-1234 (CD 70)">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Duty Status</label>
                            <select name="duty_status" id="modal_rider_duty" class="form-select form-select-sm">
                                <option value="available">🟢 Available for Orders</option>
                                <option value="on_delivery">🟡 On Delivery Road</option>
                                <option value="off_duty">⚪ Off Duty / Leave</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Shift / Notes</label>
                            <input type="text" name="notes" id="modal_rider_notes" class="form-control form-control-sm" placeholder="e.g. Morning Shift">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small text-dark">Residential Address</label>
                        <input type="text" name="address" id="modal_rider_address" class="form-control form-control-sm" placeholder="Area, City">
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="modal_rider_active" checked>
                        <label class="form-check-label font-weight-bold small text-dark" for="modal_rider_active">
                            Active Rider (Available in POS Delivery Assignment)
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="btnSaveRider">
                        <i class="fas fa-save me-1"></i> Save Rider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function openAddRiderModal() {
        $('#riderForm')[0].reset();
        $('#rider_edit_id').val('');
        $('#riderModalLabel').html('<i class="fas fa-motorcycle text-warning me-1"></i> Add New Rider');
        $('#modal_rider_duty').val('available');
        $('#modal_rider_active').prop('checked', true);
        $('#riderModal').modal('show');
    }

    function openEditRiderModal(rider) {
        $('#riderForm')[0].reset();
        $('#rider_edit_id').val(rider.id);
        $('#riderModalLabel').html('<i class="fas fa-edit text-warning me-1"></i> Edit Rider: ' + rider.name);
        $('#modal_rider_name').val(rider.name);
        $('#modal_rider_phone').val(rider.phone);
        $('#modal_rider_cnic').val(rider.cnic || '');
        $('#modal_rider_vehicle').val(rider.vehicle_no || '');
        $('#modal_rider_duty').val(rider.duty_status || 'available');
        $('#modal_rider_notes').val(rider.notes || '');
        $('#modal_rider_address').val(rider.address || '');
        if ($('#modal_rider_branch_id').length) {
            $('#modal_rider_branch_id').val(rider.branch_id);
        }
        $('#modal_rider_active').prop('checked', rider.is_active == 1);
        $('#riderModal').modal('show');
    }

    function submitRiderForm(e) {
        e.preventDefault();
        const form = $('#riderForm');
        const submitBtn = $('#btnSaveRider');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('restaurant.riders.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                $('#riderModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message || 'Rider saved successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Rider');
                let msg = 'Failed to save rider.';
                if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }
                Swal.fire('Validation Error', msg, 'error');
            }
        });
    }

    function confirmSettleCash(riderId, riderName, amount) {
        Swal.fire({
            title: `Settle Rs. ${amount.toLocaleString()}?`,
            text: `Confirm that Rider '${riderName}' has submitted all collected COD cash at the counter.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Cash Received'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('restaurant/riders') }}/${riderId}/settle-cash`,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cash Settled!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Failed to settle cash.', 'error');
                    }
                });
            }
        });
    }

    function confirmDeleteRider(id, name, activeDeliveries) {
        if (activeDeliveries > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Rider is On Delivery!',
                text: `Rider '${name}' currently has ${activeDeliveries} active deliveries. Please complete or reassign orders first.`,
            });
            return;
        }

        Swal.fire({
            title: `Delete Rider '${name}'?`,
            text: "Are you sure you want to delete this rider record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Rider'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('restaurant/riders/delete') }}/${id}`,
                    type: "GET",
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message || 'Rider deleted.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = 'Could not delete rider.';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
