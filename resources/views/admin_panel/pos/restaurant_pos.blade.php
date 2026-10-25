@extends('admin_panel.layout.app')

@section('content')
<style>
    /* =========================================================
       GLOBAL RESET & VIEWPORT CONTAINMENT (ZERO HORIZONTAL SCROLL)
       ========================================================= */
    html, body {
        overflow-x: hidden !important;
        width: 100% !important;
        max-width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        background-color: #f1f5f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        -webkit-text-size-adjust: 100%;
        touch-action: manipulation;
    }

    * {
        box-sizing: border-box !important;
    }

    footer { display: none !important; }

    /* Desktop Viewport Wrapper */
    @media (min-width: 992px) {
        html, body {
            overflow: hidden !important;
            height: 100% !important;
        }
        .page-container, .main-panel, .content-wrapper, .main-content-inner, .main-content {
            height: calc(100vh - 128px) !important;
            overflow: hidden !important;
            padding: 4px 10px !important;
            margin: 0 !important;
        }
        .rest-pos-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 138px);
            gap: 8px;
            max-width: 100vw;
            overflow: hidden;
        }
    }

    /* =========================================================
       TOP CONTROL BAR (DESKTOP)
       ========================================================= */
    .rest-topbar {
        background: #ffffff;
        border-radius: 10px;
        padding: 7px 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        border: 1px solid #e2e8f0;
    }

    .rest-brand-badge {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rms-pos-badge {
        background: #fee2e2;
        color: #ef4444;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #fecaca;
        letter-spacing: 0.5px;
    }

    /* Order Mode Switcher */
    .rest-mode-pill {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 8px;
        gap: 3px;
        border: 1px solid #e2e8f0;
    }

    .rest-mode-btn {
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        white-space: nowrap;
    }

    .rest-mode-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .rest-mode-btn.active {
        background: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 2px 5px rgba(37,99,235,0.3) !important;
    }

    /* =========================================================
       MAIN SPLIT (DESKTOP)
       ========================================================= */
    .rest-main-split {
        display: flex;
        gap: 10px;
        flex: 1;
        overflow: hidden;
        height: 100%;
        max-width: 100%;
    }

    /* MENU AREA (LEFT) */
    .rest-menu-panel {
        flex: 1;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 8px 12px;
        min-width: 0;
    }

    /* CATEGORIES & SEARCH SCROLLER */
    .rest-category-bar {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 2px;
        margin-bottom: 0;
        white-space: nowrap;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
    }
    .rest-category-bar::-webkit-scrollbar {
        display: none;
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
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* PRODUCTS GRID */
    .rest-products-grid {
        flex: 1;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        align-content: start;
        gap: 8px;
        padding: 6px 2px;
    }
    .rest-products-grid::-webkit-scrollbar {
        width: 5px;
    }
    .rest-products-grid::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .product-food-card {
        background: #ffffff;
        border: 1.2px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 8px;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
        transition: all 0.12s ease-in-out;
        min-height: 72px;
        height: auto;
        box-sizing: border-box;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        position: relative;
    }

    .product-food-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 3px 8px rgba(37,99,235,0.12);
        transform: translateY(-1px);
    }

    .product-food-card:active {
        transform: scale(0.98);
    }

    .product-food-card.highlighted-card {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3) !important;
        background-color: #f0f7ff !important;
    }

    .food-card-thumb {
        width: 54px;
        height: 54px;
        border-radius: 8px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 1.3rem;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid #f1f5f9;
    }

    .food-card-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .food-card-details {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 3px;
    }

    .food-card-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .food-card-price {
        font-size: 0.84rem;
        font-weight: 800;
        color: #059669;
        line-height: 1.2;
    }

    .btn-card-options {
        background: #2563eb;
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: background 0.12s;
        line-height: 1.3;
    }

    .btn-card-options:hover {
        background: #1d4ed8;
    }

    /* Fast Keyboard Hints & Badges */
    .rest-shortcuts-hint-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 8px;
        padding: 5px 10px;
        margin-bottom: 8px;
        font-size: 0.73rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .sc-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #cbd5e1;
        font-weight: 600;
        white-space: nowrap;
    }
    .sc-tag kbd, .mode-kbd {
        background: #2563eb;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.67rem;
        padding: 2px 5px;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.25);
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .sc-tag-highlight kbd {
        background: #10b981;
    }
    .selected-variant-row {
        background-color: #eff6ff !important;
        border-left: 4px solid #2563eb !important;
    }
    .highlighted-card {
        outline: 2.5px solid #2563eb !important;
        background: #eff6ff !important;
        box-shadow: 0 4px 12px rgba(37,99,235,0.25) !important;
    }

    /* =========================================================
       RIGHT CART PANEL (CURRENT ORDER)
       ========================================================= */
    .rest-cart-panel {
        width: 380px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        flex-shrink: 0;
        min-width: 0;
    }

    .rest-cart-header {
        background: #0f172a !important;
        color: #ffffff !important;
        padding: 10px 14px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .rest-cart-header .cart-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-cart-count {
        background: #1e3a5f;
        color: #93c5fd;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 12px;
        letter-spacing: 0.3px;
        border: 1px solid rgba(147, 197, 253, 0.2);
    }

    .btn-cart-clear-icon {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 0.95rem;
        cursor: pointer;
        transition: color 0.15s;
        padding: 2px 4px;
    }
    .btn-cart-clear-icon:hover {
        color: #f87171;
    }

    /* Target Sub-Bar (Table / Token / Rider) */
    .cart-target-bar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-target-pill {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.12s;
    }
    .btn-target-pill:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    /* CART ITEMS LIST */
    .rest-cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 8px 10px;
        background: #f8fafc;
    }
    .rest-cart-items::-webkit-scrollbar {
        width: 4px;
    }
    .rest-cart-items::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .cart-order-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 8px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        transition: border-color 0.12s;
    }

    .cart-order-row:hover {
        border-color: #cbd5e1;
    }

    .cart-row-thumb {
        width: 42px;
        height: 42px;
        border-radius: 6px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 1rem;
        overflow: hidden;
        flex-shrink: 0;
    }

    .cart-row-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-row-info {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cart-row-item-title {
        font-size: 0.84rem;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 140px;
    }

    .cart-row-item-price {
        font-size: 0.84rem;
        font-weight: 800;
        color: #059669;
        white-space: nowrap;
        margin-left: auto;
    }

    .cart-stepper {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        flex-shrink: 0;
    }

    .btn-cart-stepper {
        width: 22px;
        height: 22px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-weight: 800;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }
    .btn-cart-stepper:hover {
        background: #e2e8f0;
    }

    .cart-stepper-val {
        font-size: 0.82rem;
        font-weight: 800;
        min-width: 18px;
        text-align: center;
    }

    .cart-row-trash-btn {
        color: #ef4444;
        cursor: pointer;
        padding: 4px;
        font-size: 0.85rem;
        background: transparent;
        border: none;
        transition: color 0.12s;
        flex-shrink: 0;
    }
    .cart-row-trash-btn:hover {
        color: #b91c1c;
    }

    /* CART FOOTER */
    .rest-cart-footer {
        background: #ffffff;
        border-top: 1.5px dashed #cbd5e1;
        padding: 8px 12px;
    }

    .cart-total-bill-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }

    .cart-total-lbl {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .cart-total-val {
        font-size: 1.4rem;
        font-weight: 900;
        color: #2563eb;
    }

    .cart-action-btn-row {
        display: flex;
        gap: 8px;
    }

    .btn-kot-red {
        flex: 1;
        background: #ef4444;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.88rem;
        border: none;
        padding: 10px 8px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-kot-red:hover {
        background: #dc2626;
        color: #ffffff;
    }

    .btn-pay-green {
        flex: 1.2;
        background: #10b981;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.88rem;
        border: none;
        padding: 10px 8px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-pay-green:hover {
        background: #059669;
        color: #ffffff;
    }

    /* =========================================================
       NATIVE MOBILE APP FEEL & COMPLETE RESPONSIVENESS (< 992px)
       ========================================================= */
    .mobile-app-header {
        display: none;
    }
    .m-bottom-app-bar {
        display: none;
    }
    .mobile-back-to-menu-btn {
        display: none;
    }
    .mobile-floating-checkout {
        display: none;
    }

    @media (max-width: 991px) {
        /* Zero desktop overflows */
        html, body {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            height: auto !important;
            width: 100% !important;
            max-width: 100vw !important;
            background: #f8fafc !important;
        }

        .container-scroller, .app-page-body, .page-container, .main-panel, .content-wrapper, .main-content-inner, .main-content {
            height: auto !important;
            overflow: visible !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        /* Hide desktop bulk navigation on mobile POS */
        .rt_nav_header.horizontal-layout .nav-bottom {
            display: none !important;
        }

        /* Hide desktop topbar and show sleek mobile app header */
        .desktop-topbar {
            display: none !important;
        }

        .mobile-app-header {
            display: block;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
            width: 100%;
        }

        .mobile-header-brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }

        .mobile-brand-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .mobile-mode-segmented {
            display: flex;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 3px;
            gap: 3px;
            width: 100%;
            border: 1px solid #e2e8f0;
        }

        .m-seg-btn {
            flex: 1;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 7px 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .m-seg-btn.active {
            background: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(37,99,235,0.25);
        }

        /* Main container padding for bottom bar */
        .rest-pos-container {
            height: auto !important;
            min-height: 100vh;
            padding: 8px 8px 80px 8px !important;
            gap: 8px;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        .rest-main-split {
            flex-direction: column !important;
            height: auto !important;
            overflow: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            gap: 0 !important;
        }

        /* Panels */
        .rest-menu-panel {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            overflow: visible !important;
            padding: 10px !important;
            border-radius: 12px !important;
        }

        .rest-cart-panel {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            overflow: visible !important;
            border-radius: 12px !important;
        }

        /* Products Grid on Mobile: 1 Full-width Column on phones, 2 on small tablets */
        .rest-products-grid {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
            padding: 4px 0 !important;
            max-height: none !important;
            overflow: visible !important;
        }

        @media (min-width: 600px) and (max-width: 991px) {
            .rest-products-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        /* Mobile Food Cards - Touch Optimized App Card */
        .product-food-card {
            width: 100% !important;
            min-height: 74px !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .food-card-thumb {
            width: 58px !important;
            height: 58px !important;
            border-radius: 10px !important;
        }

        .food-card-title {
            font-size: 0.86rem !important;
        }

        .food-card-price {
            font-size: 0.88rem !important;
        }

        .btn-card-step {
            width: 26px !important;
            height: 26px !important;
            font-size: 0.95rem !important;
        }

        .card-qty-num {
            font-size: 0.88rem !important;
            min-width: 20px !important;
        }

        .btn-card-options {
            padding: 3px 9px !important;
            font-size: 0.72rem !important;
            border-radius: 6px !important;
        }

        /* Mobile Back Button in Cart Header */
        .mobile-back-to-menu-btn {
            display: inline-flex !important;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: none;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            margin-right: 8px;
        }

        /* Mobile View Toggle Logic */
        .mobile-view-hidden {
            display: none !important;
        }

        /* Floating Checkout Bar on Mobile when viewing Menu */
        .mobile-floating-checkout {
            position: fixed;
            bottom: 64px;
            left: 10px;
            right: 10px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 14px;
            padding: 10px 14px;
            display: none;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 6px 18px rgba(0,0,0,0.3);
            z-index: 1040;
            cursor: pointer;
            border: 1px solid #1e293b;
            transition: all 0.2s ease;
        }

        .mobile-floating-checkout.show-float {
            display: flex !important;
            animation: slideUpFloat 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUpFloat {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .mobile-cart-badge {
            background: #2563eb;
            color: #ffffff;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 800;
        }

        /* Fixed Mobile Bottom App Bar */
        .m-bottom-app-bar {
            display: flex !important;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            z-index: 1050;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            align-items: center;
            justify-content: space-around;
            padding: 0 4px;
        }

        .m-bar-tab {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 0;
            gap: 2px;
            cursor: pointer;
            transition: color 0.12s;
        }

        .m-bar-tab i {
            font-size: 1.15rem;
        }

        .m-bar-tab.active {
            color: #2563eb !important;
        }

        .m-bar-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
        }

        /* Modals on Mobile */
        .modal-dialog {
            width: 95% !important;
            max-width: 480px !important;
            margin: 10px auto !important;
        }
    }
</style>

<!-- MOBILE NATIVE APP HEADER (< 992px) -->
<div class="mobile-app-header d-lg-none">
    <div class="mobile-header-brand-row">
        <div class="mobile-brand-title">
            <span class="text-danger"><i class="fas fa-utensils"></i></span>
            <span class="text-truncate" style="max-width: 160px;">{{ $branch->name ?? 'Munch Fried Chicken' }}</span>
            <span class="rms-pos-badge">RMS</span>
        </div>

        <div class="d-flex align-items-center gap-1">
            <button type="button" class="btn btn-xs btn-outline-primary fw-bold py-1 px-2 d-flex align-items-center gap-1" onclick="openQuickRiderModal()" style="font-size: 0.72rem; border-radius: 6px;">
                <i class="fas fa-motorcycle"></i> + Rider
            </button>
            <button type="button" class="btn btn-xs btn-outline-danger fw-bold py-1 px-2 d-flex align-items-center gap-1" onclick="openRunningOrdersModal()" style="font-size: 0.72rem; border-radius: 6px;">
                <i class="fas fa-clock"></i> <span id="mHeaderActiveCount">{{ count($runningOrders) }}</span>
            </button>
        </div>
    </div>

    <!-- Mobile Segmented Order Mode Controller -->
    <div class="mobile-mode-segmented">
        <button type="button" class="m-seg-btn active" id="mBtnModeDineIn" onclick="setOrderMode('dine_in')">
            <i class="fas fa-chair"></i> Dine-In
        </button>
        <button type="button" class="m-seg-btn" id="mBtnModeTakeaway" onclick="setOrderMode('takeaway')">
            <i class="fas fa-shopping-bag"></i> Takeaway
        </button>
        <button type="button" class="m-seg-btn" id="mBtnModeDelivery" onclick="setOrderMode('delivery')">
            <i class="fas fa-motorcycle"></i> Delivery
        </button>
    </div>
</div>

<div class="rest-pos-container">

    <!-- DESKTOP TOP CONTROL BAR (>= 992px) -->
    <div class="rest-topbar desktop-topbar">
        <!-- Left: Brand Name + RMS POS badge + Quick Rider Button -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="rest-brand-badge">
                <span class="fw-bold text-dark fs-6 d-flex align-items-center gap-1">
                    <i class="fas fa-utensils text-danger"></i> {{ $branch->name ?? 'Munch Fried Chicken' }}
                </span>
                <span class="rms-pos-badge">RMS POS</span>
            </div>

            <!-- Quick Add Rider Button directly with Munch Fried Chicken -->
            <button type="button" class="btn btn-sm btn-outline-primary fw-bold py-1 px-2 d-flex align-items-center gap-1" onclick="openQuickRiderModal()" title="Add / View Riders">
                <i class="fas fa-motorcycle text-primary"></i>
                <span style="font-size: 0.78rem;">+ Rider</span>
            </button>

            @php
                $initialTable = isset($tables) && $tables->isNotEmpty() ? $tables->first() : null;
                $initialTableNo = $initialTable ? $initialTable->table_no : 'T-1';
                $initialTableFloor = $initialTable ? ($initialTable->floor_area ?: 'Main Hall') : 'Main Hall';
            @endphp
            <!-- Hidden Master Input for Order Submission -->
            <input type="hidden" id="orderTargetInput" value="{{ $initialTableNo }}">
        </div>

        <!-- Center / Right: Order Mode Switcher (Dine-In, Takeaway, Delivery) -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="rest-mode-pill">
                <button type="button" class="rest-mode-btn active" id="btnModeDineIn" onclick="setOrderMode('dine_in')" title="Shortcut: F1 or Alt+1">
                    <i class="fas fa-chair"></i> Dine-In <kbd class="mode-kbd">F1</kbd>
                </button>
                <button type="button" class="rest-mode-btn" id="btnModeTakeaway" onclick="setOrderMode('takeaway')" title="Shortcut: F3 or Alt+2">
                    <i class="fas fa-shopping-bag"></i> Takeaway <kbd class="mode-kbd">F3</kbd>
                </button>
                <button type="button" class="rest-mode-btn" id="btnModeDelivery" onclick="setOrderMode('delivery')" title="Shortcut: F6 or Alt+3">
                    <i class="fas fa-motorcycle"></i> Delivery <kbd class="mode-kbd">F6</kbd>
                </button>
            </div>

            <!-- Active Running Tables Badge -->
            <button type="button" class="btn btn-sm btn-outline-danger fw-bold position-relative d-flex align-items-center gap-1 py-1 px-2" onclick="openRunningOrdersModal()" style="font-size: 0.78rem;" title="View Active Running Orders">
                <i class="fas fa-clock"></i> Active Tables
                <span class="badge bg-danger rounded-pill" id="badgeRunningCount">{{ count($runningOrders) }}</span>
            </button>
        </div>
    </div>

    <!-- MAIN BODY SPLIT -->
    <div class="rest-main-split">

        <!-- LEFT PANEL: MENU & PRODUCTS -->
        <div class="rest-menu-panel" id="menuPanelContainer">
            <!-- Fast Keyboard Shortcuts Bar -->
            <div class="rest-shortcuts-hint-bar d-none d-md-flex">
                <span class="sc-tag sc-tag-highlight"><kbd>Enter</kbd> Add Dish / Settle</span>
                <span class="sc-tag"><kbd>F9</kbd> Instant Cash Pay</span>
                <span class="sc-tag"><kbd>F8</kbd> KOT</span>
                <span class="sc-tag"><kbd>F1</kbd> Dine-In</span>
                <span class="sc-tag"><kbd>F3</kbd> Takeaway</span>
                <span class="sc-tag"><kbd>F6</kbd> Delivery</span>
                <span class="sc-tag"><kbd>F5</kbd> Tables</span>
                <span class="sc-tag"><kbd>+</kbd><kbd>-</kbd> Qty</span>
                <span class="sc-tag"><kbd>Esc</kbd> Clear</span>
            </div>

            <!-- Search & Categories Bar -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="max-width: 290px; min-width: 150px; flex-shrink: 0;">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 fw-bold" id="menuSearchInput" placeholder="Dish / Code (e.g. 2*burger) [Enter]" autocomplete="off">
                </div>

                <!-- Category Pills (Smooth Single Row Scroller) -->
                <div class="rest-category-bar flex-grow-1">
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

            <!-- Products Grid -->
            <div class="rest-products-grid" id="productsGridContainer">
                @forelse($menuItems as $item)
                    <div class="product-food-card" 
                         id="foodCard_{{ $item['id'] }}"
                         data-id="{{ $item['id'] }}" 
                         data-category="{{ $item['category_id'] }}" 
                         data-name="{{ strtolower($item['name']) }}"
                         data-sku="{{ strtolower($item['sku']) }}"
                         data-price="{{ $item['price'] }}"
                         onclick="handleFoodCardClick({{ $item['id'] }})">
                        <div class="food-card-thumb">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                            @else
                                <i class="fas fa-utensils"></i>
                            @endif
                        </div>
                        <div class="food-card-details">
                            <div class="food-card-title" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div class="food-card-price">Rs. {{ number_format($item['price'], 0) }}</div>
                                @if(!empty($item['has_variants']))
                                    <button type="button" class="btn-card-options" onclick="event.stopPropagation(); handleFoodCardClick({{ $item['id'] }})">Options</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-100 text-center py-5 text-muted" style="grid-column: 1 / -1;">
                        <i class="fas fa-utensils fs-1 mb-2 text-muted"></i>
                        <p class="mb-0">No dishes found in this branch.</p>
                        <small>Add dishes from the Products / Menu Catalog.</small>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT PANEL: CART & CURRENT ORDER -->
        <div class="rest-cart-panel" id="cartPanelContainer">
            <!-- Header matching screenshot: Current Order + 0 items badge + trash icon -->
            <div class="rest-cart-header">
                <div class="cart-title">
                    <button type="button" class="mobile-back-to-menu-btn" onclick="switchMobileView('menu')">
                        <i class="fas fa-arrow-left"></i> Menu
                    </button>
                    <i class="fas fa-shopping-cart text-primary"></i>
                    <span>Current Order</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-cart-count" id="cartItemBadge">0 Items</span>
                    <button type="button" class="btn-cart-clear-icon" onclick="resetCart()" title="Clear Entire Order">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Target Selection Sub-Bar (Dine-In Table / Takeaway Token / Delivery Rider) -->
            <div class="cart-target-bar">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <!-- Dine-In Table Picker -->
                    <div id="cartHeaderDineInControl" class="d-flex align-items-center">
                        <button type="button" class="btn-target-pill" onclick="openTablePickerModal()" title="Shortcut: F5 or Alt+T">
                            <i class="fas fa-chair text-primary"></i>
                            <span style="color: #64748b;">Table:</span>
                            <span class="fw-bold text-dark" id="currentTableBadge">{{ $initialTableNo }}</span>
                            <span class="badge bg-primary text-white" style="font-size:0.65rem;" id="currentTableFloorBadge">{{ $initialTableFloor }}</span>
                            <kbd class="mode-kbd ms-1" style="font-size:9px;">F5</kbd>
                            <i class="fas fa-chevron-down text-muted ms-1" style="font-size:0.6rem;"></i>
                        </button>
                    </div>

                    <!-- Takeaway Token Badge -->
                    <div id="cartHeaderTakeawayControl" class="d-none align-items-center">
                        <div class="btn-target-pill" style="cursor: default;">
                            <i class="fas fa-ticket-alt text-warning"></i>
                            <span style="color: #64748b;">Token:</span>
                            <span class="fw-bold text-dark" id="cartTokenBadge">{{ $nextTokenNo ?? 1 }}</span>
                        </div>
                    </div>

                    <!-- Delivery Rider Control -->
                    <div id="cartHeaderDeliveryControl" class="d-none align-items-center">
                        <div class="btn-target-pill" style="cursor: default;">
                            <i class="fas fa-motorcycle text-success"></i>
                            <span class="fw-bold text-dark">Home Delivery</span>
                        </div>
                    </div>
                </div>

                <div class="small text-muted fw-bold" id="cartHeaderOrderModeBadge" style="font-size: 0.72rem;">
                    Dine-In
                </div>
            </div>

            <!-- CART ITEMS LIST (matches screenshot: image, title, price, notes input, stepper, delete icon) -->
            <div class="rest-cart-items" id="cartItemsList">
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted py-5" id="cartEmptyState">
                    <i class="fas fa-shopping-basket fs-2 text-black-50 mb-2"></i>
                    <p class="small mb-0 fw-bold">No dishes in order</p>
                    <small class="text-muted">Click or search dishes on the left</small>
                </div>
            </div>

            <!-- CART FOOTER & ACTIONS -->
            <div class="rest-cart-footer">
                <!-- Delivery Specific Fields (Shown only in Delivery mode) -->
                <div id="deliveryDetailsBox" class="p-2 mb-2 rounded border bg-light d-none" style="font-size: 0.78rem;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-primary"><i class="fas fa-motorcycle me-1"></i> Rider & Delivery Info</span>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold text-primary" style="font-size: 0.72rem;" onclick="openQuickRiderModal()">
                            + Add Rider
                        </button>
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

                <!-- Total Bill Row matching screenshot -->
                <div class="cart-total-bill-row">
                    <span class="cart-total-lbl">Total Bill:</span>
                    <span class="cart-total-val" id="cartTotalDisplay">Rs. 0</span>
                </div>

                <!-- Action Buttons: KOT (Red) & Pay & Settle (Green) matching screenshot -->
                <div class="cart-action-btn-row">
                    <button type="button" class="btn-kot-red" id="btnSendKOT" onclick="submitOrder('kot')" title="Shortcut: Shift+Enter or F8">
                        <i class="fas fa-print"></i> KOT (Kitchen) <kbd class="mode-kbd" style="background:rgba(0,0,0,0.3); font-size:10px;">F8</kbd>
                    </button>
                    <button type="button" class="btn-pay-green" id="btnPayClose" onclick="openPaymentModal()" title="Shortcut: Enter or F4">
                        <i class="fas fa-cash-register"></i> Pay & Settle <kbd class="mode-kbd" style="background:rgba(0,0,0,0.3); font-size:10px;">Enter</kbd>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- FLOATING BOTTOM CHECKOUT CAPSULE ON MOBILE VIEW (WHEN BROWSING MENU) -->
<div class="mobile-floating-checkout" id="mobileFloatingCheckout" onclick="switchMobileView('cart')">
    <div class="d-flex align-items-center gap-2">
        <div class="mobile-cart-badge"><i class="fas fa-shopping-bag"></i> <span id="mobFloatCount">0</span></div>
        <div>
            <div class="small text-white-50" style="font-size: 0.7rem; line-height: 1;">Total Bill</div>
            <div class="fw-bold text-white fs-6" id="mobFloatTotal">Rs. 0</div>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-light fw-bold px-3 py-1 text-primary shadow-sm" style="border-radius: 20px; font-size: 0.8rem;">
        View Order &raquo;
    </button>
</div>

<!-- FIXED NATIVE MOBILE APP BOTTOM NAVIGATION BAR (< 992px) -->
<div class="m-bottom-app-bar d-lg-none">
    <button type="button" class="m-bar-tab active" id="mTabMenu" onclick="switchMobileView('menu')">
        <i class="fas fa-utensils"></i>
        <span>Dishes Menu</span>
    </button>
    <button type="button" class="m-bar-tab" id="mTabTables" onclick="openRunningOrdersModal()">
        <div class="position-relative d-inline-block">
            <i class="fas fa-chair"></i>
            <span class="m-bar-badge" id="mBottomBadgeTables">{{ count($runningOrders) }}</span>
        </div>
        <span>Active Tables</span>
    </button>
    <button type="button" class="m-bar-tab" id="mTabCart" onclick="switchMobileView('cart')">
        <div class="position-relative d-inline-block">
            <i class="fas fa-shopping-cart"></i>
            <span class="m-bar-badge" id="mBottomBadgeCart">0</span>
        </div>
        <span>Order (<span id="mBottomCartTotal">Rs. 0</span>)</span>
    </button>
</div>

<!-- QUICK ADD RIDER MODAL -->
<div class="modal fade" id="quickRiderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title fw-bold text-white mb-0">
                    <i class="fas fa-motorcycle text-info me-2"></i> Add Delivery Rider
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#quickRiderModal').modal('hide')" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickRiderForm" onsubmit="submitQuickRider(event)">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1">Rider Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="qrRiderName" required placeholder="e.g. Ali Ahmed">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="qrRiderPhone" required placeholder="e.g. 03001234567">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Vehicle / Bike No</label>
                            <input type="text" class="form-control form-control-sm" id="qrVehicleNo" placeholder="e.g. LE-1234">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Status</label>
                            <select class="form-select form-select-sm" id="qrDutyStatus">
                                <option value="available" selected>Available</option>
                                <option value="on_delivery">On Delivery</option>
                                <option value="off_duty">Off Duty</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold text-dark mb-1">CNIC / ID (Optional)</label>
                        <input type="text" class="form-control form-control-sm" id="qrCnic" placeholder="35201-xxxxxxx-x">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#quickRiderModal').modal('hide')">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSaveQuickRider">
                        <i class="fas fa-save me-1"></i> Save Rider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- RUNNING ORDERS MODAL -->
<div class="modal fade" id="runningOrdersModal" tabindex="-1" aria-labelledby="runningOrdersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title fw-bold text-white mb-0" id="runningOrdersModalLabel">
                    <i class="fas fa-clock text-warning me-2"></i>Active Running Orders
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#runningOrdersModal').modal('hide')" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="runningOrdersListContainer" style="max-height: 70vh; overflow-y: auto; background: #f8fafc;">
                @forelse($runningOrders as $ro)
                    <div class="running-order-item" onclick="loadRunningOrder({{ $ro->id }})" style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px; margin-bottom:8px; cursor:pointer; transition: all 0.15s;">
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
                        <p class="mb-0 fw-bold">No active running orders.</p>
                        <small>All tables are currently clear!</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- TABLE SELECTION / FLOOR PLAN MODAL -->
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
                        <i class="fas fa-cog me-1"></i> Manage Tables
                    </a>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#tablePickerModal').modal('hide')" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-3 p-md-4" style="background: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="modalTableSearchInput" class="form-control border-start-0 fw-bold" placeholder="Table # (e.g. 1, T-1) & [Enter]..." autocomplete="off">
                    </div>

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
                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#dc2626;"></span> Occupied
                        </span>
                    </div>
                </div>

                <div class="row g-3" id="modalTablesGrid">
                    @forelse($tables as $tbl)
                        @php
                            $isOcc = $tbl->is_occupied;
                            $order = $tbl->active_order;
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 modal-table-col" data-floor="{{ $tbl->floor_area ?: 'Main Hall' }}" data-tableno="{{ strtolower($tbl->table_no) }}">
                            <div class="card h-100 border-2 {{ $isOcc ? 'border-danger shadow-sm' : 'border-success' }}"
                                 onclick="onModalTableClick('{{ $tbl->table_no }}', '{{ $tbl->floor_area ?: 'Main Hall' }}', {{ $isOcc ? 'true' : 'false' }}, {{ $order ? $order->id : 'null' }}, '{{ $order ? $order->invoice_no : '' }}', {{ $order ? (float)$order->total_bill_amount : 0 }})"
                                 style="cursor: pointer; border-radius: 12px; transition: all 0.15s; background: {{ $isOcc ? '#fff5f5' : '#f0fdf4' }};">
                                <div class="card-body p-2 text-center d-flex flex-column justify-content-between" style="min-height: 110px;">
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
                                            <i class="fas fa-check me-1"></i> Pick
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

                <!-- Custom Table Option -->
                <div class="mt-4 pt-3 border-top text-center">
                    <button class="btn btn-sm btn-link text-muted fw-bold text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#customTableCollapse">
                        <i class="fas fa-keyboard me-1"></i> Custom / unregistered table name?
                    </button>
                    <div class="collapse mt-2" id="customTableCollapse">
                        <div class="d-inline-flex align-items-center gap-2 bg-white p-2 border rounded shadow-sm">
                            <input type="text" id="manualCustomTableInput" class="form-control form-control-sm" placeholder="e.g. Lawn-1, VIP" style="width: 200px;">
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
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h5 class="modal-title fs-6 fw-bold text-white" id="variantsModalLabel">Select Product Variant</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#variantsModal').modal('hide')" aria-label="Close" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="p-2 bg-light border-bottom">
                    <input type="text" id="variantSearchInput" class="form-control form-control-sm" placeholder="Search variant size / option..." autocomplete="off">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="font-size: 12px;">Option / Size</th>
                                <th class="text-end" style="font-size: 12px;">Price</th>
                                <th class="text-center" style="width: 130px; font-size: 12px;">Quantity</th>
                                <th class="text-center" style="width: 120px; font-size: 12px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="variantsModalList">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#variantsModal').modal('hide')">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- PAYMENT & SETTLEMENT MODAL -->
<div class="modal fade" id="settlementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-2 px-3">
                <h6 class="modal-title fw-bold text-white mb-0"><i class="fas fa-check-circle me-2"></i>Bill Settlement & Payment</h6>
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
                    <label class="form-label fw-bold small text-secondary">Cash Received from Customer (Press Enter to Settle)</label>
                    <input type="number" class="form-control form-control-lg fw-bold text-center" id="cashReceivedInput" placeholder="0" oninput="calculateChange()">
                    
                    <!-- Quick Presets -->
                    <div class="d-flex gap-1 mt-2 justify-content-center flex-wrap" id="quickCashButtons">
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
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#settlementModal').modal('hide')">Cancel</button>
                <button type="button" class="btn btn-success btn-sm px-4 fw-bold" id="btnConfirmPayment" onclick="submitOrder('post')">
                    <i class="fas fa-print me-1"></i> Complete & Print Receipt [Enter]
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
    const menuItems = @json($menuItems->keyBy('id'));
    let currentModalProduct = null;
    let highlightedIndex = 0;
    let activeMobileView = 'menu';

    // Mobile View Switcher (Native App Feel)
    function switchMobileView(view) {
        activeMobileView = view;
        if ($(window).width() < 992) {
            if (view === 'menu') {
                $('#menuPanelContainer').removeClass('mobile-view-hidden').show();
                $('#cartPanelContainer').addClass('mobile-view-hidden').hide();
                $('#mTabMenu').addClass('active');
                $('#mTabCart').removeClass('active');
                if (cart.length > 0) {
                    $('#mobileFloatingCheckout').addClass('show-float');
                } else {
                    $('#mobileFloatingCheckout').removeClass('show-float');
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                $('#menuPanelContainer').addClass('mobile-view-hidden').hide();
                $('#cartPanelContainer').removeClass('mobile-view-hidden').show();
                $('#mTabCart').addClass('active');
                $('#mTabMenu').removeClass('active');
                $('#mobileFloatingCheckout').removeClass('show-float');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }

    // Set Order Mode: dine_in, takeaway, delivery
    function setOrderMode(mode) {
        currentOrderMode = mode;
        $('.rest-mode-btn').removeClass('active');
        $('.m-seg-btn').removeClass('active');

        if (mode === 'dine_in') {
            $('#btnModeDineIn, #mBtnModeDineIn').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-none').addClass('d-flex');
            $('#cartHeaderTakeawayControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderDeliveryControl').removeClass('d-flex').addClass('d-none');
            $('#deliveryDetailsBox').addClass('d-none');
            $('#cartHeaderOrderModeBadge').text('Dine-In');
            
            let curTbl = $('#currentTableBadge').text().trim() || 'T-1';
            $('#orderTargetInput').val(curTbl);
        } else if (mode === 'takeaway') {
            $('#btnModeTakeaway, #mBtnModeTakeaway').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderTakeawayControl').removeClass('d-none').addClass('d-flex');
            $('#cartHeaderDeliveryControl').removeClass('d-flex').addClass('d-none');
            $('#deliveryDetailsBox').addClass('d-none');
            $('#cartHeaderOrderModeBadge').text('Takeaway');
            
            $('#cartTokenBadge').text(nextToken);
            $('#orderTargetInput').val(nextToken);
        } else if (mode === 'delivery') {
            $('#btnModeDelivery, #mBtnModeDelivery').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderTakeawayControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderDeliveryControl').removeClass('d-none').addClass('d-flex');
            $('#deliveryDetailsBox').removeClass('d-none');
            $('#cartHeaderOrderModeBadge').text('Delivery');
            
            onRiderSelectChange();
        }
    }

    // Delivery Box Rider Dropdown
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

    // Quick Add Rider Modal
    function openQuickRiderModal() {
        $('#quickRiderForm')[0].reset();
        $('#quickRiderModal').modal('show');
        setTimeout(() => {
            $('#qrRiderName').focus();
        }, 200);
    }

    function submitQuickRider(e) {
        e.preventDefault();
        let name = $('#qrRiderName').val().trim();
        let phone = $('#qrRiderPhone').val().trim();
        let vehicle = $('#qrVehicleNo').val().trim();
        let status = $('#qrDutyStatus').val();
        let cnic = $('#qrCnic').val().trim();

        if (!name || !phone) {
            Swal.fire('Required', 'Please enter rider name and phone number.', 'warning');
            return;
        }

        let $btn = $('#btnSaveQuickRider');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('restaurant.riders.store') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: name,
                phone: phone,
                vehicle_no: vehicle,
                duty_status: status,
                cnic: cnic,
                is_active: 1
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Rider');
                let rider = res.rider || { id: res.id, name: name, phone: phone, vehicle_no: vehicle };
                
                // Add to posRiderSelect and select it
                let optHtml = `<option value="${rider.id}" data-name="${rider.name}" data-phone="${rider.phone}" selected>${rider.name} (${rider.vehicle_no || 'Bike'}) &mdash; Available</option>`;
                $('#posRiderSelect').append(optHtml).val(rider.id);
                onRiderSelectChange();

                // Switch to delivery mode automatically
                setOrderMode('delivery');

                $('#quickRiderModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Rider Added!',
                    text: `${rider.name} added & assigned to delivery.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Rider');
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to add rider.';
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    // Floor Plan Table Modal
    function openTablePickerModal() {
        $('#tablePickerModal').modal('show');
        $('#modalTableSearchInput').val('');
        $('.modal-table-col').show();
        setTimeout(() => {
            $('#modalTableSearchInput').focus();
        }, 150);
    }

    $('#modalTableSearchInput').on('input', function() {
        let q = $(this).val().toLowerCase().trim();
        if (!q) {
            $('.modal-table-col').show();
            return;
        }
        $('.modal-table-col').each(function() {
            let tno = String($(this).data('tableno') || '');
            let fl = String($(this).data('floor') || '').toLowerCase();
            let txt = $(this).text().toLowerCase();
            if (tno.includes(q) || fl.includes(q) || txt.includes(q)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#modalTableSearchInput').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let $first = $('.modal-table-col:visible .card').first();
            if ($first.length > 0) {
                $first.trigger('click');
            } else {
                let customVal = $(this).val().trim();
                if (customVal) {
                    selectFloorTable(customVal, 'Custom');
                }
            }
        } else if (e.key === 'Escape') {
            $('#tablePickerModal').modal('hide');
            setTimeout(() => { $('#menuSearchInput').focus(); }, 100);
        }
    });

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

    function onModalTableClick(tableNo, floor, isOcc, orderId, invoiceNo, totalBill) {
        if (isOcc && orderId) {
            Swal.fire({
                title: `Table ${tableNo} is Occupied`,
                html: `This table has an active order <b>#${invoiceNo}</b> (Rs. ${totalBill.toLocaleString()}).<br><br>What would you like to do?`,
                icon: 'info',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#2563eb',
                denyButtonColor: '#16a34a',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-folder-open me-1"></i> Open Running Order',
                denyButtonText: '<i class="fas fa-plus me-1"></i> Start New Order',
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

    function selectFloorTable(tableNo, floor) {
        $('#orderTargetInput').val(tableNo);
        $('#currentTableBadge').text(tableNo);
        $('#currentTableFloorBadge').text(floor || 'Main Hall');
        $('#tablePickerModal').modal('hide');
        setTimeout(() => { $('#menuSearchInput').focus(); }, 100);
    }

    function selectManualCustomTable() {
        let customName = $('#manualCustomTableInput').val().trim();
        if (!customName) {
            Swal.fire('Please enter table name', '', 'warning');
            return;
        }
        selectFloorTable(customName, 'Custom');
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
        highlightFirstVisible();
    }

    // Highlight First Visible Card for Arrow/Enter Navigation
    function highlightFirstVisible() {
        $('.product-food-card').removeClass('highlighted-card');
        let $visible = $('.product-food-card:visible');
        if ($visible.length > 0) {
            if (highlightedIndex >= $visible.length) highlightedIndex = 0;
            $visible.eq(highlightedIndex).addClass('highlighted-card');
        }
    }

    // Parse quantity prefix/suffix (e.g. 2*burger, burger*2, 3 zinger)
    function parseSearchInput(raw) {
        raw = (raw || '').trim();
        let qty = 1;
        let query = raw;

        let m1 = raw.match(/^(\d+)[\*xX\s]+(.*)$/);
        if (m1 && m1[2].trim().length > 0) {
            qty = parseInt(m1[1]) || 1;
            query = m1[2].trim();
        } else {
            let m2 = raw.match(/^(.*)[\*xX](\d+)$/);
            if (m2 && m2[1].trim().length > 0) {
                query = m2[1].trim();
                qty = parseInt(m2[2]) || 1;
            }
        }
        return { query: query.toLowerCase(), qty: Math.max(1, qty) };
    }

    // Search Filter with instant Enter-Enter support
    $('#menuSearchInput').on('input', function() {
        let parsed = parseSearchInput($(this).val());
        let q = parsed.query;
        highlightedIndex = 0;
        if (!q) {
            $('.product-food-card').show();
            highlightFirstVisible();
            return;
        }
        $('.product-food-card').each(function() {
            let name = String($(this).data('name') || '');
            let sku = String($(this).data('sku') || '');
            if (name.includes(q) || sku.includes(q)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        highlightFirstVisible();
    });

    // Enter & Arrow Key Navigation on Search Input
    $('#menuSearchInput').on('keydown', function(e) {
        let $visible = $('.product-food-card:visible');

        if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
            e.preventDefault();
            if ($visible.length > 0) {
                highlightedIndex = (highlightedIndex + 1) % $visible.length;
                $('.product-food-card').removeClass('highlighted-card');
                let $target = $visible.eq(highlightedIndex).addClass('highlighted-card');
                $target[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
            e.preventDefault();
            if ($visible.length > 0) {
                highlightedIndex = (highlightedIndex - 1 + $visible.length) % $visible.length;
                $('.product-food-card').removeClass('highlighted-card');
                let $target = $visible.eq(highlightedIndex).addClass('highlighted-card');
                $target[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        } else if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            let raw = $(this).val().trim();

            if (raw.length > 0 && $visible.length > 0) {
                let parsed = parseSearchInput(raw);
                let $target = $visible.eq(highlightedIndex);
                let id = $target.data('id');
                if (id) {
                    let item = menuItems[id];
                    if (item && item.has_variants && item.variants && item.variants.length > 0) {
                        openVariantsModal(item, parsed.qty);
                    } else if (item) {
                        addToCart(item, parsed.qty);
                        $('#menuSearchInput').val('');
                        $('.product-food-card').show();
                        highlightedIndex = 0;
                        highlightFirstVisible();
                    }
                }
            } else if (raw.length === 0) {
                // Empty search + Enter -> Checkout & Pay!
                if (cart.length > 0) {
                    openPaymentModal();
                } else {
                    Swal.fire({
                        title: 'Order is Empty',
                        text: 'Type a dish name / code and press Enter.',
                        icon: 'info',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            }
        } else if (e.key === '+' || e.key === '=') {
            if ($(this).val().trim() === '' && cart.length > 0) {
                e.preventDefault();
                cart[cart.length - 1].qty += 1;
                renderCart();
            }
        } else if (e.key === '-') {
            if ($(this).val().trim() === '' && cart.length > 0) {
                e.preventDefault();
                cart[cart.length - 1].qty -= 1;
                if (cart[cart.length - 1].qty <= 0) cart.pop();
                renderCart();
            }
        }
    });

    // Fast Direct Exact Cash Checkout (Peak Rush Mode)
    function fastExactCashPay() {
        if (cart.length === 0) {
            Swal.fire({
                title: 'Order is Empty',
                text: 'Please add dishes to order first.',
                icon: 'warning',
                timer: 1200,
                showConfirmButton: false
            });
            return;
        }
        let total = getTotalBill();
        $('#cashReceivedInput').val(total);
        calculateChange();
        submitOrder('post');
    }

    // Global Keyboard Shortcuts
    $(document).on('keydown', function(e) {
        // If Swal alert is open, any Enter, Space or Escape closes it instantly so cashier isn't blocked
        if (typeof Swal !== 'undefined' && Swal.isVisible()) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'Escape') {
                e.preventDefault();
                Swal.close();
                $('#menuSearchInput').focus();
                return;
            }
        }

        // Allow modal handlers to deal with keys if modals are open
        if ($('#settlementModal').hasClass('show') || $('#variantsModal').hasClass('show') || $('#quickRiderModal').hasClass('show') || $('#runningOrdersModal').hasClass('show') || $('#tablePickerModal').hasClass('show')) {
            if (e.key === 'Escape') {
                $('.modal').modal('hide');
                setTimeout(() => { $('#menuSearchInput').focus(); }, 100);
            }
            return;
        }

        // F1 or Alt+1: Dine-In mode
        if (e.key === 'F1' || (e.altKey && e.key === '1')) {
            e.preventDefault();
            setOrderMode('dine_in');
            $('#menuSearchInput').focus();
        }
        // F3 or Alt+2: Takeaway mode
        else if (e.key === 'F3' || (e.altKey && e.key === '2')) {
            e.preventDefault();
            setOrderMode('takeaway');
            $('#menuSearchInput').focus();
        }
        // F6 or Alt+3: Delivery mode
        else if (e.key === 'F6' || (e.altKey && e.key === '3')) {
            e.preventDefault();
            setOrderMode('delivery');
            $('#menuSearchInput').focus();
        }
        // F5 or Alt+T: Table Picker (Dine-In)
        else if (e.key === 'F5' || (e.altKey && (e.key === 't' || e.key === 'T'))) {
            e.preventDefault();
            openTablePickerModal();
        }
        // F8 or Shift+Enter: Kitchen Order (KOT)
        else if (e.key === 'F8' || (e.shiftKey && e.key === 'Enter')) {
            e.preventDefault();
            submitOrder('kot');
        }
        // F9 or Alt+Enter: Instant Exact Cash Pay & Print (Fastest Checkout)
        else if (e.key === 'F9' || (e.altKey && e.key === 'Enter')) {
            e.preventDefault();
            fastExactCashPay();
        }
        // F4 or Ctrl+Enter: Pay & Settle Modal
        else if (e.key === 'F4' || (e.ctrlKey && e.key === 'Enter')) {
            e.preventDefault();
            openPaymentModal();
        }
        // F2: Jump to search input
        else if (e.key === 'F2') {
            e.preventDefault();
            $('#menuSearchInput').focus().select();
        }
        // Escape: Clear Search or Prompt Clear Cart
        else if (e.key === 'Escape') {
            if ($('#menuSearchInput').val().length > 0) {
                $('#menuSearchInput').val('');
                $('.product-food-card').show();
                highlightFirstVisible();
            } else if (cart.length > 0) {
                resetCart();
            }
            $('#menuSearchInput').focus();
        }
        // Plus (+) / Minus (-) anywhere if not in an input
        else if ((e.key === '+' || e.key === '=') && !$(e.target).is('input, textarea, select')) {
            e.preventDefault();
            if (cart.length > 0) {
                cart[cart.length - 1].qty += 1;
                renderCart();
            }
        }
        else if ((e.key === '-' || e.key === '_') && !$(e.target).is('input, textarea, select')) {
            e.preventDefault();
            if (cart.length > 0) {
                cart[cart.length - 1].qty -= 1;
                if (cart[cart.length - 1].qty <= 0) cart.pop();
                renderCart();
            }
        }
    });

    // Stepper buttons directly on the product food card
    function changeCardQty(id, delta) {
        let item = menuItems[id];
        if (!item) return;

        if (item.has_variants && item.variants && item.variants.length > 0) {
            openVariantsModal(item);
            return;
        }

        let cartKey = String(item.id);
        let existingIdx = cart.findIndex(x => x.cartKey === cartKey);

        if (delta > 0) {
            if (existingIdx > -1) {
                cart[existingIdx].qty += 1;
            } else {
                cart.push({
                    cartKey: cartKey,
                    id: item.id,
                    name: item.name,
                    price: parseFloat(item.price) || 0,
                    qty: 1,
                    notes: '',
                    image: item.image || '',
                    variant_data: ''
                });
            }
        } else if (delta < 0 && existingIdx > -1) {
            cart[existingIdx].qty -= 1;
            if (cart[existingIdx].qty <= 0) {
                cart.splice(existingIdx, 1);
            }
        }

        renderCart();
    }

    // Handle Food Card Click (Check for Variants)
    function handleFoodCardClick(id) {
        let item = menuItems[id];
        if (!item) return;

        if (item.has_variants && item.variants && item.variants.length > 0) {
            openVariantsModal(item);
        } else {
            addToCart(item);
        }
    }

    // Open Variants Selection Modal with instant Keyboard Navigation (1..9, Arrows, Enter)
    let selectedVariantRowIdx = 0;
    let currentModalQty = 1;

    function openVariantsModal(product, defaultQty = 1) {
        currentModalProduct = product;
        currentModalQty = parseInt(defaultQty) || 1;
        if (currentModalQty <= 0) currentModalQty = 1;

        $('#variantsModalLabel').text(`Select Option - ${product.name}`);
        let $tbody = $('#variantsModalList');
        $tbody.empty();
        $('#variantSearchInput').val('');
        selectedVariantRowIdx = 0;

        product.variants.forEach((v, idx) => {
            let desc = `${v.size_val !== '-' ? v.size_val : ''} ${v.color_val !== '-' ? '(' + v.color_val + ')' : ''}`.trim();
            if (!desc || desc === '') {
                desc = v.name || 'Standard Variant';
            }
            let price = parseFloat(v.price) || 0;
            let hotkey = idx < 9 ? (idx + 1) : '';

            let row = `
                <tr id="variant-row-${idx}" class="variant-item-row ${idx === 0 ? 'selected-variant-row' : ''}" data-idx="${idx}" style="cursor:pointer;">
                    <td class="ps-3 fw-bold" style="font-size: 13px;">
                        ${hotkey ? `<span class="badge bg-dark me-2" style="font-family:monospace; font-size:11px;">[${hotkey}]</span>` : ''}
                        ${desc}
                    </td>
                    <td class="text-end fw-bold text-success">Rs. ${price.toLocaleString()}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center mx-auto" style="width: 100px; gap: 4px;" onclick="event.stopPropagation();">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fw-bold modal-qty-minus" style="line-height:1.2;">&minus;</button>
                            <input type="number" class="form-control form-control-sm text-center fw-bold modal-qty-val" value="${currentModalQty}" min="1" style="width: 44px; padding: 2px 4px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fw-bold modal-qty-plus" style="line-height:1.2;">&plus;</button>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold variant-add-btn" onclick="event.stopPropagation(); addVariantToCart(${idx}, this)">
                            <i class="fas fa-plus me-1"></i> ADD <small class="text-white-50 ms-1">[Enter]</small>
                        </button>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });

        $('#variantsModal').modal('show');
        setTimeout(() => {
            $('#variantSearchInput').focus();
            highlightSelectedVariantRow();
        }, 150);
    }

    function highlightSelectedVariantRow() {
        let $rows = $('#variantsModalList tr:visible');
        $rows.removeClass('selected-variant-row');
        if ($rows.length > 0) {
            if (selectedVariantRowIdx >= $rows.length) selectedVariantRowIdx = 0;
            if (selectedVariantRowIdx < 0) selectedVariantRowIdx = $rows.length - 1;
            let $target = $rows.eq(selectedVariantRowIdx).addClass('selected-variant-row');
            $target[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }

    // Filter variants as user types in search
    $('#variantSearchInput').on('input', function() {
        let q = $(this).val().toLowerCase().trim();
        selectedVariantRowIdx = 0;
        $('#variantsModalList tr').each(function() {
            let txt = $(this).text().toLowerCase();
            if (!q || txt.includes(q)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        highlightSelectedVariantRow();
    });

    // Variant modal key navigation
    $('#variantsModal').on('keydown', function(e) {
        let $rows = $('#variantsModalList tr:visible');

        // Number keys 1-9: Instant pick variant by number
        if (!$(e.target).is('input[type="number"]') && e.key >= '1' && e.key <= '9') {
            let num = parseInt(e.key) - 1;
            if ($rows.length > num) {
                e.preventDefault();
                let $targetRow = $rows.eq(num);
                let idx = $targetRow.data('idx');
                addVariantToCart(idx, $targetRow.find('.variant-add-btn'));
                return;
            }
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if ($rows.length > 0) {
                selectedVariantRowIdx = (selectedVariantRowIdx + 1) % $rows.length;
                highlightSelectedVariantRow();
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if ($rows.length > 0) {
                selectedVariantRowIdx = (selectedVariantRowIdx - 1 + $rows.length) % $rows.length;
                highlightSelectedVariantRow();
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if ($rows.length > 0) {
                let $targetRow = $rows.eq(selectedVariantRowIdx);
                let idx = $targetRow.data('idx');
                addVariantToCart(idx, $targetRow.find('.variant-add-btn'));
            }
        } else if (e.key === 'Escape') {
            $('#variantsModal').modal('hide');
            setTimeout(() => { $('#menuSearchInput').focus(); }, 100);
        }
    });

    // Click on row selects and adds it
    $(document).on('click', '.variant-item-row', function() {
        let idx = $(this).data('idx');
        addVariantToCart(idx, $(this).find('.variant-add-btn'));
    });

    // Pressing Enter in Variant Qty automatically adds to cart
    $(document).on('keydown', '.modal-qty-val', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            let $addBtn = $(this).closest('tr').find('.variant-add-btn');
            if ($addBtn.length) {
                $addBtn.click();
            }
        }
    });

    $(document).on('click', '.modal-qty-plus', function(e) {
        e.stopPropagation();
        let $inp = $(this).siblings('.modal-qty-val');
        $inp.val((parseInt($inp.val()) || 0) + 1);
    });
    $(document).on('click', '.modal-qty-minus', function(e) {
        e.stopPropagation();
        let $inp = $(this).siblings('.modal-qty-val');
        let cur = parseInt($inp.val()) || 1;
        if (cur > 1) $inp.val(cur - 1);
    });

    // Add Variant to Cart
    function addVariantToCart(idx, btn) {
        if (!currentModalProduct || !currentModalProduct.variants || !currentModalProduct.variants[idx]) {
            return;
        }
        let v = currentModalProduct.variants[idx];
        let qty = currentModalQty || 1;
        if (btn) {
            let $row = $(btn).closest('tr');
            let q = parseInt($row.find('.modal-qty-val').val());
            if (q && q > 0) qty = q;
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
                image: currentModalProduct.image || '',
                variant_data: v.variant_data || ''
            });
        }

        renderCart();
        $('#variantsModal').modal('hide');

        $('#menuSearchInput').val('').focus();
        $('.product-food-card').show();
        highlightedIndex = 0;
        highlightFirstVisible();
    }

    // Add Standard Product to Cart
    function addToCart(item, qty = 1) {
        qty = parseInt(qty) || 1;
        if (qty <= 0) qty = 1;
        let cartKey = String(item.id);
        let existing = cart.find(x => x.cartKey === cartKey);
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({
                cartKey: cartKey,
                id: item.id,
                name: item.name,
                price: parseFloat(item.price) || 0,
                qty: qty,
                notes: '',
                image: item.image || '',
                variant_data: ''
            });
        }
        renderCart();
    }

    // Render Cart (Row matches screenshot: thumb, name, price, notes input, stepper, trash icon)
    function renderCart() {
        let $list = $('#cartItemsList');
        if (cart.length === 0) {
            $list.html(`
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted py-5" id="cartEmptyState">
                    <i class="fas fa-shopping-basket fs-2 text-black-50 mb-2"></i>
                    <p class="small mb-0 fw-bold">No dishes in order</p>
                    <small class="text-muted">Click or search dishes on the left</small>
                </div>
            `);
            $('#cartItemBadge').text('0 Items');
            $('#cartTotalDisplay').text('Rs. 0');
            $('#mobFloatCount').text('0');
            $('#mobFloatTotal').text('Rs. 0');
            $('#mBottomBadgeCart').text('0');
            $('#mBottomCartTotal').text('Rs. 0');
            $('#mobileFloatingCheckout').removeClass('show-float');

            return;
        }

        let total = 0;
        let count = 0;
        let html = '';

        cart.forEach((item, index) => {
            let lineTotal = item.qty * item.price;
            total += lineTotal;
            count += item.qty;

            let thumbHtml = item.image 
                ? `<img src="${item.image}" alt="${item.name}">` 
                : `<i class="fas fa-utensils"></i>`;

            html += `
                <div class="cart-order-row">
                    <div class="cart-row-thumb">
                        ${thumbHtml}
                    </div>
                    <div class="cart-row-info">
                        <span class="cart-row-item-title" title="${item.name}">${item.name}</span>
                        <div class="cart-stepper">
                            <button type="button" class="btn-cart-stepper" onclick="updateQty(${index}, -1)">&minus;</button>
                            <span class="cart-stepper-val">${item.qty}</span>
                            <button type="button" class="btn-cart-stepper" onclick="updateQty(${index}, 1)">&plus;</button>
                        </div>
                    </div>
                    <span class="cart-row-item-price">Rs. ${lineTotal.toLocaleString()}</span>
                    <button type="button" class="cart-row-trash-btn" onclick="removeFromCart(${index})" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
        });

        // Add delivery charges if in delivery mode
        let deliveryCharges = currentOrderMode === 'delivery' ? (parseFloat($('#posDeliveryCharges').val()) || 0) : 0;
        let grandTotal = total + deliveryCharges;

        $list.html(html);
        $('#cartItemBadge').text(count + ' Items');
        $('#cartTotalDisplay').text('Rs. ' + grandTotal.toLocaleString());
        $('#mobFloatCount').text(count);
        $('#mobFloatTotal').text('Rs. ' + grandTotal.toLocaleString());
        $('#mBottomBadgeCart').text(count);
        $('#mBottomCartTotal').text('Rs. ' + grandTotal.toLocaleString());

        // Show floating checkout on mobile if viewing menu
        if ($(window).width() < 992 && activeMobileView === 'menu') {
            $('#mobileFloatingCheckout').addClass('show-float');
        } else {
            $('#mobileFloatingCheckout').removeClass('show-float');
        }
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
        renderCart();
    }

    function getTotalBill() {
        let itemsTotal = cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
        let deliveryCharges = currentOrderMode === 'delivery' ? (parseFloat($('#posDeliveryCharges').val()) || 0) : 0;
        return itemsTotal + deliveryCharges;
    }

    // Payment Settlement Modal
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
        setTimeout(() => {
            $('#cashReceivedInput').focus().select();
        }, 150);
    }

    // Enter in Cash Received automatically submits order!
    $('#cashReceivedInput').on('keydown', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            submitOrder('post');
        }
    });

    function setCashPreset(val) {
        $('#cashReceivedInput').val(val);
        calculateChange();
        $('#cashReceivedInput').focus();
    }

    function setExactCash() {
        let total = getTotalBill();
        $('#cashReceivedInput').val(total);
        calculateChange();
        $('#cashReceivedInput').focus();
    }

    function calculateChange() {
        let total = getTotalBill();
        let received = parseFloat($('#cashReceivedInput').val()) || 0;
        let change = Math.max(0, received - total);
        $('#modalChangeDue').text('Rs. ' + change.toLocaleString());
    }

    // Direct Thermal Print
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
                            timer: 1000,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    } else {
                        let receiptUrl = res.receipt_url || res.invoice_url || `{{ url('sales') }}/${res.booking_id || res.sale_id}/recepit`;
                        printThermalDirect(receiptUrl);

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
                            timer: 1000,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    }

                    // Return focus to dish search on desktop
                    if ($(window).width() >= 992) {
                        setTimeout(() => {
                            $('#menuSearchInput').focus();
                        }, 300);
                    } else {
                        switchMobileView('menu');
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

    // Open Active Running Orders Modal
    function openRunningOrdersModal() {
        $('#runningOrdersModal').modal('show');
    }

    // Refresh Active Running Orders
    function refreshActiveOrders() {
        $.get('{{ route("restaurant.active_orders") }}', function(res) {
            if (res.success) {
                $('#badgeRunningCount, #mHeaderActiveCount, #mBottomBadgeTables').text(res.count);
                let html = '';
                if (res.orders.length === 0) {
                    html = `
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fs-1 text-success mb-2"></i>
                            <p class="mb-0 fw-bold">No active running orders.</p>
                            <small>All tables are currently clear!</small>
                        </div>
                    `;
                } else {
                    res.orders.forEach(o => {
                        let icon = o.order_type === 'dine_in' ? '🍽️ Table: ' + (o.table_no || '-') : (o.order_type === 'takeaway' ? '🥡 Token: ' + (o.token_no || '-') : '🛵 ' + (o.walkin_name || 'Delivery'));
                        html += `
                            <div class="running-order-item" onclick="loadRunningOrder(${o.id})" style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px; margin-bottom:8px; cursor:pointer;">
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

    // Load Running Order
    function loadRunningOrder(id) {
        Swal.fire({
            title: 'Loading Table Order...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.get(`{{ url('restaurant/order') }}/${id}`, function(res) {
            Swal.close();
            $('#runningOrdersModal').modal('hide');

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
                        image: it.image || '',
                        variant_data: vData
                    };
                });

                renderCart();

                // On mobile, immediately show cart
                if ($(window).width() < 992) {
                    switchMobileView('cart');
                }
            }
        }).fail(function() {
            Swal.close();
            Swal.fire('Error', 'Failed to load order.', 'error');
        });
    }

    // Initialize on page load
    $(document).ready(function() {
        // Desktop auto-focus search
        if ($(window).width() >= 992) {
            setTimeout(() => {
                $('#menuSearchInput').focus();
                highlightFirstVisible();
            }, 150);
        } else {
            switchMobileView('menu');
        }

        // Periodic poll for active tables/orders
        setInterval(refreshActiveOrders, 30000);
    });
</script>
@endsection
