@extends('admin_panel.layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                        <h3 class="card-title mb-0">{{ !empty($isRestaurant) ? 'Restaurant POS Settings' : 'ERP Settings' }}</h3>
                        @if(isset($selectedBranch))
                            <span class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
                                <i class="fas fa-building mr-1"></i> Branch: {{ $selectedBranch->name }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <!-- Branch Selection Banner -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 rounded border shadow-sm" style="background-color: #f8fafc; border-left: 4px solid #4f46e5 !important;">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="p-2 rounded bg-white shadow-sm mr-3 text-primary">
                                    <i class="fas fa-code-branch fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold text-dark">
                                        Configuring: <span class="text-primary">{{ $selectedBranch->name ?? 'Default Branch' }}</span>
                                    </h5>
                                    <small class="text-muted">
                                        Restaurant name, receipt logo, contact info, and receipt terms configured below are saved specifically for this branch.
                                    </small>
                                </div>
                            </div>
                            @if(!empty($canSwitchBranch) && isset($branches) && count($branches) > 1 && empty($isRestaurant))
                                <div class="d-flex align-items-center">
                                    <label for="branchSelectDropdown" class="mb-0 mr-2 font-weight-bold text-secondary text-nowrap">
                                        <i class="fas fa-exchange-alt mr-1"></i> Switch Branch:
                                    </label>
                                    <select id="branchSelectDropdown" class="custom-select custom-select-sm font-weight-bold border-primary shadow-sm" style="min-width: 180px; height: 38px;" onchange="window.location.href='{{ route('settings.index') }}?branch_id=' + this.value;">
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ $b->id == $branchId ? 'selected' : '' }}>
                                                {{ $b->name }} (ID #{{ $b->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        @if(empty($isRestaurant))
                        <!-- Advanced Settings & Navigation (Hidden in Restaurant Mode) -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted mb-3 font-weight-bold text-uppercase"
                                style="font-size: 0.8rem; letter-spacing: 1px;">Advanced Actions</h6>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('settings.return-policy') }}?branch_id={{ $branchId }}"
                                    class="btn btn-outline-primary mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-undo-alt mr-2"></i> Return Policy
                                </a>
                                <a href="{{ route('settings.return-approvers') }}"
                                    class="btn btn-outline-info mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-user-shield mr-2"></i> Return Approvers
                                </a>
                                <a href="{{ route('branch.index') }}" class="btn btn-outline-dark mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-code-branch mr-2"></i> Manage Branches
                                </a>
                            </div>
                        </div>
                        @endif

                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="company-tab" data-toggle="tab" href="#company"
                                    role="tab">
                                    <i class="fas fa-building"></i> Profile &amp; Receipt
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sales-tab" data-toggle="tab" href="#sales" role="tab">
                                    <i class="fas fa-shopping-cart"></i> POS &amp; Sales
                                </a>
                            </li>
                            @if(empty($isRestaurant))
                            <li class="nav-item">
                                <a class="nav-link" id="inventory-tab" data-toggle="tab" href="#inventory" role="tab">
                                    <i class="fas fa-boxes"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="accounting-tab" data-toggle="tab" href="#accounting" role="tab">
                                    <i class="fas fa-calculator"></i> Accounting
                                </a>
                            </li>
                            @endif
                        </ul>

                        @php
                            $canEditSettings = !auth()->check() || auth()->user()->hasRole('Super Admin') || auth()->user()->can('settings.edit') || auth()->user()->can('settings.create') || !empty($isRestaurant);
                        @endphp
                        <form id="settingsForm" class="mt-4" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="branch_id" id="settingBranchIdInput" value="{{ $branchId }}">

                            <div class="tab-content" id="settingsTabContent">
                                <!-- Company Tab -->
                                <div class="tab-pane fade show active" id="company" role="tabpanel">
                                    <!-- Company Logo Section -->
                                    @php
                                        $companyLogo = \App\Models\Setting::get('company_logo', null, $branchId);
                                        $hasLogo = !empty($companyLogo);
                                    @endphp
                                    <div class="card mb-4 border shadow-sm" style="background-color: #f8fafc;">
                                        <div class="card-header bg-white font-weight-bold d-flex align-items-center">
                                            <i class="fas fa-image text-primary mr-2"></i> Branch Logo (Sale Receipt & Invoice)
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-3">
                                                Upload official logo for <strong>{{ $selectedBranch->name ?? 'this branch' }}</strong>. When uploaded, this logo will appear at the top of receipts and invoices issued by this branch.
                                            </p>
                                            <div class="row align-items-center">
                                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                                    <div id="logoPreviewContainer" class="p-3 bg-white border rounded shadow-sm d-inline-flex align-items-center justify-content-center" style="min-width: 170px; min-height: 110px; width: 100%; max-width: 220px; height: 110px;">
                                                        <img id="logoPreview" 
                                                             src="{{ $hasLogo ? asset(ltrim($companyLogo, '/')) : '' }}" 
                                                             alt="Branch Logo Preview" 
                                                             style="max-width: 100%; max-height: 90px; object-fit: contain; {{ !$hasLogo ? 'display: none;' : '' }}" />
                                                        <div id="noLogoText" class="text-muted text-center" style="{{ $hasLogo ? 'display: none;' : '' }}">
                                                            <i class="fas fa-image fa-2x d-block mb-1 text-secondary"></i>
                                                            <small>No Logo Uploaded for this Branch</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="custom-file mb-2">
                                                        <input type="file" name="company_logo" id="companyLogoInput" class="custom-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                        <label class="custom-file-label text-truncate" for="companyLogoInput" id="companyLogoLabel">Choose branch logo image file...</label>
                                                    </div>
                                                    <input type="hidden" name="remove_company_logo" id="removeCompanyLogoInput" value="0">
                                                    
                                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnRemoveLogo" style="{{ $hasLogo ? '' : 'display: none;' }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash-alt mr-1"></i> Remove Logo
                                                        </button>
                                                        <small class="text-muted ml-md-2">Formats: PNG, JPG, WebP, SVG (Max: 4MB). Transparent PNG recommended.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (isset($settings['company']))
                                        @foreach ($settings['company'] as $setting)
                                            @if ($setting['key'] === 'company_logo')
                                                @continue
                                            @endif
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">{{ $setting['label'] }}</label>
                                                @if ($setting['type'] === 'text')
                                                    <textarea name="settings[{{ $setting['key'] }}]" class="form-control" rows="3" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                @else
                                                    <input type="text" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @endif
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Sales Tab -->
                                <div class="tab-pane fade" id="sales" role="tabpanel">
                                    @if (isset($settings['sales']))
                                        @foreach ($settings['sales'] as $setting)
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">{{ $setting['label'] }}</label>
                                                @if ($setting['type'] === 'text')
                                                    <textarea name="settings[{{ $setting['key'] }}]" class="form-control" rows="3" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                @elseif($setting['type'] === 'integer')
                                                    <input type="number" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @else
                                                    <input type="text" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @endif
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Inventory Tab -->
                                <div class="tab-pane fade" id="inventory" role="tabpanel">
                                    @if (isset($settings['inventory']))
                                        @foreach ($settings['inventory'] as $setting)
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">{{ $setting['label'] }}</label>
                                                <input type="number" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Accounting Tab -->
                                <div class="tab-pane fade" id="accounting" role="tabpanel">
                                    @if (isset($settings['accounting']))
                                        @foreach ($settings['accounting'] as $setting)
                                            <div class="form-group">
                                                <label class="font-weight-bold text-dark">{{ $setting['label'] }}</label>
                                                <input type="text" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex align-items-center">
                                @if($canEditSettings)
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSaveSettings">
                                        <i class="fas fa-save mr-1"></i> Save {{ $selectedBranch->name ?? 'Branch' }} Settings
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary px-4" disabled style="opacity: 0.6; cursor: not-allowed;">
                                        <i class="fas fa-lock mr-1"></i> Save Settings (Read Only)
                                    </button>
                                @endif
                                <span class="text-muted ml-3 small">
                                    <i class="fas fa-info-circle mr-1"></i> Changes will take effect immediately for this branch.
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Live preview logo on file select
                $('#companyLogoInput').on('change', function(e) {
                    var file = e.target.files[0];
                    if (file) {
                        $('#companyLogoLabel').text(file.name);
                        $('#removeCompanyLogoInput').val('0');
                        var reader = new FileReader();
                        reader.onload = function(evt) {
                            $('#logoPreview').attr('src', evt.target.result).show();
                            $('#noLogoText').hide();
                            $('#btnRemoveLogo').show();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Remove logo action
                $('#btnRemoveLogo').on('click', function() {
                    $('#companyLogoInput').val('');
                    $('#companyLogoLabel').text('Choose branch logo image file...');
                    $('#removeCompanyLogoInput').val('1');
                    $('#logoPreview').attr('src', '').hide();
                    $('#noLogoText').show();
                    $(this).hide();
                });

                $('#settingsForm').on('submit', function(e) {
                    e.preventDefault();

                    var submitBtn = $('#btnSaveSettings');
                    var origHtml = submitBtn.html();
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                    var formData = new FormData(this);

                    $.ajax({
                        url: '{{ route('settings.update') }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            submitBtn.prop('disabled', false).html(origHtml);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (response.logo_url) {
                                $('#logoPreview').attr('src', response.logo_url).show();
                                $('#noLogoText').hide();
                                $('#btnRemoveLogo').show();
                                $('#removeCompanyLogoInput').val('0');
                                $('#companyLogoInput').val('');
                                $('#companyLogoLabel').text('Choose branch logo image file...');
                            } else if ($('#removeCompanyLogoInput').val() === '1') {
                                $('#logoPreview').attr('src', '').hide();
                                $('#noLogoText').show();
                                $('#btnRemoveLogo').hide();
                            }
                        },
                        error: function(xhr) {
                            submitBtn.prop('disabled', false).html(origHtml);
                            var errorMsg = 'Failed to update settings';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errs = Object.values(xhr.responseJSON.errors).flat();
                                if (errs.length > 0) errorMsg = errs.join('\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMsg,
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
