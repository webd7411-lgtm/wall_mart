@extends('admin_panel.layout.app')

@section('content')
<style>
    .table-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 180px;
    }
    .table-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }
    .table-card.occupied {
        border-color: #f87171;
        background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%);
    }
    .table-card.available {
        border-color: #86efac;
        background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    }
    .table-card.inactive {
        border-color: #e2e8f0;
        background: #f8fafc;
        opacity: 0.75;
    }
    .table-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .table-card-number {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .table-card-floor {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
    }
    .table-card-capacity {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .table-card-status-badge {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-card-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed #e2e8f0;
    }
    .floor-pill-btn {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .floor-pill-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .floor-pill-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    .stat-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stat-metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
</style>

<div class="main-content-inner">
    <div class="container-fluid pt-3">

        <!-- Top Header & Actions Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div>
                <h4 class="font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-chair text-primary"></i> Restaurant Table Management
                    <span class="badge bg-primary text-white fs-6 px-2 py-1 rounded" style="font-size: 0.75rem;">
                        {{ $activeBranch->name ?? 'All Branches' }}
                    </span>
                </h4>
                <p class="text-muted small mb-0">Create, edit, and manage floor seating layouts and live table occupancy.</p>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0 flex-wrap">
                <a href="{{ route('restaurant.pos') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                    <i class="fas fa-cash-register me-1"></i> POS Counter
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-bs-toggle="modal" data-bs-target="#batchGenerateModal">
                    <i class="fas fa-layer-group me-1"></i> Quick Batch Generate
                </button>
                <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="openAddTableModal()">
                    <i class="fas fa-plus-circle me-1"></i> Add New Table
                </button>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        @php
            $totalCount = $tables->count();
            $occupiedCount = $tables->where('is_occupied', true)->count();
            $availableCount = $tables->where('is_occupied', false)->where('is_active', true)->count();
            $inactiveCount = $tables->where('is_active', false)->count();
        @endphp
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="stat-metric-card">
                    <div class="stat-metric-icon bg-primary-subtle text-primary" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-border-all"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-dark">{{ $totalCount }}</div>
                        <div class="small text-muted font-weight-bold">Total Tables</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-metric-card">
                    <div class="stat-metric-icon bg-success-subtle text-success" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-success">{{ $availableCount }}</div>
                        <div class="small text-muted font-weight-bold">Available Free</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-metric-card">
                    <div class="stat-metric-icon bg-danger-subtle text-danger" style="background: #fef2f2; color: #dc2626;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-danger">{{ $occupiedCount }}</div>
                        <div class="small text-muted font-weight-bold">Occupied / Eating</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-metric-card">
                    <div class="stat-metric-icon bg-secondary-subtle text-secondary" style="background: #f1f5f9; color: #64748b;">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <div class="fs-5 font-weight-bold text-secondary">{{ $inactiveCount }}</div>
                        <div class="small text-muted font-weight-bold">Disabled / Out of Order</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <!-- Floor Pills -->
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <a href="{{ route('restaurant.tables.index', array_merge(request()->except('floor_area'), ['floor_area' => 'all'])) }}"
                       class="floor-pill-btn {{ !request('floor_area') || request('floor_area') === 'all' ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> All Floors ({{ $totalCount }})
                    </a>
                    @foreach($floors as $fl)
                        @php
                            $flCount = $tables->where('floor_area', $fl)->count();
                        @endphp
                        <a href="{{ route('restaurant.tables.index', array_merge(request()->except('floor_area'), ['floor_area' => $fl])) }}"
                           class="floor-pill-btn {{ request('floor_area') === $fl ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i> {{ $fl }} ({{ $flCount }})
                        </a>
                    @endforeach
                </div>

                <!-- Search Input -->
                <form action="{{ route('restaurant.tables.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0" style="max-width: 280px;">
                    @if(request('floor_area'))
                        <input type="hidden" name="floor_area" value="{{ request('floor_area') }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search table..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        @if(request('search'))
                            <a href="{{ route('restaurant.tables.index', request()->except('search')) }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Tables View Mode (Grid) -->
        @if($tables->isEmpty())
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-chair text-muted" style="font-size: 3.5rem; opacity: 0.4;"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark">No Tables Found</h5>
                    <p class="text-muted small mb-3">You haven't added any tables for this branch yet. You can add one or use Quick Batch Generate to create 10-20 tables in 1 click!</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary font-weight-bold" onclick="openAddTableModal()">
                            <i class="fas fa-plus me-1"></i> Add Single Table
                        </button>
                        <button type="button" class="btn btn-outline-secondary font-weight-bold" data-bs-toggle="modal" data-bs-target="#batchGenerateModal">
                            <i class="fas fa-layer-group me-1"></i> Quick Batch Generate (T-1 to T-10)
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach($tables as $t)
                    @php
                        $isOcc = $t->is_occupied;
                        $isAct = $t->is_active;
                        $cardClass = !$isAct ? 'inactive' : ($isOcc ? 'occupied' : 'available');
                    @endphp
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="table-card {{ $cardClass }}">
                            <div>
                                <div class="table-card-header">
                                    <div>
                                        <div class="table-card-number">{{ $t->table_no }}</div>
                                        <div class="table-card-floor">
                                            <i class="fas fa-map-marker-alt text-muted"></i> {{ $t->floor_area ?: 'Main Hall' }}
                                        </div>
                                    </div>
                                    <div>
                                        @if(!$isAct)
                                            <span class="table-card-status-badge bg-secondary text-white">Disabled</span>
                                        @elseif($isOcc)
                                            <span class="table-card-status-badge bg-danger text-white">Occupied</span>
                                        @else
                                            <span class="table-card-status-badge bg-success text-white">Available</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="table-card-capacity">
                                        <i class="fas fa-user-friends text-secondary"></i> {{ $t->capacity }} Persons
                                    </span>
                                    @if($t->notes)
                                        <span class="badge bg-light text-muted border text-truncate" style="max-width: 120px;" title="{{ $t->notes }}">
                                            <i class="fas fa-info-circle me-1"></i> {{ $t->notes }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Running order details if occupied -->
                                @if($isOcc && $t->active_order)
                                    <div class="p-2 rounded mt-2" style="background: rgba(220, 38, 38, 0.08); border: 1px dashed rgba(220, 38, 38, 0.3);">
                                        <div class="d-flex justify-content-between small font-weight-bold text-danger">
                                            <span>#{{ $t->active_order->invoice_no }}</span>
                                            <span>Rs. {{ number_format($t->active_order->total_bill_amount) }}</span>
                                        </div>
                                        <div class="small text-muted d-flex justify-content-between" style="font-size: 0.72rem;">
                                            <span>{{ $t->active_order->walkin_name ?: 'Dine-In Customer' }}</span>
                                            <span>{{ $t->active_order->created_at ? $t->active_order->created_at->format('h:i A') : '' }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Bottom Actions -->
                            <div class="table-card-actions">
                                <div class="form-check form-switch m-0" title="Active / Inactive Status">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="switch-{{ $t->id }}"
                                           {{ $t->is_active ? 'checked' : '' }}
                                           onchange="toggleTableStatus({{ $t->id }}, this)">
                                    <label class="form-check-label small text-muted font-weight-bold" for="switch-{{ $t->id }}">
                                        {{ $t->is_active ? 'Active' : 'Disabled' }}
                                    </label>
                                </div>

                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary"
                                            onclick="openEditTableModal({{ json_encode($t) }})"
                                            title="Edit Table Details" style="padding: 3px 8px;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger"
                                            onclick="confirmDeleteTable({{ $t->id }}, '{{ $t->table_no }}', {{ $isOcc ? 'true' : 'false' }})"
                                            title="Delete Table" style="padding: 3px 8px;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

<!-- ==========================================
     MODAL 1: ADD / EDIT TABLE MODAL
     ========================================== -->
<div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="tableForm" onsubmit="submitTableForm(event)">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id" value="">

                <div class="modal-header bg-light border-bottom py-3">
                    <h5 class="modal-title font-weight-bold text-dark" id="tableModalLabel">
                        <i class="fas fa-chair text-primary me-1"></i> Add New Table
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Branch Selector (if multiple branches and user is admin) -->
                    @if(\App\Helpers\BranchHelper::canSwitchBranch())
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-dark">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" id="modal_branch_id" class="form-select form-select-sm" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $b->id == $activeBranchId ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ ucfirst($b->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="branch_id" value="{{ $activeBranchId }}">
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Table Number / Name <span class="text-danger">*</span></label>
                            <input type="text" name="table_no" id="modal_table_no" class="form-control form-control-sm" placeholder="e.g. T-1, VIP-01" required>
                            <div class="form-text small text-muted">Unique identifier for this branch.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Seating Capacity <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-users text-muted"></i></span>
                                <input type="number" name="capacity" id="modal_capacity" class="form-control" value="4" min="1" max="100" required>
                                <span class="input-group-text bg-light text-muted">Persons</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Floor / Area</label>
                            <input type="text" name="floor_area" id="modal_floor_area" class="form-control form-control-sm" placeholder="e.g. Ground Floor, Rooftop, VIP" list="floorSuggestions" value="Main Hall">
                            <datalist id="floorSuggestions">
                                <option value="Main Hall">
                                <option value="Ground Floor">
                                <option value="First Floor">
                                <option value="Rooftop">
                                <option value="Family Section">
                                <option value="VIP Lounge">
                                <option value="Outdoor / Garden">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Display Order</label>
                            <input type="number" name="sort_order" id="modal_sort_order" class="form-control form-control-sm" value="0" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small text-dark">Special Notes / Description</label>
                        <input type="text" name="notes" id="modal_notes" class="form-control form-control-sm" placeholder="e.g. Near window, Corner table, Smoking zone">
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="modal_is_active" checked>
                        <label class="form-check-label font-weight-bold small text-dark" for="modal_is_active">
                            Active (Available for Orders in POS)
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold" id="btnSaveTable">
                        <i class="fas fa-save me-1"></i> Save Table
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL 2: QUICK BATCH GENERATE MODAL
     ========================================== -->
<div class="modal fade" id="batchGenerateModal" tabindex="-1" aria-labelledby="batchGenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="batchGenerateForm" onsubmit="submitBatchGenerate(event)">
                @csrf
                <div class="modal-header bg-light border-bottom py-3">
                    <h5 class="modal-title font-weight-bold text-dark" id="batchGenerateModalLabel">
                        <i class="fas fa-layer-group text-secondary me-1"></i> Quick Batch Generate Tables
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Quickly generate multiple sequential tables in 1 click (e.g. T-1 to T-15).</p>

                    @if(\App\Helpers\BranchHelper::canSwitchBranch())
                        <div class="mb-3">
                            <label class="form-label font-weight-bold small text-dark">Branch</label>
                            <select name="branch_id" class="form-select form-select-sm" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $b->id == $activeBranchId ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold small text-dark">Prefix</label>
                            <input type="text" name="prefix" class="form-control form-control-sm text-center font-weight-bold" value="T-" placeholder="e.g. T-">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold small text-dark">Start Number</label>
                            <input type="number" name="start_num" class="form-control form-control-sm text-center" value="1" min="1" max="999" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold small text-dark">Total Tables</label>
                            <input type="number" name="count" class="form-control form-control-sm text-center font-weight-bold text-primary" value="10" min="1" max="50" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Floor / Area</label>
                            <input type="text" name="floor_area" class="form-control form-control-sm" value="Main Hall" placeholder="e.g. Ground Floor">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold small text-dark">Default Capacity</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="capacity" class="form-control" value="4" min="1" max="100" required>
                                <span class="input-group-text bg-light text-muted">Persons</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold" id="btnBatchSubmit">
                        <i class="fas fa-magic me-1"></i> Generate Tables Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Open Add Modal
    function openAddTableModal() {
        $('#tableForm')[0].reset();
        $('#edit_id').val('');
        $('#tableModalLabel').html('<i class="fas fa-chair text-primary me-1"></i> Add New Table');
        $('#modal_capacity').val(4);
        $('#modal_sort_order').val(0);
        $('#modal_floor_area').val('Main Hall');
        $('#modal_is_active').prop('checked', true);
        $('#tableModal').modal('show');
    }

    // Open Edit Modal
    function openEditTableModal(table) {
        $('#tableForm')[0].reset();
        $('#edit_id').val(table.id);
        $('#tableModalLabel').html('<i class="fas fa-edit text-primary me-1"></i> Edit Table: ' + table.table_no);
        $('#modal_table_no').val(table.table_no);
        $('#modal_floor_area').val(table.floor_area || 'Main Hall');
        $('#modal_capacity').val(table.capacity || 4);
        $('#modal_sort_order').val(table.sort_order || 0);
        $('#modal_notes').val(table.notes || '');
        if ($('#modal_branch_id').length) {
            $('#modal_branch_id').val(table.branch_id);
        }
        $('#modal_is_active').prop('checked', table.is_active == 1);
        $('#tableModal').modal('show');
    }

    // Submit Add/Edit Table
    function submitTableForm(e) {
        e.preventDefault();
        const form = $('#tableForm');
        const submitBtn = $('#btnSaveTable');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('restaurant.tables.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                $('#tableModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message || 'Table saved successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Table');
                let msg = 'Failed to save table.';
                if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: msg
                });
            }
        });
    }

    // Submit Batch Generate
    function submitBatchGenerate(e) {
        e.preventDefault();
        const form = $('#batchGenerateForm');
        const btn = $('#btnBatchSubmit');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Generating...');

        $.ajax({
            url: "{{ route('restaurant.tables.batch_create') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                $('#batchGenerateModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Batch Generated!',
                    text: res.message || 'Tables created successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err) {
                btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> Generate Tables Now');
                let msg = 'Failed to generate tables.';
                if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    }

    // Toggle Active Status
    function toggleTableStatus(id, checkbox) {
        const isChecked = checkbox.checked;
        $.ajax({
            url: "{{ url('restaurant/tables') }}/" + id + "/toggle-status",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                // update parent card styling
                const card = $(checkbox).closest('.table-card');
                if (res.is_active) {
                    card.removeClass('inactive');
                    $(checkbox).next('label').text('Active');
                } else {
                    card.addClass('inactive');
                    $(checkbox).next('label').text('Disabled');
                }
            },
            error: function() {
                checkbox.checked = !isChecked;
                Swal.fire('Error', 'Could not update table status.', 'error');
            }
        });
    }

    // Delete Table
    function confirmDeleteTable(id, tableNo, isOccupied) {
        if (isOccupied) {
            Swal.fire({
                icon: 'warning',
                title: 'Table Occupied!',
                text: "Table '" + tableNo + "' currently has an active running order. Please settle or cancel the order in POS before deleting.",
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        Swal.fire({
            title: "Delete Table '" + tableNo + "'?",
            text: "Are you sure you want to delete this table? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Table'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('restaurant/tables/delete') }}/" + id,
                    type: "GET",
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message || 'Table deleted successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = 'Could not delete table.';
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
