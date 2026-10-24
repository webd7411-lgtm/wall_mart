@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Full Viewport POS Layout */
    html, body {
        overflow: hidden !important;
        height: 100% !important;
        background-color: #f1f5f9;
    }
    
    .page-container, .main-panel, .content-wrapper, .main-content-inner, .main-content {
        height: calc(100vh - 135px) !important;
        overflow: hidden !important;
        padding: 4px 8px !important;
        margin: 0 !important;
    }
    
    footer { display: none !important; }

    .rest-pos-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 145px);
        gap: 10px;
    }

    /* TOP BAR */
    .rest-topbar {
        background: #ffffff;
        border-radius: 10px;
        padding: 8px 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        border: 1px solid #e2e8f0;
    }

    .rest-brand-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-right: 12px;
        border-right: 2px solid #e2e8f0;
    }

    .rest-mode-pill {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 8px;
        gap: 2px;
    }

    .rest-mode-btn {
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
    }

    .rest-mode-btn.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 2px 4px rgba(37,99,235,0.25);
    }

    .rest-target-control {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* MAIN SPLIT */
    .rest-main-split {
        display: flex;
        gap: 10px;
        flex: 1;
        overflow: hidden;
        height: 100%;
    }

    /* MENU AREA (LEFT) */
    .rest-menu-panel {
        flex: 1;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 10px 12px;
    }

    /* CATEGORIES SCROLLER */
    .rest-category-bar {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-bottom: 8px;
        white-space: nowrap;
        scrollbar-width: thin;
    }

    .category-chip {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
        user-select: none;
    }

    .category-chip:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .category-chip.active {
        background: #1e293b;
        color: #ffffff;
        border-color: #1e293b;
    }

    /* PRODUCTS GRID - FIX FOR GIANT STRETCHED CARDS */
    .rest-products-grid {
        flex: 1;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 145px));
        grid-auto-rows: 135px;
        align-content: start;
        align-items: start;
        gap: 10px;
        padding: 4px 2px;
    }

    .product-food-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        text-align: center;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease-in-out;
        height: 135px;
        width: 100%;
        box-sizing: border-box;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .product-food-card:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 10px rgba(37,99,235,0.15);
        transform: translateY(-2px);
    }

    .product-food-card:active {
        transform: scale(0.97);
    }

    .food-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 1.3rem;
        overflow: hidden;
    }

    .food-card-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .food-card-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        max-height: 2.5em;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        padding: 0 2px;
    }

    .food-card-price {
        font-size: 0.82rem;
        font-weight: 800;
        color: #059669;
        background: #ecfdf5;
        padding: 2px 10px;
        border-radius: 6px;
        border: 1px solid #d1fae5;
    }

    /* RIGHT CART PANEL */
    .rest-cart-panel {
        width: 390px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .rest-cart-header {
        background: #1e293b !important;
        border-bottom: 2px solid #3b82f6 !important;
        color: #ffffff !important;
        padding: 8px 10px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
    }

    /* Cart Mode Pill Switcher inside Cart Header */
    .cart-mode-pill {
        display: flex;
        width: 100%;
        background: #0f172a;
        padding: 3px;
        border-radius: 8px;
        border: 1px solid #334155;
        gap: 3px;
    }

    .cart-mode-pill .rest-mode-btn {
        flex: 1;
        justify-content: center;
        border: none;
        padding: 6px 4px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #cbd5e1;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        height: 32px;
        white-space: nowrap;
    }

    .cart-mode-pill .rest-mode-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.08);
    }

    .cart-mode-pill .rest-mode-btn.active {
        background: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(37,99,235,0.4) !important;
    }

    .cart-divider-row {
        border-top: 1px solid rgba(255, 255, 255, 0.12) !important;
        width: 100%;
        padding-top: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    /* Dine-in table selector button inside cart header */
    .btn-cart-table-select {
        background: #0f172a !important;
        border: 1.5px solid #38bdf8 !important;
        color: #ffffff !important;
        padding: 4px 10px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
        transition: all 0.15s ease-in-out !important;
        text-decoration: none !important;
    }
    .btn-cart-table-select:hover {
        background: #334155 !important;
        border-color: #60a5fa !important;
    }
    .btn-cart-table-select .table-tag-lbl {
        color: #94a3b8 !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
    }
    .btn-cart-table-select .table-tag-num {
        color: #ffffff !important;
        font-size: 0.95rem !important;
        font-weight: 800 !important;
    }
    .btn-cart-table-select .table-tag-floor {
        background: #3b82f6 !important;
        color: #ffffff !important;
        font-size: 0.68rem !important;
        font-weight: 700 !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
    }

    /* Takeaway token badge inside cart header */
    .badge-cart-token-box {
        background: #0f172a !important;
        border: 1.5px solid #f59e0b !important;
        color: #ffffff !important;
        padding: 4px 10px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 0.82rem !important;
        font-weight: 700 !important;
    }
    .badge-cart-token-box .token-tag-num {
        color: #fbbf24 !important;
        font-size: 0.95rem !important;
        font-weight: 800 !important;
    }

    /* Delivery badge inside cart header */
    .badge-cart-delivery-box {
        background: #0f172a !important;
        border: 1.5px solid #10b981 !important;
        color: #ffffff !important;
        padding: 4px 10px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 0.82rem !important;
        font-weight: 700 !important;
    }
    .badge-cart-delivery-box .delivery-tag-title {
        color: #34d399 !important;
        font-size: 0.88rem !important;
        font-weight: 800 !important;
    }

    /* Clear Cart Button */
    .btn-cart-clear {
        background: #ffffff !important;
        color: #dc2626 !important;
        border: 1px solid #fca5a5 !important;
        font-weight: 700 !important;
        font-size: 0.72rem !important;
        padding: 4px 8px !important;
        border-radius: 6px !important;
        cursor: pointer !important;
        transition: all 0.15s;
    }
    .btn-cart-clear:hover {
        background: #fee2e2 !important;
        color: #b91c1c !important;
    }

    .rest-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 8px 10px;
        background: #f8fafc;
    }

    .cart-row-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 8px;
        margin-bottom: 6px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .cart-row-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3px;
    }

    .cart-row-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
    }

    .cart-row-total {
        font-size: 0.85rem;
        font-weight: 800;
        color: #0f172a;
    }

    .cart-row-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }

    .cart-qty-btn {
        width: 26px;
        height: 26px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: 800;
        font-size: 0.85rem;
        color: #334155;
    }

    .cart-qty-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .cart-qty-val {
        font-weight: 800;
        font-size: 0.9rem;
        min-width: 24px;
        text-align: center;
    }

    .cart-item-note-input {
        flex: 1;
        font-size: 0.72rem;
        padding: 2px 6px;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        background: #fafafa;
        height: 26px;
    }

    .cart-row-del {
        color: #ef4444;
        cursor: pointer;
        padding: 4px;
        font-size: 0.85rem;
    }

    /* CART FOOTER */
    .rest-cart-footer {
        background: #ffffff;
        border-top: 1.5px dashed #e2e8f0;
        padding: 8px 12px;
    }

    .footer-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .action-btn-group {
        display: flex;
        gap: 6px;
    }

    .btn-kot {
        flex: 1;
        background: #ea580c;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.85rem;
        border: none;
        padding: 9px 8px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-kot:hover {
        background: #c2410c;
        color: #ffffff;
    }

    .btn-checkout {
        flex: 1.2;
        background: #16a34a;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.85rem;
        border: none;
        padding: 9px 8px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-checkout:hover {
        background: #15803d;
        color: #ffffff;
    }

    .table-btn-chip {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .table-btn-chip:hover {
        background: #e2e8f0;
    }

    .table-btn-chip.selected {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
    }

    .table-btn-chip.occupied {
        background: #fef2f2;
        border-color: #f87171;
        color: #dc2626;
    }

    .table-btn-chip.occupied.selected {
        background: #dc2626;
        color: #ffffff;
        border-color: #b91c1c;
    }

    .running-order-item {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .running-order-item:hover {
        border-color: #3b82f6;
        background: #f8fafc;
    }

    .modal-qty-val:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
        outline: none !important;
        background: #eff6ff !important;
    }

    .variant-add-btn:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px #f59e0b, 0 0 0 5px rgba(37, 99, 235, 0.5) !important;
        transform: scale(1.05);
    }
</style>

<div class="rest-pos-container">

    <!-- TOP CONTROL BAR -->
    <div class="rest-topbar">
        <!-- Left: Brand Name -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="rest-brand-badge" style="border-right: none; padding-right: 0;">
                <span class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                    <i class="fas fa-utensils text-danger"></i> {{ $branch->name ?? 'Restaurant' }}
                </span>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.7rem;">RMS POS</span>
            </div>

            @php
                $initialTable = isset($tables) && $tables->isNotEmpty() ? $tables->first() : null;
                $initialTableNo = $initialTable ? $initialTable->table_no : 'T-1';
                $initialTableFloor = $initialTable ? ($initialTable->floor_area ?: 'Main Hall') : 'Main Hall';
            @endphp
            <!-- Hidden Master Input for Order Submission -->
            <input type="hidden" id="orderTargetInput" value="{{ $initialTableNo }}">
        </div>

        <!-- Right: Active Tables Drawer & Retail Mode Toggle -->
        <div class="d-flex align-items-center gap-2">
            <!-- Sale Report Quick Link -->
            <a href="{{ route('report.sale') }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold d-flex align-items-center gap-1 py-1 px-2" style="font-size: 0.78rem;" title="View Sales Report">
                <i class="fas fa-chart-line"></i> Sales Report
            </a>

            <!-- Active Tables Badge -->
            <button type="button" class="btn btn-sm btn-outline-danger fw-bold position-relative d-flex align-items-center gap-1 py-1 px-2" data-bs-toggle="offcanvas" data-bs-target="#runningOrdersOffcanvas" style="font-size: 0.78rem;">
                <i class="fas fa-clock"></i> Active Tables
                <span class="badge bg-danger rounded-pill" id="badgeRunningCount">{{ count($runningOrders) }}</span>
            </button>

            <!-- Switch to Retail POS -->
            <a href="{{ route('pos.index', ['mode_retail' => 1]) }}" class="btn btn-sm btn-light border text-secondary fw-bold py-1 px-2" title="Switch to Retail POS" style="font-size: 0.78rem;">
                <i class="fas fa-exchange-alt me-1"></i> Retail Mode
            </a>
        </div>
    </div>

    <!-- MAIN BODY SPLIT -->
    <div class="rest-main-split">

        <!-- LEFT PANEL: MENU & PRODUCTS -->
        <div class="rest-menu-panel">
            <!-- Search & Categories Bar -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="max-width: 240px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="menuSearchInput" placeholder="Search dish..." autocomplete="off">
                </div>

                <!-- Category Pills -->
                <div class="rest-category-bar flex-grow-1 mb-0">
                    <div class="category-chip active" onclick="filterCategory('all', this)">
                        <i class="fas fa-th-large me-1"></i> All Items
                    </div>
                    @foreach($categories as $cat)
                        <div class="category-chip" onclick="filterCategory('{{ $cat->id }}', this)">
                            {{ $cat->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid - Clean Grid with Fixed Cell Dimensions -->
            <div class="rest-products-grid" id="productsGridContainer">
                @forelse($menuItems as $item)
                    <div class="product-food-card" 
                         data-id="{{ $item['id'] }}" 
                         data-category="{{ $item['category_id'] }}" 
                         data-name="{{ strtolower($item['name']) }}"
                         data-sku="{{ strtolower($item['sku']) }}"
                         data-price="{{ $item['price'] }}"
                         onclick="handleFoodCardClick({{ $item['id'] }})">
                        <div class="food-card-icon">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                            @else
                                <i class="fas fa-utensils"></i>
                            @endif
                        </div>
                        <div class="food-card-title" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                        <div class="food-card-price d-flex align-items-center justify-content-center gap-1">
                            <span>Rs. {{ number_format($item['price'], 0) }}</span>
                            @if(!empty($item['has_variants']))
                                <span class="badge bg-primary text-white" style="font-size: 0.62rem; padding: 1px 4px; border-radius: 3px;">Options</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="w-100 text-center py-5 text-muted" style="grid-column: 1 / -1;">
                        <i class="fas fa-utensils fs-1 mb-2 text-muted"></i>
                        <p class="mb-0">No dishes found in this branch.</p>
                        <small>Add dishes from the Products Catalog.</small>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT PANEL: CART & BILLING -->
        <div class="rest-cart-panel">
            <div class="rest-cart-header">
                <!-- Mode Toggle (Dine-In / Takeaway / Delivery) inside Cart Header -->
                <div class="cart-mode-pill">
                    <button type="button" class="rest-mode-btn active" id="btnModeDineIn" onclick="setOrderMode('dine_in')">
                        <i class="fas fa-chair"></i> Dine-In
                    </button>
                    <button type="button" class="rest-mode-btn" id="btnModeTakeaway" onclick="setOrderMode('takeaway')">
                        <i class="fas fa-shopping-bag"></i> Takeaway
                    </button>
                    <button type="button" class="rest-mode-btn" id="btnModeDelivery" onclick="setOrderMode('delivery')">
                        <i class="fas fa-motorcycle"></i> Delivery
                    </button>
                </div>

                <!-- Sub-header Row: Target Selection + Items Count + Clear Button -->
                <div class="cart-divider-row">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <!-- Dine-In Table Picker (Inside Cart Header) -->
                        <div id="cartHeaderDineInControl" class="d-flex align-items-center">
                            <button type="button" class="btn-cart-table-select" onclick="openTablePickerModal()" title="Click to select / change table">
                                <i class="fas fa-chair text-warning"></i>
                                <span class="table-tag-lbl">Table:</span>
                                <span class="table-tag-num" id="currentTableBadge">{{ $initialTableNo }}</span>
                                <span class="table-tag-floor" id="currentTableFloorBadge">{{ $initialTableFloor }}</span>
                                <i class="fas fa-chevron-down text-info ms-1" style="font-size: 0.65rem;"></i>
                            </button>
                        </div>

                        <!-- Takeaway Token Badge (Inside Cart Header) -->
                        <div id="cartHeaderTakeawayControl" class="d-none align-items-center">
                            <div class="badge-cart-token-box">
                                <i class="fas fa-ticket-alt text-warning"></i>
                                <span style="color: #cbd5e1; font-weight: 700;">Token #:</span>
                                <span class="token-tag-num" id="cartTokenBadge">{{ $nextTokenNo ?? 1 }}</span>
                            </div>
                        </div>

                        <!-- Delivery Badge (Inside Cart Header) -->
                        <div id="cartHeaderDeliveryControl" class="d-none align-items-center">
                            <div class="badge-cart-delivery-box">
                                <i class="fas fa-motorcycle text-success"></i>
                                <span class="delivery-tag-title">Home Delivery</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right controls: Items count badge & Clear button -->
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-sm" id="cartItemBadge" style="font-size: 0.78rem; border-radius: 6px; letter-spacing: 0.3px;">0 Items</span>
                        <button type="button" class="btn-cart-clear" onclick="resetCart()" title="Clear Entire Order">
                            <i class="fas fa-trash-alt me-1"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- CART ITEMS LIST -->
            <div class="rest-cart-items" id="cartItemsList">
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted" id="cartEmptyState">
                    <i class="fas fa-shopping-basket fs-2 text-black-50 mb-2"></i>
                    <p class="small mb-0 fw-bold">No dishes selected</p>
                    <small class="text-muted">Click any dish from the left menu</small>
                </div>
            </div>

            <!-- CART FOOTER & ACTIONS -->
            <div class="rest-cart-footer">
                <!-- Delivery Specific Fields (Shown only in Delivery mode) -->
                <div id="deliveryDetailsBox" class="p-2 mb-2 rounded border bg-light d-none" style="font-size: 0.78rem;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-primary"><i class="fas fa-motorcycle me-1"></i> Delivery & Rider Info</span>
                        <a href="{{ route('restaurant.riders.index') }}" target="_blank" class="small text-muted text-decoration-none" style="font-size: 0.7rem;">+ Riders</a>
                    </div>
                    <div class="mb-1">
                        <select class="form-select form-select-sm" id="posRiderSelect" style="font-size: 0.78rem;" onchange="onRiderSelectChange()">
                            <option value="">-- Assign Rider (Optional / Can assign later) --</option>
                            @foreach($riders as $rd)
                                <option value="{{ $rd->id }}" data-name="{{ $rd->name }}" data-phone="{{ $rd->phone }}">
                                    {{ $rd->name }} ({{ $rd->vehicle_no ?: 'Bike' }}) &mdash; {{ ucfirst(str_replace('_', ' ', $rd->duty_status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-1 mb-1">
                        <div class="col-7">
                            <input type="text" class="form-control form-control-sm" id="posCustomerPhone" placeholder="Customer Phone (03xx...)" style="font-size: 0.78rem;">
                        </div>
                        <div class="col-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text px-1" style="font-size: 0.7rem;">Del Rs</span>
                                <input type="number" class="form-control form-control-sm px-1 text-center" id="posDeliveryCharges" value="0" placeholder="0" style="font-size: 0.78rem;" oninput="renderCart()">
                            </div>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm" id="posDeliveryAddress" placeholder="Delivery Address / House / Street..." style="font-size: 0.78rem;">
                </div>

                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm mb-1" id="customerWalkinName" value="Walk-in Customer" placeholder="Customer Name (Optional)">
                    <input type="text" class="form-control form-control-sm" id="orderRemarksInput" placeholder="Special Note (e.g. Less spicy, Table request)">
                </div>

                <!-- Total Row -->
                <div class="footer-total-row">
                    <span>Total Bill:</span>
                    <span class="text-primary" id="cartTotalDisplay">Rs. 0</span>
                </div>

                <!-- Action Buttons: KOT & Pay -->
                <div class="action-btn-group">
                    <button type="button" class="btn-kot" id="btnSendKOT" onclick="submitOrder('kot')">
                        <i class="fas fa-print"></i> KOT (Kitchen)
                    </button>
                    <button type="button" class="btn-checkout" id="btnPayClose" onclick="openPaymentModal()">
                        <i class="fas fa-cash-register"></i> Pay & Settle
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- RUNNING ORDERS OFFCANVAS DRAWER -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="runningOrdersOffcanvas" style="width: 360px;">
    <div class="offcanvas-header bg-dark text-white py-2">
        <h6 class="offcanvas-title fw-bold"><i class="fas fa-clock text-warning me-2"></i>Active Running Orders</h6>
        <button type="button" class="close text-white" data-dismiss="offcanvas" onclick="$('#runningOrdersOffcanvas').removeClass('show');" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="offcanvas-body p-2" id="runningOrdersListContainer">
        @forelse($runningOrders as $ro)
            <div class="running-order-item" onclick="loadRunningOrder({{ $ro->id }})">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark fs-6">
                        @if($ro->order_type === 'dine_in')
                            🍽️ Table: {{ $ro->table_no }}
                        @elseif($ro->order_type === 'takeaway')
                            🥡 Token: {{ $ro->token_no }}
                        @else
                            🛵 {{ $ro->walkin_name }}
                        @endif
                    </span>
                    <span class="badge bg-success fs-6">Rs. {{ number_format($ro->total_bill_amount, 0) }}</span>
                </div>
                <div class="small text-muted mb-1 text-truncate">
                    {{ $ro->items->map(function($i){ return ($i->qty ?: $i->total_pieces).'x '.$i->product_name; })->implode(', ') }}
                </div>
                <div class="d-flex justify-content-between align-items-center text-secondary small">
                    <span><i class="far fa-clock me-1"></i>{{ $ro->created_at ? $ro->created_at->diffForHumans(null, true) : 'just now' }}</span>
                    <span class="text-primary fw-bold">Click to Open &raquo;</span>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fs-1 text-success mb-2"></i>
                <p>No active running orders.<br>All tables are currently clear!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- ==============================================
     TABLE SELECTION / FLOOR PLAN MODAL
     ============================================== -->
<div class="modal fade" id="tablePickerModal" tabindex="-1" aria-labelledby="tablePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fas fa-chair text-warning"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white">Select Dine-In Table</h5>
                        <small class="text-white-50">Click any table card to assign to this order</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('restaurant.tables.index') }}" target="_blank" class="btn btn-sm btn-outline-light py-1 px-3 fw-bold" style="font-size: 0.8rem; border-radius: 8px;">
                        <i class="fas fa-cog me-1"></i> Add / Manage Tables
                    </a>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#tablePickerModal').modal('hide')" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-3 p-md-4" style="background: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">
                
                <!-- Floor Filter Tabs & Status Legend -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2 flex-wrap" id="modalFloorFilters">
                        <button type="button" class="btn btn-sm btn-primary fw-bold floor-tab-btn active py-1 px-3" onclick="filterModalFloor('all', this)" style="border-radius: 8px;">
                            <i class="fas fa-th-large me-1"></i> All Floors
                        </button>
                        @if(isset($tables) && $tables->isNotEmpty())
                            @foreach($tables->pluck('floor_area')->unique()->filter() as $flName)
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold floor-tab-btn py-1 px-3" onclick="filterModalFloor('{{ $flName }}', this)" style="border-radius: 8px;">
                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $flName }}
                                </button>
                            @endforeach
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="small fw-bold text-success d-flex align-items-center gap-1">
                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#16a34a;"></span> Free / Available
                        </span>
                        <span class="small fw-bold text-danger d-flex align-items-center gap-1">
                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#dc2626;"></span> Occupied (Running Bill)
                        </span>
                    </div>
                </div>

                <!-- Grid of Interactive Tables -->
                <div class="row g-3" id="modalTablesGrid">
                    @forelse($tables as $tbl)
                        @php
                            $isOcc = $tbl->is_occupied;
                            $order = $tbl->active_order;
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 modal-table-col" data-floor="{{ $tbl->floor_area ?: 'Main Hall' }}">
                            <div class="card h-100 border-2 {{ $isOcc ? 'border-danger shadow-sm' : 'border-success' }} modal-table-card"
                                 onclick="onModalTableClick('{{ $tbl->table_no }}', '{{ $tbl->floor_area ?: 'Main Hall' }}', {{ $isOcc ? 'true' : 'false' }}, {{ $order ? $order->id : 'null' }}, '{{ $order ? $order->invoice_no : '' }}', {{ $order ? (float)$order->total_bill_amount : 0 }})"
                                 style="cursor: pointer; border-radius: 12px; transition: all 0.15s; background: {{ $isOcc ? '#fff5f5' : '#f0fdf4' }};">
                                <div class="card-body p-2 text-center d-flex flex-column justify-content-between" style="min-height: 115px;">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge {{ $isOcc ? 'bg-danger' : 'bg-success' }} text-white py-1 px-1" style="font-size: 0.65rem; border-radius: 4px;">
                                                {{ $isOcc ? 'Occupied' : 'Available' }}
                                            </span>
                                            <span class="small text-muted fw-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-user-friends"></i> {{ $tbl->capacity }}p
                                            </span>
                                        </div>
                                        <div class="fs-4 fw-bold {{ $isOcc ? 'text-danger' : 'text-dark' }} mt-1 mb-0">
                                            {{ $tbl->table_no }}
                                        </div>
                                        <div class="small text-muted text-truncate" style="font-size: 0.72rem;">
                                            {{ $tbl->floor_area ?: 'Main Hall' }}
                                        </div>
                                    </div>

                                    @if($isOcc && $order)
                                        <div class="mt-2 pt-1 border-top border-danger-subtle text-danger font-weight-bold" style="font-size: 0.75rem;">
                                            Rs. {{ number_format($order->total_bill_amount, 0) }}
                                            <div class="text-muted small" style="font-size: 0.68rem;">#{{ $order->invoice_no }}</div>
                                        </div>
                                    @else
                                        <div class="mt-2 pt-1 border-top border-success-subtle text-success small fw-bold" style="font-size: 0.72rem;">
                                            <i class="fas fa-check me-1"></i> Tap to Pick
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-chair text-muted mb-2" style="font-size: 2.5rem; opacity: 0.5;"></i>
                            <h6 class="fw-bold text-dark">No Tables Found</h6>
                            <p class="small text-muted mb-3">You have not created tables for this branch yet.</p>
                            <a href="{{ route('restaurant.tables.index') }}" target="_blank" class="btn btn-sm btn-primary fw-bold">
                                <i class="fas fa-plus me-1"></i> Create Tables Now
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Custom / Manual Table Option (Collapsible) -->
                <div class="mt-4 pt-3 border-top text-center">
                    <button class="btn btn-sm btn-link text-muted fw-bold text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#customTableCollapse">
                        <i class="fas fa-keyboard me-1"></i> Need a custom / unregistered table name?
                    </button>
                    <div class="collapse mt-2" id="customTableCollapse">
                        <div class="d-inline-flex align-items-center gap-2 bg-white p-2 border rounded shadow-sm">
                            <input type="text" id="manualCustomTableInput" class="form-control form-control-sm" placeholder="e.g. Lawn-1, VIP-Corner" style="width: 200px;">
                            <button type="button" class="btn btn-sm btn-secondary fw-bold" onclick="selectManualCustomTable()">Set Custom</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- VARIANTS SELECTION POPUP MODAL -->
<div class="modal fade" id="variantsModal" tabindex="-1" aria-labelledby="variantsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2">
                <h5 class="modal-title fs-6 fw-bold" id="variantsModalLabel">Select Product Variant</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="p-2 bg-light border-bottom">
                    <input type="text" id="variantSearchInput" class="form-control form-control-sm" placeholder="Search variant size / color..." autocomplete="off">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="font-size: 12px;">Variant Size / Color</th>
                                <th class="text-end" style="font-size: 12px;">Price</th>
                                <th class="text-center" style="width: 140px; font-size: 12px;">Quantity</th>
                                <th class="text-center" style="width: 120px; font-size: 12px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="variantsModalList">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#variantsModal').modal('hide')">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- PAYMENT & SETTLEMENT MODAL -->
<div class="modal fade" id="settlementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i>Bill Settlement & Payment</h6>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#settlementModal').modal('hide')" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center mb-3">
                    <div class="text-muted small">Total Payable Amount</div>
                    <div class="fs-2 fw-bold text-dark" id="modalPayableAmount">Rs. 0</div>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary d-block mb-1">
                        <i class="fas fa-wallet me-1 text-primary"></i> Payment Method / Account
                    </label>
                    <select class="form-control fw-bold" id="paymentAccountSelect" style="height: 40px; width: 100%; font-size: 0.9rem;">
                        @forelse($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>
                                {{ $acc->title }} ({{ optional($acc->head)->name ?? 'Cash/Bank' }})
                            </option>
                        @empty
                            <option value="1">Cash Account (Default)</option>
                        @endforelse
                    </select>
                </div>

                <!-- Cash Tendered -->
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Cash Received from Customer</label>
                    <input type="number" class="form-control form-control-lg fw-bold text-center" id="cashReceivedInput" placeholder="0" oninput="calculateChange()">
                    
                    <!-- Quick Presets -->
                    <div class="d-flex gap-1 mt-2 justify-content-center" id="quickCashButtons">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setCashPreset(500)">Rs. 500</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setCashPreset(1000)">Rs. 1000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setCashPreset(2000)">Rs. 2000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setCashPreset(5000)">Rs. 5000</button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 fw-bold" onclick="setExactCash()">Exact Cash</button>
                    </div>
                </div>

                <!-- Change Due -->
                <div class="p-2 rounded bg-light border text-center mb-2">
                    <span class="text-muted small">Change to Return: </span>
                    <span class="fw-bold fs-5 text-danger" id="modalChangeDue">Rs. 0</span>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#settlementModal').modal('hide')">Cancel</button>
                <button type="button" class="btn btn-success btn-sm px-4 fw-bold" id="btnConfirmPayment" onclick="submitOrder('post')">
                    <i class="fas fa-print me-1"></i> Complete & Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // State
    let cart = [];
    let currentOrderMode = 'dine_in';
    let activeSaleId = null;
    let nextToken = {{ $nextTokenNo ?? 1 }};

    function setOrderMode(mode) {
        currentOrderMode = mode;
        $('.rest-mode-btn').removeClass('active');
        if (mode === 'dine_in') {
            $('#btnModeDineIn').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-none').addClass('d-flex');
            $('#cartHeaderTakeawayControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderDeliveryControl').removeClass('d-flex').addClass('d-none');
            $('#deliveryDetailsBox').addClass('d-none');
            
            let curTbl = $('#currentTableBadge').text().trim() || 'T-1';
            $('#orderTargetInput').val(curTbl);
        } else if (mode === 'takeaway') {
            $('#btnModeTakeaway').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderTakeawayControl').removeClass('d-none').addClass('d-flex');
            $('#cartHeaderDeliveryControl').removeClass('d-flex').addClass('d-none');
            $('#deliveryDetailsBox').addClass('d-none');
            
            $('#cartTokenBadge').text(nextToken);
            $('#orderTargetInput').val(nextToken);
        } else if (mode === 'delivery') {
            $('#btnModeDelivery').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderTakeawayControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderDeliveryControl').removeClass('d-none').addClass('d-flex');
            $('#deliveryDetailsBox').removeClass('d-none');
            
            onRiderSelectChange();
        }
    }

    // Handle Delivery Box Rider Dropdown Change in Cart Footer
    function onRiderSelectChange() {
        let opt = $('#posRiderSelect option:selected');
        let rId = opt.val() || '';
        let rName = opt.data('name') || '';
        if (rId && rName) {
            $('#orderTargetInput').val('Rider: ' + rName);
        } else {
            $('#orderTargetInput').val('Delivery Order');
        }
    }

    // Open Floor Plan Table Picker Modal
    function openTablePickerModal() {
        $('#tablePickerModal').modal('show');
    }

    // Filter Floor in Modal
    function filterModalFloor(floor, btn) {
        $('.floor-tab-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(btn).addClass('active btn-primary').removeClass('btn-outline-secondary');

        if (floor === 'all') {
            $('.modal-table-col').show();
        } else {
            $('.modal-table-col').hide();
            $(`.modal-table-col[data-floor="${floor}"]`).show();
        }
    }

    // Handle Table Card Click inside Modal
    function onModalTableClick(tableNo, floor, isOcc, orderId, invoiceNo, totalBill) {
        if (isOcc && orderId) {
            Swal.fire({
                title: `Table ${tableNo} is Occupied`,
                html: `This table currently has an active order <b>#${invoiceNo}</b> (Rs. ${totalBill.toLocaleString()}).<br><br>What would you like to do?`,
                icon: 'info',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#2563eb',
                denyButtonColor: '#16a34a',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-folder-open me-1"></i> Open Running Order',
                denyButtonText: '<i class="fas fa-plus me-1"></i> Start New Order on Table',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#tablePickerModal').modal('hide');
                    loadRunningOrder(orderId);
                } else if (result.isDenied) {
                    selectFloorTable(tableNo, floor);
                }
            });
        } else {
            selectFloorTable(tableNo, floor);
        }
    }

    // Select Table and Close Modal
    function selectFloorTable(tableNo, floor) {
        $('#orderTargetInput').val(tableNo);
        $('#currentTableBadge').text(tableNo);
        $('#currentTableFloorBadge').text(floor || 'Main Hall');
        updateCartHeaderTarget();
        $('#tablePickerModal').modal('hide');
    }

    // Select Manual Custom Table
    function selectManualCustomTable() {
        let customName = $('#manualCustomTableInput').val().trim();
        if (!customName) {
            Swal.fire('Please enter table name', '', 'warning');
            return;
        }
        selectFloorTable(customName, 'Custom');
    }

    function updateCartHeaderTarget() {
        let val = $('#orderTargetInput').val() || '';
        let label = currentOrderMode === 'dine_in' ? 'TABLE ' : (currentOrderMode === 'takeaway' ? 'TOKEN #' : 'DELIVERY: ');
        $('#cartHeaderTarget').text(label + val);
    }

    // Category Filter
    function filterCategory(catId, btn) {
        $('.category-chip').removeClass('active');
        $(btn).addClass('active');
        if (catId === 'all') {
            $('.product-food-card').show();
        } else {
            $('.product-food-card').hide();
            $(`.product-food-card[data-category="${catId}"]`).show();
        }
    }

    // Search Filter
    $('#menuSearchInput').on('input', function() {
        let q = $(this).val().toLowerCase().trim();
        if (!q) {
            $('.product-food-card').show();
            return;
        }
        $('.product-food-card').each(function() {
            let name = $(this).data('name') || '';
            let sku = $(this).data('sku') || '';
            if (name.includes(q) || sku.includes(q)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // All menu items mapped by ID
    const menuItems = @json($menuItems->keyBy('id'));
    let currentModalProduct = null;

    // Handle Food Card Click (Check for Variants)
    function handleFoodCardClick(id) {
        let item = menuItems[id];
        if (!item) {
            console.error('Menu item not found for ID:', id);
            return;
        }
        if (item.has_variants && item.variants && item.variants.length > 0) {
            openVariantsModal(item);
        } else {
            addToCart(item);
        }
    }

    // Open Variants Selection Modal
    function openVariantsModal(product) {
        currentModalProduct = product;
        $('#variantsModalLabel').text(`Select Product Variant - ${product.name}`);
        let $tbody = $('#variantsModalList');
        $tbody.empty();
        $('#variantSearchInput').val('');

        product.variants.forEach((v, idx) => {
            let desc = `${v.size_val !== '-' ? v.size_val : ''} ${v.color_val !== '-' ? '(' + v.color_val + ')' : ''}`.trim();
            if (!desc || desc === '') {
                desc = v.name || 'Standard Variant';
            }
            let price = parseFloat(v.price) || 0;

            let row = `
                <tr id="variant-row-${idx}">
                    <td class="ps-3 fw-bold" style="font-size: 13px;">${desc}</td>
                    <td class="text-end fw-bold">Rs ${price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center mx-auto" style="width: 100px; gap: 4px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fw-bold modal-qty-minus" style="line-height:1.2;">&minus;</button>
                            <input type="number" class="form-control form-control-sm text-center fw-bold modal-qty-val" value="1" min="1" style="width: 44px; padding: 2px 4px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fw-bold modal-qty-plus" style="line-height:1.2;">&plus;</button>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold variant-add-btn" onclick="addVariantToCart(${idx}, this)">
                            <i class="fas fa-plus me-1"></i> ADD
                        </button>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });

        // Show modal (works cleanly in Bootstrap 4 and 5)
        $('#variantsModal').modal('show');

        // Focus & select quantity field immediately
        setTimeout(() => {
            let $firstQty = $('#variantsModalList tr:visible:first .modal-qty-val');
            if ($firstQty.length) {
                $firstQty.focus().select();
            }
        }, 120);
    }

    // Auto-focus quantity field whenever variants modal opens
    $('#variantsModal').on('shown.bs.modal', function () {
        setTimeout(() => {
            let $firstQty = $('#variantsModalList tr:visible:first .modal-qty-val');
            if ($firstQty.length) {
                $firstQty.focus().select();
            }
        }, 50);
    });

    // Enter in Quantity field focuses the corresponding ADD button
    $(document).on('keydown', '.modal-qty-val', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            let $addBtn = $(this).closest('tr').find('.variant-add-btn');
            if ($addBtn.length) {
                $addBtn.focus();
            }
        }
    });

    // Enter on ADD button triggers click / adds to cart
    $(document).on('keydown', '.variant-add-btn', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            $(this).click();
        }
    });

    // Modal Qty Increment/Decrement
    $(document).on('click', '.modal-qty-plus', function() {
        let $inp = $(this).siblings('.modal-qty-val');
        $inp.val((parseInt($inp.val()) || 0) + 1);
    });
    $(document).on('click', '.modal-qty-minus', function() {
        let $inp = $(this).siblings('.modal-qty-val');
        let cur = parseInt($inp.val()) || 1;
        if (cur > 1) $inp.val(cur - 1);
    });

    // Variant Search Filter
    $('#variantSearchInput').on('input', function() {
        let term = $(this).val().toLowerCase().trim();
        $('#variantsModalList tr').each(function() {
            let text = $(this).find('td:first').text().toLowerCase();
            if (text.includes(term)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Add Variant to Cart
    function addVariantToCart(idx, btn) {
        if (!currentModalProduct || !currentModalProduct.variants || !currentModalProduct.variants[idx]) {
            console.error('Variant not found at index:', idx);
            return;
        }
        let v = currentModalProduct.variants[idx];

        let qty = 1;
        if (btn) {
            let $row = $(btn).closest('tr');
            qty = parseInt($row.find('.modal-qty-val').val()) || 1;
            if (qty <= 0) qty = 1;
        }

        let cartKey = v.id + '_' + (v.variant_data || v.name);
        let existing = cart.find(x => x.cartKey === cartKey);
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({
                cartKey: cartKey,
                id: v.id,
                name: v.name,
                price: parseFloat(v.price) || 0,
                qty: qty,
                notes: '',
                variant_data: v.variant_data || ''
            });
        }

        renderCart();

        if (btn) {
            let $btn = $(btn);
            let origHtml = $btn.html();
            $btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check me-1"></i> ADDED');
            setTimeout(() => {
                $btn.removeClass('btn-success').addClass('btn-primary').html(origHtml);
                $('#variantsModal').modal('hide');
            }, 300);
        } else {
            $('#variantsModal').modal('hide');
        }
    }

    // Add Standard Product to Cart
    function addToCart(item) {
        let cartKey = String(item.id);
        let existing = cart.find(x => x.cartKey === cartKey);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                cartKey: cartKey,
                id: item.id,
                name: item.name,
                price: parseFloat(item.price) || 0,
                qty: 1,
                notes: '',
                variant_data: ''
            });
        }
        renderCart();
    }

    // Render Cart
    function renderCart() {
        let $list = $('#cartItemsList');
        if (cart.length === 0) {
            $list.html(`
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted" id="cartEmptyState">
                    <i class="fas fa-shopping-basket fs-2 text-black-50 mb-2"></i>
                    <p class="small mb-0 fw-bold">No dishes selected</p>
                    <small class="text-muted">Click any dish from the left menu</small>
                </div>
            `);
            $('#cartItemBadge').text('0 Items');
            $('#cartTotalDisplay').text('Rs. 0');
            return;
        }

        let total = 0;
        let count = 0;
        let html = '';

        cart.forEach((item, index) => {
            let lineTotal = item.qty * item.price;
            total += lineTotal;
            count += item.qty;

            html += `
                <div class="cart-row-item">
                    <div class="cart-row-top">
                        <span class="cart-row-title">${item.name}</span>
                        <span class="cart-row-total">Rs. ${lineTotal.toLocaleString()}</span>
                    </div>
                    <div class="cart-row-bottom">
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="cart-qty-btn" onclick="updateQty(${index}, -1)">&minus;</button>
                            <span class="cart-qty-val">${item.qty}</span>
                            <button type="button" class="cart-qty-btn" onclick="updateQty(${index}, 1)">&plus;</button>
                        </div>
                        <input type="text" class="cart-item-note-input" placeholder="Notes (e.g. Spicy)" value="${item.notes || ''}" onchange="updateNote(${index}, this.value)">
                        <i class="fas fa-trash-alt cart-row-del" onclick="removeFromCart(${index})" title="Remove item"></i>
                    </div>
                </div>
            `;
        });

        $list.html(html);
        $('#cartItemBadge').text(count + ' Items');
        $('#cartTotalDisplay').text('Rs. ' + total.toLocaleString());
    }

    function updateQty(index, delta) {
        if (!cart[index]) return;
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function updateNote(index, val) {
        if (!cart[index]) return;
        cart[index].notes = val;
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function resetCart() {
        cart = [];
        activeSaleId = null;
        $('#orderRemarksInput').val('');
        $('#customerWalkinName').val('Walk-in Customer');
        updateCartHeaderTarget();
        renderCart();
    }

    function getTotalBill() {
        let itemsTotal = cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
        let deliveryCharges = currentOrderMode === 'delivery' ? (parseFloat($('#posDeliveryCharges').val()) || 0) : 0;
        return itemsTotal + deliveryCharges;
    }

    // Payment Modal
    function openPaymentModal() {
        if (cart.length === 0) {
            Swal.fire('Empty Order', 'Please add items to the order first.', 'warning');
            return;
        }
        let total = getTotalBill();
        $('#modalPayableAmount').text('Rs. ' + total.toLocaleString());
        $('#cashReceivedInput').val(total);
        calculateChange();
        $('#settlementModal').modal('show');
    }

    function setCashPreset(val) {
        $('#cashReceivedInput').val(val);
        calculateChange();
    }

    function setExactCash() {
        let total = getTotalBill();
        $('#cashReceivedInput').val(total);
        calculateChange();
    }

    function calculateChange() {
        let total = getTotalBill();
        let received = parseFloat($('#cashReceivedInput').val()) || 0;
        let change = Math.max(0, received - total);
        $('#modalChangeDue').text('Rs. ' + change.toLocaleString());
    }

    // Center Print Function for Thermal Receipts & KOT directly in the main screen
    function printThermalDirect(url) {
        if (!url) return;
        let cleanUrl = url + (url.indexOf('?') > -1 ? '&' : '?') + 'from=restaurant_pos';

        try {
            let iframe = document.getElementById('restaurantThermalPrintIframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'restaurantThermalPrintIframe';
                iframe.name = 'restaurantThermalPrintIframe';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '300px';
                iframe.style.height = '300px';
                iframe.style.opacity = '0.01';
                iframe.style.pointerEvents = 'none';
                iframe.style.zIndex = '-9999';
                iframe.style.border = 'none';
                document.body.appendChild(iframe);
            }
            iframe.src = cleanUrl;
        } catch (e) {
            console.error('Direct iframe print error, using centered window:', e);
            openCenteredPrintWindow(cleanUrl, 520, 750);
        }
    }

    // Helper to open a popup strictly in the center of the screen
    function openCenteredPrintWindow(url, w = 520, h = 750) {
        if (!url) return;
        let cleanUrl = url + (url.indexOf('?') > -1 ? '&' : '?') + 'from=popup';

        const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const left = ((width / 2) - (w / 2)) + dualScreenLeft;
        const top = ((height / 2) - (h / 2)) + dualScreenTop;

        const newWin = window.open(
            cleanUrl,
            '_blank',
            `toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=${w},height=${h},top=${top},left=${left}`
        );
        if (newWin && window.focus) {
            newWin.focus();
        }
        return newWin;
    }

    // Submit Order (KOT or Post)
    function submitOrder(actionType) {
        if (cart.length === 0) {
            Swal.fire('Empty Order', 'Please select at least 1 item.', 'warning');
            return;
        }

        let target = $('#orderTargetInput').val() || '';
        let walkin = $('#customerWalkinName').val() || 'Walk-in Customer';
        let remarks = $('#orderRemarksInput').val() || '';
        let total = getTotalBill();
        let cash = actionType === 'post' ? (parseFloat($('#cashReceivedInput').val()) || total) : 0;
        let selectedAccountId = $('#paymentAccountSelect').val() || 9;

        let riderId = $('#posRiderSelect').val() || null;
        let selectedRiderOpt = $('#posRiderSelect option:selected');
        let riderName = selectedRiderOpt.data('name') || ($('#orderTargetInput').val() || null);
        let riderPhone = $('#posCustomerPhone').val() || selectedRiderOpt.data('phone') || null;
        let deliveryAddress = $('#posDeliveryAddress').val() || null;
        let deliveryCharges = parseFloat($('#posDeliveryCharges').val()) || 0;

        let formData = {
            _token: '{{ csrf_token() }}',
            action: actionType === 'post' ? 'post' : 'running',
            order_type: currentOrderMode,
            table_no: currentOrderMode === 'dine_in' ? target : null,
            token_no: currentOrderMode === 'takeaway' ? target : null,
            rider_id: currentOrderMode === 'delivery' ? riderId : null,
            rider_name: currentOrderMode === 'delivery' ? riderName : null,
            rider_phone: currentOrderMode === 'delivery' ? riderPhone : null,
            delivery_address: currentOrderMode === 'delivery' ? deliveryAddress : null,
            delivery_charges: currentOrderMode === 'delivery' ? deliveryCharges : 0,
            walkin_name: walkin,
            is_walkin: '1',
            reference: remarks,
            order_notes: remarks,
            cash: cash,
            change: actionType === 'post' ? Math.max(0, cash - total) : 0,
            receipt_account_id: actionType === 'post' ? [selectedAccountId] : [],
            receipt_amount: actionType === 'post' ? [total] : [],
            product_id: cart.map(x => x.id),
            qty: cart.map(x => x.qty),
            price_per_piece: cart.map(x => x.price),
            item_notes: cart.map(x => x.notes || ''),
            color: cart.map(x => x.variant_data || '')
        };

        let submitUrl = activeSaleId ? `{{ url('sales') }}/${activeSaleId}` : `{{ route('sales.store') }}`;
        let submitMethod = activeSaleId ? 'PUT' : 'POST';
        if (activeSaleId) {
            formData._method = 'PUT';
        }

        let loadingTitle = actionType === 'post' ? 'Processing Bill...' : 'Sending to Kitchen...';
        Swal.fire({
            title: loadingTitle,
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: submitUrl,
            method: submitMethod,
            data: formData,
            success: function(res) {
                Swal.close();
                $('#settlementModal').modal('hide');

                if (res.ok || res.success) {
                    if (actionType === 'kot') {
                        let kotUrl = res.kot_url || `{{ url('sales') }}/${res.booking_id || res.sale_id}/kot`;
                        printThermalDirect(kotUrl);

                        Swal.fire({
                            icon: 'success',
                            title: 'Sent to Kitchen!',
                            html: `
                                <div class="text-muted small mb-2">KOT ticket printed. Table is active.</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="openCenteredPrintWindow('${kotUrl}', 520, 750)">
                                    <i class="fas fa-print me-1"></i> Re-print KOT
                                </button>
                            `,
                            timer: 2500,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    } else {
                        let receiptUrl = res.receipt_url || res.invoice_url || `{{ url('sales') }}/${res.booking_id || res.sale_id}/recepit`;
                        printThermalDirect(receiptUrl);

                        // If delivery order, also print delivery challan slip
                        if (currentOrderMode === 'delivery') {
                            setTimeout(() => {
                                let slipUrl = `{{ url('restaurant/deliveries') }}/${res.booking_id || res.sale_id}/slip`;
                                printThermalDirect(slipUrl);
                            }, 1200);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Bill Completed!',
                            html: `
                                <div class="text-muted small mb-2">Payment received and receipt printed.</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="openCenteredPrintWindow('${receiptUrl}', 520, 750)">
                                    <i class="fas fa-print me-1"></i> Re-print Receipt
                                </button>
                            `,
                            timer: 2500,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    }
                } else {
                    Swal.fire('Error', res.msg || 'Failed to save order.', 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                let msg = 'Could not save order.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    // Refresh Active Orders
    function refreshActiveOrders() {
        $.get('{{ route("restaurant.active_orders") }}', function(res) {
            if (res.success) {
                $('#badgeRunningCount').text(res.count);
                let html = '';
                if (res.orders.length === 0) {
                    html = `
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fs-1 text-success mb-2"></i>
                            <p>No active running orders.<br>All tables are currently clear!</p>
                        </div>
                    `;
                } else {
                    res.orders.forEach(o => {
                        let icon = o.order_type === 'dine_in' ? '🍽️ Table: ' + (o.table_no || '-') : (o.order_type === 'takeaway' ? '🥡 Token: ' + (o.token_no || '-') : '🛵 ' + (o.walkin_name || 'Delivery'));
                        html += `
                            <div class="running-order-item" onclick="loadRunningOrder(${o.id})">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark fs-6">${icon}</span>
                                    <span class="badge bg-success fs-6">Rs. ${o.total_bill.toLocaleString()}</span>
                                </div>
                                <div class="small text-muted mb-1 text-truncate">${o.items_summary}</div>
                                <div class="d-flex justify-content-between align-items-center text-secondary small">
                                    <span><i class="far fa-clock me-1"></i>${o.elapsed_time}</span>
                                    <span class="text-primary fw-bold">Click to Open &raquo;</span>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#runningOrdersListContainer').html(html);
            }
        });
    }

    // Load Running Order back into Cart
    function loadRunningOrder(id) {
        Swal.fire({
            title: 'Loading Table Order...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.get(`{{ url('restaurant/order') }}/${id}`, function(res) {
            Swal.close();
            if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas && typeof bootstrap.Offcanvas.getInstance === 'function') {
                let bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('runningOrdersOffcanvas'));
                if (bsOffcanvas) bsOffcanvas.hide();
            }
            $('#runningOrdersOffcanvas').removeClass('show');
            $('.offcanvas-backdrop').remove();

            if (res.success && res.sale) {
                let s = res.sale;
                activeSaleId = s.id;
                setOrderMode(s.order_type || 'dine_in');

                if (s.order_type === 'dine_in') {
                    let tNo = s.table_no || 'T-1';
                    $('#orderTargetInput').val(tNo);
                    $('#currentTableBadge').text(tNo);
                } else if (s.order_type === 'takeaway') {
                    let tok = s.token_no || '1';
                    $('#orderTargetInput').val(tok);
                    $('#cartTokenBadge').text(tok);
                } else {
                    let rdr = s.rider_name || 'Delivery Order';
                    $('#orderTargetInput').val(rdr);
                    if (s.rider_id) {
                        $('#posRiderSelect').val(s.rider_id);
                    }
                    if (s.delivery_charges) {
                        $('#posDeliveryCharges').val(s.delivery_charges);
                    }
                    if (s.delivery_address) {
                        $('#posDeliveryAddress').val(s.delivery_address);
                    }
                    if (s.customer_phone) {
                        $('#posCustomerPhone').val(s.customer_phone);
                    }
                }
                $('#customerWalkinName').val(s.walkin_name || 'Walk-in Customer');
                $('#orderRemarksInput').val(s.order_notes || '');

                updateCartHeaderTarget();

                cart = s.items.map(it => {
                    let vData = it.variant_data || '';
                    let cartKey = it.id + (vData ? '_' + vData : '');
                    return {
                        cartKey: cartKey,
                        id: it.id,
                        name: it.name,
                        price: it.price,
                        qty: it.qty,
                        notes: it.item_notes || '',
                        variant_data: vData
                    };
                });

                renderCart();
            }
        }).fail(function() {
            Swal.close();
            Swal.fire('Error', 'Failed to load order.', 'error');
        });
    }

    setInterval(refreshActiveOrders, 30000);
</script>
@endsection
