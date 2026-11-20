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
       OFFCANVAS RUNNING ORDERS SIDEBAR DRAWER
       ========================================================= */
    /* =========================================================
       RUNNING ORDERS SIDEBAR (DOCKED 3-COLUMN DESKTOP + MOBILE OFFCANVAS)
       ========================================================= */
    .ro-live-dot {
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: ro-pulse 1.8s infinite;
    }
    @keyframes ro-pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Desktop (>= 992px): Docked Left Panel inside .rest-main-split */
    @media (min-width: 992px) {
        .running-orders-backdrop {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        .running-orders-drawer {
            position: relative !important;
            top: auto !important;
            bottom: auto !important;
            left: auto !important;
            right: auto !important;
            transform: none !important;
            width: 320px !important;
            min-width: 290px !important;
            max-width: 340px !important;
            height: 100% !important;
            background: #ffffff !important;
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
            z-index: 10 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            transition: width 0.24s cubic-bezier(0.16, 1, 0.3, 1), 
                        min-width 0.24s cubic-bezier(0.16, 1, 0.3, 1), 
                        max-width 0.24s cubic-bezier(0.16, 1, 0.3, 1), 
                        opacity 0.2s ease, 
                        margin 0.24s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .running-orders-drawer.ro-collapsed {
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            opacity: 0 !important;
            margin: 0 !important;
            border: none !important;
            padding: 0 !important;
            pointer-events: none !important;
            overflow: hidden !important;
        }
    }

    /* Mobile & Tablet (< 992px): Offcanvas Drawer */
    @media (max-width: 991px) {
        .running-orders-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
            z-index: 1050;
            display: none;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .running-orders-backdrop.active {
            display: block !important;
            opacity: 1 !important;
        }

        .running-orders-drawer {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            right: auto !important;
            width: 420px !important;
            max-width: 92vw !important;
            height: 100vh !important;
            background: #ffffff !important;
            box-shadow: 6px 0 28px rgba(0,0,0,0.25) !important;
            z-index: 999999 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .running-orders-drawer.active {
            transform: translateX(0) !important;
        }
    }

    /* Common Beautiful Theme for Running Orders Panel */
    .ro-drawer-header {
        padding: 9px 12px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .ro-header-btn {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: #ffffff;
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.74rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .ro-header-btn:hover {
        background: rgba(255,255,255,0.25);
        color: #ffffff;
    }

    .ro-filter-bar {
        padding: 6px 8px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 5px;
        overflow-x: auto;
        scrollbar-width: none;
        flex-shrink: 0;
    }
    .ro-filter-bar::-webkit-scrollbar {
        display: none;
    }
    .ro-filter-pill {
        padding: 4px 9px;
        border-radius: 16px;
        font-size: 0.72rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        user-select: none;
    }
    .ro-filter-pill:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #cbd5e1;
    }
    .ro-filter-pill.active[data-filter="all"] {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
        box-shadow: 0 1px 3px rgba(15,23,42,0.2);
    }
    .ro-filter-pill.active[data-filter="dine_in"] {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 1px 3px rgba(37,99,235,0.25);
    }
    .ro-filter-pill.active[data-filter="takeaway"] {
        background: #d97706;
        color: #ffffff;
        border-color: #d97706;
        box-shadow: 0 1px 3px rgba(217,119,6,0.25);
    }
    .ro-filter-pill.active[data-filter="delivery"] {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        box-shadow: 0 1px 3px rgba(5,150,105,0.25);
    }

    .ro-search-box {
        padding: 6px 8px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .ro-search-box .input-group-text {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .ro-search-box .form-control {
        border-color: #cbd5e1;
    }
    .ro-search-box .form-control:focus {
        border-color: #3b82f6;
        box-shadow: none;
    }

    .ro-drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
        background: #f1f5f9;
    }
    .ro-drawer-body::-webkit-scrollbar {
        width: 5px;
    }
    .ro-drawer-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .ro-drawer-footer {
        padding: 7px 12px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .ro-drawer-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 9px 11px;
        margin-bottom: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.15s ease-in-out;
    }
    .ro-drawer-card:hover {
        border-color: #94a3b8;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transform: translateY(-1px);
    }
    .ro-card-dine_in {
        border-left: 4px solid #3b82f6 !important;
    }
    .ro-card-takeaway {
        border-left: 4px solid #f59e0b !important;
    }
    .ro-card-delivery {
        border-left: 4px solid #10b981 !important;
    }

    .ro-card-btn {
        padding: 3px 7px;
        border-radius: 5px;
        font-size: 0.70rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        transition: all 0.12s;
        text-decoration: none;
    }
    .ro-card-btn:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .ro-card-btn-complete {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: 1px solid #059669 !important;
        box-shadow: 0 1px 3px rgba(16,185,129,0.25) !important;
    }
    .ro-card-btn-complete:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
    }
    .ro-card-btn-serve {
        background: #0284c7 !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
    }
    .ro-card-btn-serve:hover {
        background: #0369a1 !important;
        color: #ffffff !important;
    }
    .ro-card-btn-dispatch {
        background: #8b5cf6 !important;
        color: #ffffff !important;
        border-color: #8b5cf6 !important;
    }
    .ro-card-btn-dispatch:hover {
        background: #7c3aed !important;
        color: #ffffff !important;
    }
    .ro-card-btn-cancel {
        color: #ef4444 !important;
        border-color: #fecaca !important;
        background: #fff5f5 !important;
    }
    .ro-card-btn-cancel:hover {
        background: #ef4444 !important;
        color: #ffffff !important;
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
        flex: 1 1 0;
        min-height: 45px;
        overflow-y: auto;
        padding: 6px 8px;
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

    .cart-stepper-input {
        width: 44px;
        height: 24px;
        padding: 0 2px;
        border: 1.5px solid #cbd5e1;
        border-radius: 4px;
        background: #ffffff;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.88rem;
        text-align: center;
        outline: none;
        transition: all 0.15s ease;
        -moz-appearance: textfield;
    }
    .cart-stepper-input::-webkit-outer-spin-button,
    .cart-stepper-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .cart-stepper-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
        background: #eff6ff;
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
        padding: 6px 10px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        max-height: calc(100% - 85px);
        overflow: hidden;
    }

    .cart-footer-config {
        overflow-y: auto;
        max-height: 220px;
        padding-right: 2px;
        margin-bottom: 4px;
    }
    .cart-footer-config::-webkit-scrollbar {
        width: 3px;
    }
    .cart-footer-config::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .cart-footer-pinned {
        flex-shrink: 0;
        margin-top: auto;
    }

    .cart-total-bill-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 6px;
    }

    .cart-total-lbl {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .cart-total-val {
        font-size: 1.35rem;
        font-weight: 900;
        color: #2563eb;
    }

    .cart-action-btn-row {
        display: flex;
        gap: 6px;
    }

    .btn-kot-red {
        flex: 1;
        background: #ef4444;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.84rem;
        border: none;
        padding: 8px 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-kot-red:hover {
        background: #dc2626;
        color: #ffffff;
    }

    .btn-bill-blue {
        flex: 0.9;
        background: #0284c7;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.82rem;
        border: none;
        padding: 8px 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-bill-blue:hover {
        background: #0369a1;
        color: #ffffff;
    }

    .btn-pay-green {
        flex: 1.2;
        background: #10b981;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.84rem;
        border: none;
        padding: 8px 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-pay-green:hover {
        background: #059669;
        color: #ffffff;
    }

    .btn-delivery-dispatch {
        flex: 1.4;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.82rem;
        border: none;
        padding: 8px 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        transition: all 0.15s ease;
    }
    .btn-delivery-dispatch:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        transform: translateY(-1px);
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
            <button type="button" class="btn btn-xs btn-outline-danger fw-bold py-1 px-2 d-flex align-items-center gap-1" onclick="toggleRunningOrdersSidebar()" style="font-size: 0.72rem; border-radius: 6px;">
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
        <!-- Left: Brand Name + RMS POS badge + Quick Rider Button + Running Orders Button -->
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

            <!-- Running Orders Toggle Button (Left Side) -->
            <button type="button" class="btn btn-sm btn-outline-danger fw-bold position-relative d-flex align-items-center gap-2 py-1 px-3 shadow-sm" onclick="toggleRunningOrdersSidebar()" style="font-size: 0.82rem; border-radius: 8px; border-width: 1.5px; background: #fff5f5;" title="Toggle Active Running Orders Sidebar">
                <i class="fas fa-clock text-danger"></i>
                <span class="fw-bold text-danger">Running Orders</span>
                <span class="badge bg-danger rounded-pill px-2 py-1" id="badgeRunningCount" style="font-size: 0.75rem;">{{ count($runningOrders) }}</span>
            </button>

            @php
                $initialTable = isset($tables) && $tables->isNotEmpty() ? $tables->first() : null;
                $initialTableNo = $initialTable ? $initialTable->table_no : 'T-1';
                $initialTableFloor = $initialTable ? ($initialTable->floor_area ?: 'Main Hall') : 'Main Hall';
            @endphp
            <!-- Hidden Master Input for Order Submission -->
            <input type="hidden" id="orderTargetInput" value="{{ $initialTableNo }}">
        </div>

        <!-- Right Side: Order Mode Switcher (Dine-In, Takeaway, Delivery) -->
        <div class="d-flex align-items-center ms-auto">
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
        </div>
    </div>

    <!-- MAIN BODY SPLIT -->
    <div class="rest-main-split">

        <!-- OFFCANVAS BACKDROP FOR MOBILE (< 992px) -->
        <div class="running-orders-backdrop" id="runningOrdersBackdrop" onclick="closeRunningOrdersDrawer()"></div>

        <!-- 1. LEFT COLUMN: RUNNING ORDERS SIDEBAR (DOCKED PERMANENTLY ON DESKTOP) -->
        <div class="running-orders-drawer" id="runningOrdersDrawer">
            <!-- Header -->
            <div class="ro-drawer-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="ro-live-dot" title="Live Running Orders"></span>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-white fs-6">Running Orders</span>
                            <span class="badge bg-danger rounded-pill px-2 py-0.5" id="drawerActiveSubtitle" style="font-size: 0.68rem;">{{ count($runningOrders) }} Active</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-xs ro-header-btn" onclick="refreshActiveOrders(this)" title="Refresh Active Orders">
                        <i class="fas fa-sync-alt" id="roSyncIcon"></i>
                    </button>
                    <button type="button" class="btn btn-xs ro-header-btn" onclick="toggleRunningOrdersSidebar()" title="Toggle Sidebar">
                        <i class="fas fa-chevron-left d-none d-lg-inline"></i>
                        <span class="d-inline d-lg-none fs-5" style="line-height: 1;">&times;</span>
                    </button>
                </div>
            </div>

            <!-- Quick Filter Bar -->
            <div class="ro-filter-bar">
                <button type="button" class="ro-filter-pill active" data-filter="all" onclick="filterDrawerOrders('all')">All</button>
                <button type="button" class="ro-filter-pill" data-filter="dine_in" onclick="filterDrawerOrders('dine_in')">🍽️ Tables</button>
                <button type="button" class="ro-filter-pill" data-filter="takeaway" onclick="filterDrawerOrders('takeaway')">🥡 Tokens</button>
                <button type="button" class="ro-filter-pill" data-filter="delivery" onclick="filterDrawerOrders('delivery')">🛵 Deliveries</button>
            </div>

            <!-- Instant Search Input Box -->
            <div class="ro-search-box">
                <div class="input-group input-group-sm">
                    <span class="input-group-text py-1 px-2 text-muted" style="font-size: 0.72rem;">
                        <i class="fas fa-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control form-control-sm border-start-0 ps-1 fw-semibold" id="roSearchInput" placeholder="Search Invoice, Customer, Phone, Table..." style="font-size: 0.74rem; height: 30px;" oninput="onDrawerSearchInput(this.value)" autocomplete="off">
                    <button type="button" class="btn btn-sm btn-light border border-start-0 text-muted px-2 d-none" id="roSearchClearBtn" onclick="clearDrawerSearch()" style="font-size: 0.72rem; height: 30px;" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Drawer Body / Order Cards List -->
            <div class="ro-drawer-body" id="runningOrdersListContainer">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                    <p class="mt-2 small mb-0">Loading active orders...</p>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="ro-drawer-footer">
                <div class="d-flex align-items-center gap-1">
                    <span class="text-muted" style="font-size: 0.72rem;">Total:</span>
                    <span class="fw-bold text-dark fs-6" id="drawerFooterTotal">Rs. {{ number_format($runningOrders->sum('total_bill_amount')) }}</span>
                </div>
                <a href="{{ route('restaurant.running_orders') }}" class="btn btn-xs btn-outline-dark fw-bold px-2 py-1 text-decoration-none" style="font-size: 0.72rem; border-radius: 6px;">
                    <i class="fas fa-external-link-alt me-1"></i> Full Page &raquo;
                </a>
            </div>
        </div>

        <!-- 2. CENTER COLUMN: MENU & PRODUCTS -->
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
                <div class="cart-footer-config">
                    <!-- Delivery Specific Fields (Shown only in Delivery mode - SINGLE COMPACT CARD) -->
                    <div id="deliveryDetailsBox" class="p-1 px-2 mb-1 rounded border bg-light d-none" style="font-size: 0.75rem;">
                        <!-- Row 1: Rider + Delivery Charges -->
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-7">
                                <select class="form-select form-select-sm" id="posRiderSelect" style="font-size: 0.75rem; height: 28px; padding: 2px 22px 2px 8px; border: 1.5px solid #94a3b8 !important; border-radius: 4px; background-color: #ffffff; color: #1e293b; font-weight: 500; cursor: pointer;" onchange="onRiderSelectChange()">
                                    <option value="">Choose Rider (Optional)</option>
                                    @foreach($riders as $rd)
                                        <option value="{{ $rd->id }}" data-name="{{ $rd->name }}" data-phone="{{ $rd->phone }}">
                                            {{ $rd->name }} ({{ ucfirst(str_replace('_', ' ', $rd->duty_status)) }})
                                        </option>
                                    @endforeach
                                    <option value="__add_new__" style="color: #2563eb; font-weight: bold;">+ Add New Rider...</option>
                                </select>
                            </div>
                            <div class="col-5">
                                <div class="input-group input-group-sm" style="height: 28px;">
                                    <span class="input-group-text px-1 bg-light text-muted" style="font-size: 0.7rem; padding: 2px 6px; border: 1.5px solid #94a3b8; border-right: none;">Del Rs</span>
                                    <input type="number" class="form-control form-control-sm px-1 text-center fw-bold text-success" id="posDeliveryCharges" value="0" placeholder="0" style="font-size: 0.76rem; height: 28px; padding: 2px 4px; border: 1.5px solid #94a3b8; border-left: 1px solid #cbd5e1;" oninput="renderCart()">
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Customer Name + Phone Number -->
                        <input type="hidden" id="posDeliveryCustomerId" value="">
                        <div class="row g-1 mb-1">
                            <div class="col-6">
                                <input type="text" class="form-control form-control-sm" id="posDeliveryCustomerName" placeholder="Customer Name (optional)" style="font-size: 0.75rem; height: 27px; padding: 2px 6px;" oninput="$('#customerWalkinName').val(this.value)">
                            </div>
                            <div class="col-6">
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm" id="posCustomerPhone" placeholder="Phone (03xx...) *" style="font-size: 0.75rem; height: 27px; padding: 2px 20px 2px 6px;" autocomplete="off">
                                    <span id="posPhoneLookupSpinner" class="position-absolute end-0 top-50 translate-middle-y me-1 d-none" style="font-size: 0.68rem; color: #2563eb;" title="Searching customer...">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Found Banner (Auto-Fill Status) -->
                        <div id="deliveryCustomerFoundNotice" class="d-none alert alert-success py-1 px-2 mb-1 rounded d-flex align-items-center justify-content-between" style="font-size: 0.7rem; border-left: 3px solid #16a34a; background-color: #ecfdf5;">
                            <span class="text-truncate text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Auto-filled: <span id="deliveryFoundBadgeName"></span></span>
                            <span class="badge bg-success" style="font-size: 0.62rem;">Found</span>
                        </div>

                        <!-- Row 3: Delivery Address -->
                        <div class="mb-1">
                            <input type="text" class="form-control form-control-sm" id="posDeliveryAddress" placeholder="Delivery Address (optional)..." style="font-size: 0.75rem; height: 27px; padding: 2px 6px;">
                        </div>

                        <!-- Row 4: Optional Delivery Note -->
                        <div>
                            <input type="text" class="form-control form-control-sm" id="posDeliveryRemarks" placeholder="Note (e.g. deliver hot, call on arrival)" style="font-size: 0.74rem; height: 25px; padding: 2px 6px;" oninput="$('#orderRemarksInput').val(this.value)">
                        </div>
                    </div>

                    <!-- Customer Selection (Shown in Dine-In & Takeaway modes) -->
                    <div class="pos-customer-box mb-1 p-1 rounded border bg-white shadow-sm" id="posCustomerBox" style="font-size: 0.76rem;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <button type="button" class="btn btn-sm btn-primary py-0 fw-bold" id="btnCustomerWalkin" onclick="setCustomerMode('walkin')" style="font-size: 0.7rem; height: 24px;">
                                    <i class="fas fa-walking me-1"></i> Walk-in
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 fw-bold" id="btnCustomerRegistered" onclick="setCustomerMode('registered')" style="font-size: 0.7rem; height: 24px;">
                                    <i class="fas fa-user-check me-1"></i> Registered / Credit
                                </button>
                            </div>
                        </div>

                        <!-- Walk-in Input -->
                        <div id="walkinCustomerDiv">
                            <input type="text" class="form-control form-control-sm" id="customerWalkinName" value="Walk-in Customer" placeholder="Customer Name (Optional)" style="font-size: 0.75rem; height: 26px; padding: 2px 6px;">
                        </div>

                        <!-- Registered Customer Dropdown & Quick Add -->
                        <div id="registeredCustomerDiv" style="display: none;">
                            <div class="d-flex gap-1 align-items-center">
                                <select class="form-select form-select-sm" id="posCustomerSelect" style="font-size: 0.75rem; width: 100%; height: 28px;" onchange="onCustomerSelectChange()">
                                    <option value="">-- Choose Customer --</option>
                                    @foreach($customers as $c)
                                        @php
                                            $cBal = (float)($c->previous_balance ?? 0);
                                        @endphp
                                        <option value="{{ $c->id }}" data-name="{{ $c->customer_name }}" data-phone="{{ $c->mobile }}" data-address="{{ $c->address }}" data-balance="{{ $cBal }}">
                                            {{ $c->customer_name }} {{ $c->mobile ? '('.$c->mobile.')' : '' }} &mdash; Bal: Rs {{ number_format(abs($cBal), 0) }} {{ $cBal >= 0 ? 'Dr' : 'Cr' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-success px-2 py-0" title="Add New Customer" onclick="openQuickCustomerModal()" style="height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <!-- Customer Info / Balance Badge -->
                            <div id="selectedCustomerBadge" class="mt-1 p-1 px-2 rounded bg-light border d-flex justify-content-between align-items-center" style="display: none; font-size: 0.7rem;">
                                <span class="text-truncate fw-bold text-dark" id="badgeCustomerName">-</span>
                                <span class="badge bg-primary" id="badgeCustomerBalance">Bal: Rs 0</span>
                            </div>
                        </div>

                        <input type="text" class="form-control form-control-sm mt-1" id="orderRemarksInput" placeholder="Special Note (e.g. Less spicy, Table request)" style="font-size: 0.74rem; height: 25px; padding: 2px 6px;">
                    </div>

                    <!-- Subtotal & Discount Section -->
                    <div class="cart-summary-section mb-1 p-1 px-2 bg-light rounded border" style="font-size: 0.76rem;">
                        <!-- Subtotal Row -->
                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted">
                            <span>Subtotal:</span>
                            <span class="fw-bold text-dark" id="cartSubtotalDisplay">Rs. 0</span>
                        </div>

                        <!-- Discount Controls Row -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-bold text-danger"><i class="fas fa-tag me-1"></i>Discount:</span>
                                <div class="btn-group btn-group-sm" role="group" style="scale: 0.8; transform-origin: left center;">
                                    <button type="button" class="btn btn-outline-danger py-0 px-2 fw-bold active" id="btnDiscTypeFlat" onclick="setDiscountType('flat')">Rs</button>
                                    <button type="button" class="btn btn-outline-danger py-0 px-2 fw-bold" id="btnDiscTypePercent" onclick="setDiscountType('percent')">%</button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <input type="number" step="any" min="0" id="orderDiscountInput" class="form-control form-control-sm text-end fw-bold text-danger" placeholder="0" value="0" oninput="onDiscountInputChanged()" style="width: 65px; height: 24px; padding: 1px 4px; font-size: 0.8rem; border: 1.5px solid #fca5a5; border-radius: 5px;">
                                <span class="fw-bold text-danger small" id="cartDiscountAmountDisplay" style="min-width: 55px; text-align: right; font-size: 0.75rem;">- Rs. 0</span>
                            </div>
                        </div>

                        <!-- Quick Discount Buttons -->
                        <div class="d-flex gap-1 justify-content-end align-items-center pt-1 border-top" style="border-color: #e2e8f0 !important;">
                            <span class="text-muted" style="font-size: 9px;">Quick:</span>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 9px; border-radius: 3px;" onclick="applyQuickPercent(5)">5%</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 9px; border-radius: 3px;" onclick="applyQuickPercent(10)">10%</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 9px; border-radius: 3px;" onclick="applyQuickPercent(15)">15%</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size: 9px; border-radius: 3px;" onclick="applyQuickPercent(20)">20%</button>
                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" style="font-size: 9px; border-radius: 3px;" onclick="clearDiscount()" title="Clear Discount"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>

                <!-- PINNED BOTTOM: TOTAL BILL & ACTIONS (ALWAYS VISIBLE & NEVER HIDDEN) -->
                <div class="cart-footer-pinned">
                    <!-- Total Bill Row -->
                    <div class="cart-total-bill-row">
                        <span class="cart-total-lbl">Total Bill:</span>
                        <span class="cart-total-val" id="cartTotalDisplay">Rs. 0</span>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="cart-action-btn-row">
                        <!-- Standard Dine-In / Takeaway Buttons -->
                        <div id="btnGroupStandard" class="d-flex gap-1 w-100">
                            <button type="button" class="btn-kot-red" id="btnSendKOT" onclick="submitOrder('kot')" title="Book Order & Print Kitchen KOT - Shortcut: F8">
                                <i class="fas fa-utensils"></i> KOT <kbd class="mode-kbd" style="background:rgba(0,0,0,0.3); font-size:9px;">F8</kbd>
                            </button>
                            <button type="button" class="btn-bill-blue" id="btnPrintGuestBill" onclick="submitOrder('print_bill')" title="Print Guest Bill (Provisional / Without Payment)">
                                <i class="fas fa-file-invoice"></i> Bill Slip
                            </button>
                            <button type="button" class="btn-pay-green" id="btnPayClose" onclick="openPaymentModal()" title="Direct Payment & Complete - Shortcut: Enter or F4">
                                <i class="fas fa-cash-register"></i> Pay &amp; Settle <kbd class="mode-kbd" style="background:rgba(0,0,0,0.3); font-size:9px;">Enter</kbd>
                            </button>
                        </div>

                        <!-- Delivery Specific Buttons: Direct Dispatch & Print COD Bill -->
                        <div id="btnGroupDelivery" class="d-none gap-1 w-100">
                            <button type="button" class="btn-delivery-dispatch" id="btnDeliveryDispatch" onclick="submitOrder('delivery_dispatch')" title="Dispatch Order & Print Bill for Rider (Cash on Delivery - No cash required upfront)">
                                <i class="fas fa-motorcycle"></i> <span>Dispatch &amp; Print Bill (COD)</span>
                            </button>
                            <button type="button" class="btn-kot-red py-1 px-2" id="btnSendKOTDelivery" onclick="submitOrder('kot')" title="Kitchen KOT Only" style="flex: 0 0 65px; font-size: 0.74rem;">
                                <i class="fas fa-utensils"></i> KOT
                            </button>
                            <button type="button" class="btn-pay-green py-1 px-2" id="btnPayDelivery" onclick="openPaymentModal()" title="Paid in Advance (Card / Online / Cash)" style="flex: 0 0 65px; font-size: 0.74rem;">
                                <i class="fas fa-check"></i> Paid
                            </button>
                        </div>
                    </div>
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
    <button type="button" class="m-bar-tab" id="mTabTables" onclick="toggleRunningOrdersSidebar()">
        <div class="position-relative d-inline-block">
            <i class="fas fa-chair"></i>
            <span class="m-bar-badge" id="mBottomBadgeTables">{{ count($runningOrders) }}</span>
        </div>
        <span>Running Orders</span>
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


<!-- =========================================================
     ORDER DETAILS MODAL (FOR POS SCREEN)
     ========================================================= -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-receipt fs-5 text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0" id="odModalTitle">Order Details</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="$('#orderDetailsModal').modal('hide')" aria-label="Close"></button>
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
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" onclick="$('#orderDetailsModal').modal('hide')">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     ASSIGN RIDER MODAL (FOR POS SCREEN - DIRECT RIDERS LIST)
     ========================================================= -->
<div class="modal fade" id="assignRiderModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="$('#assignRiderModal').modal('hide')" aria-label="Close"></button>
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
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" onclick="$('#assignRiderModal').modal('hide')">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     UNIFIED COMPLETE & SETTLE MODAL (FOR POS SCREEN)
     ========================================================= -->
<div class="modal fade" id="completeSettleModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="$('#completeSettleModal').modal('hide')" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="$('#completeSettleModal').modal('hide')">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 py-2" id="btnSubmitCs">
                        <i class="fas fa-check-double me-1"></i> Confirm & Print Receipt
                    </button>
                </div>
            </form>
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
                <!-- Customer Summary Header in Settlement Modal -->
                <div class="p-2 mb-3 rounded bg-light border" id="modalCustomerSummaryBox">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Customer: </span>
                            <strong id="modalCustName">Walk-in Customer</strong>
                        </div>
                        <div id="modalCustBalWrapper" style="display: none;">
                            <span class="text-muted small">Prev Bal: </span>
                            <span class="badge bg-secondary" id="modalCustBal">Rs. 0</span>
                        </div>
                    </div>
                </div>

                <div class="bg-light p-3 rounded border mb-3">
                    <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                        <span>Order Subtotal:</span>
                        <span class="fw-bold text-dark" id="modalSubtotalAmount">Rs. 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small text-danger mb-2">
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-bold"><i class="fas fa-tag me-1"></i>Discount:</span>
                            <div class="btn-group btn-group-sm" role="group" style="scale: 0.8; transform-origin: left center;">
                                <button type="button" class="btn btn-outline-danger py-0 px-2 fw-bold active" id="modalBtnDiscTypeFlat" onclick="setDiscountType('flat')">Rs</button>
                                <button type="button" class="btn btn-outline-danger py-0 px-2 fw-bold" id="modalBtnDiscTypePercent" onclick="setDiscountType('percent')">%</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" step="any" min="0" id="modalDiscountInput" class="form-control form-control-sm text-end fw-bold text-danger" placeholder="0" value="0" oninput="syncModalDiscount()" style="width: 80px; height: 26px; padding: 2px 6px; font-size: 0.85rem; border: 1.5px solid #fca5a5; border-radius: 6px;">
                            <span class="fw-bold text-danger" id="modalDiscountDisplay">- Rs. 0</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark fs-6">Total Payable:</span>
                        <span class="fs-3 fw-bold text-primary" id="modalPayableAmount">Rs. 0</span>
                    </div>
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
                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 fw-bold" id="btnPresetCredit" onclick="setFullCredit()" style="display: none;">
                            <i class="fas fa-hand-holding-usd me-1"></i> Full Credit (Udhar)
                        </button>
                    </div>
                </div>

                <!-- Payment Status Box (Change Due OR Credit / Udhar Due) -->
                <div class="p-2 rounded bg-light border text-center mb-2" id="modalStatusBox">
                    <div id="statusChangeRow">
                        <span class="text-muted small">Change to Return: </span>
                        <span class="fw-bold fs-5 text-success" id="modalChangeDue">Rs. 0</span>
                    </div>
                    <div id="statusCreditRow" style="display: none;">
                        <div class="text-danger fw-bold small mb-1">
                            <i class="fas fa-hand-holding-usd me-1"></i> Remaining Credit (Udhar): <span class="fs-5" id="modalCreditDue">Rs. 0</span>
                        </div>
                        <div class="text-muted small" id="modalNewBalanceText">New Customer Balance: Rs. 0</div>
                    </div>
                    <div id="statusWalkinWarningRow" style="display: none;">
                        <span class="text-danger fw-bold small"><i class="fas fa-exclamation-triangle me-1"></i> Cash cannot be less than total bill for Walk-in customer. Select a Registered Customer for Credit (Udhar).</span>
                    </div>
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

<!-- QUICK ADD CUSTOMER MODAL -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h6 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-user-plus me-1"></i> Add New Customer</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="$('#quickCustomerModal').modal('hide')"></button>
            </div>
            <form id="quickCustomerForm" onsubmit="saveQuickCustomer(event)" autocomplete="off">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" id="qc_name" class="form-control form-control-sm" required placeholder="Full Name">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" id="qc_mobile" class="form-control form-control-sm" required placeholder="03xx-xxxxxxx">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Address (Optional)</label>
                        <input type="text" name="address" id="qc_address" class="form-control form-control-sm" placeholder="Street / House #">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold mb-1">Opening Balance (Optional)</label>
                        <input type="number" name="opening_balance" id="qc_balance" class="form-control form-control-sm" value="0" placeholder="0">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal" onclick="$('#quickCustomerModal').modal('hide')">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3" id="btnSaveQuickCustomer">
                        <i class="fas fa-save me-1"></i> Save Customer
                    </button>
                </div>
            </form>
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
    let orderDiscountType = 'flat'; // 'flat' or 'percent'
    let orderDiscountValue = 0;
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
            $('#posCustomerBox').removeClass('d-none');
            $('#cartHeaderOrderModeBadge').text('Dine-In');
            $('#btnGroupStandard').removeClass('d-none').addClass('d-flex');
            $('#btnGroupDelivery').removeClass('d-flex').addClass('d-none');
            
            let curTbl = $('#currentTableBadge').text().trim() || 'T-1';
            $('#orderTargetInput').val(curTbl);
        } else if (mode === 'takeaway') {
            $('#btnModeTakeaway, #mBtnModeTakeaway').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderTakeawayControl').removeClass('d-none').addClass('d-flex');
            $('#cartHeaderDeliveryControl').removeClass('d-flex').addClass('d-none');
            $('#deliveryDetailsBox').addClass('d-none');
            $('#posCustomerBox').removeClass('d-none');
            $('#cartHeaderOrderModeBadge').text('Takeaway');
            $('#btnGroupStandard').removeClass('d-none').addClass('d-flex');
            $('#btnGroupDelivery').removeClass('d-flex').addClass('d-none');
            
            $('#cartTokenBadge').text(nextToken);
            $('#orderTargetInput').val(nextToken);
        } else if (mode === 'delivery') {
            $('#btnModeDelivery, #mBtnModeDelivery').addClass('active');
            $('#cartHeaderDineInControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderTakeawayControl').removeClass('d-flex').addClass('d-none');
            $('#cartHeaderDeliveryControl').removeClass('d-none').addClass('d-flex');
            $('#deliveryDetailsBox').removeClass('d-none');
            $('#posCustomerBox').addClass('d-none');
            $('#cartHeaderOrderModeBadge').text('Delivery');
            $('#btnGroupStandard').removeClass('d-flex').addClass('d-none');
            $('#btnGroupDelivery').removeClass('d-none').addClass('d-flex');
            
            onRiderSelectChange();
        }
    }

    // Delivery Box Rider Dropdown
    function onRiderSelectChange() {
        let opt = $('#posRiderSelect option:selected');
        let rId = opt.val() || '';
        if (rId === '__add_new__') {
            $('#posRiderSelect').val('');
            openQuickRiderModal();
            return;
        }
        let rName = opt.data('name') || '';
        if (rId && rName) {
            $('#orderTargetInput').val('Rider: ' + rName);
        } else {
            $('#orderTargetInput').val('Delivery Order');
        }
    }

    // Auto-Lookup Customer by Phone for Delivery Orders
    let phoneLookupTimer = null;
    let lastSearchedDeliveryPhone = '';

    function lookupCustomerDetailsByPhone(phone, force = false) {
        phone = (phone || '').trim();
        let cleanDigits = phone.replace(/[^0-9]/g, '');

        // Allow search for at least 3 digits (supports short numbers, test inputs, and full mobile numbers)
        if (cleanDigits.length < 3 && phone.length < 3) {
            $('#deliveryCustomerFoundNotice').addClass('d-none');
            $('#posDeliveryCustomerId').val('');
            return;
        }

        if (!force && phone === lastSearchedDeliveryPhone) {
            return;
        }

        lastSearchedDeliveryPhone = phone;
        $('#posPhoneLookupSpinner').removeClass('d-none');

        $.ajax({
            url: "{{ route('restaurant.lookup_customer') }}",
            type: "GET",
            data: { phone: phone },
            dataType: "json",
            success: function(res) {
                $('#posPhoneLookupSpinner').addClass('d-none');
                if (res && res.found) {
                    if (res.name) {
                        $('#posDeliveryCustomerName').val(res.name);
                        $('#customerWalkinName').val(res.name);
                    }
                    if (res.address) {
                        $('#posDeliveryAddress').val(res.address);
                    }
                    if (res.remarks) {
                        $('#posDeliveryRemarks').val(res.remarks);
                        $('#orderRemarksInput').val(res.remarks);
                    }
                    if (res.customer_id) {
                        $('#posDeliveryCustomerId').val(res.customer_id);
                    }

                    $('#deliveryFoundBadgeName').text(res.name || 'Saved Customer');
                    $('#deliveryCustomerFoundNotice').removeClass('d-none');

                    // Highlight visual feedback
                    $('#posDeliveryCustomerName, #posDeliveryAddress')
                        .css('transition', 'background-color 0.3s ease')
                        .css('background-color', '#dcfce7');
                    setTimeout(() => {
                        $('#posDeliveryCustomerName, #posDeliveryAddress').css('background-color', '');
                    }, 1200);
                } else {
                    $('#deliveryCustomerFoundNotice').addClass('d-none');
                    $('#posDeliveryCustomerId').val('');
                }
            },
            error: function() {
                $('#posPhoneLookupSpinner').addClass('d-none');
            }
        });
    }

    $(document).on('input', '#posCustomerPhone', function() {
        let phone = $(this).val();
        clearTimeout(phoneLookupTimer);
        let clean = phone.replace(/[^0-9]/g, '');
        // If 10 or 11 digits (full mobile) or user stops typing, trigger fast
        let delay = (clean.length >= 10) ? 200 : 350;
        phoneLookupTimer = setTimeout(() => {
            lookupCustomerDetailsByPhone(phone);
        }, delay);
    });

    $(document).on('blur change', '#posCustomerPhone', function() {
        let phone = $(this).val();
        if (phone) {
            lookupCustomerDetailsByPhone(phone, true);
        }
    });

    $(document).on('paste', '#posCustomerPhone', function() {
        setTimeout(() => {
            let phone = $('#posCustomerPhone').val();
            if (phone) {
                lookupCustomerDetailsByPhone(phone, true);
            }
        }, 100);
    });

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
            highlightSelectedVariantRow();
            let $firstQty = $('#variantsModalList tr.selected-variant-row .modal-qty-val');
            if (!$firstQty.length) {
                $firstQty = $('#variantsModalList .modal-qty-val').first();
            }
            if ($firstQty.length) {
                $firstQty.focus().select();
            }
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
            let $qty = $target.find('.modal-qty-val');
            if ($qty.length) {
                $qty.focus().select();
            }
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
        // If user is inside the quantity input, do not duplicate Enter or Arrow keys
        if ($(e.target).is('.modal-qty-val')) {
            return;
        }

        let $rows = $('#variantsModalList tr:visible');

        // Number keys 1-9: Instant pick variant by number
        if (!$(e.target).is('input') && e.key >= '1' && e.key <= '9') {
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

    // Click on row selects and adds it (only if not clicking on buttons or inputs)
    $(document).on('click', '.variant-item-row', function(e) {
        if ($(e.target).closest('.modal-qty-minus, .modal-qty-plus, .modal-qty-val, .variant-add-btn').length) {
            return;
        }
        let idx = $(this).data('idx');
        addVariantToCart(idx, $(this).find('.variant-add-btn'));
    });

    // Pressing Enter in Variant Qty automatically adds to cart ONCE
    $(document).on('keydown', '.modal-qty-val', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            e.stopPropagation();
            let $row = $(this).closest('tr');
            let idx = $row.data('idx');
            addVariantToCart(idx, $row.find('.variant-add-btn'));
        }
    });

    $(document).on('focus', '.modal-qty-val', function() {
        this.select();
    });

    $('#variantsModal').on('shown.bs.modal', function () {
        let $firstQty = $('#variantsModalList tr.selected-variant-row .modal-qty-val');
        if (!$firstQty.length) {
            $firstQty = $('#variantsModalList .modal-qty-val').first();
        }
        if ($firstQty.length) {
            $firstQty.focus().select();
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

    // Add Variant to Cart with single-execution lock
    let isAddingVariant = false;
    function addVariantToCart(idx, btn) {
        if (isAddingVariant) return;
        isAddingVariant = true;
        setTimeout(() => { isAddingVariant = false; }, 300);

        if (!currentModalProduct || !currentModalProduct.variants || !currentModalProduct.variants[idx]) {
            return;
        }
        let v = currentModalProduct.variants[idx];
        let qty = currentModalQty || 1;
        if (btn) {
            let $row = $(btn).closest('tr');
            let q = parseInt($row.find('.modal-qty-val').val());
            if (q && q > 0) qty = q;
        } else {
            let $row = $(`#variant-row-${idx}`);
            if ($row.length) {
                let q = parseInt($row.find('.modal-qty-val').val());
                if (q && q > 0) qty = q;
            }
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
                            <input type="number" class="cart-stepper-input cart-qty-val" id="cart-qty-${index}" data-index="${index}" value="${item.qty}" min="1" onchange="setDirectQty(${index}, this.value)" onfocus="this.select()" onkeydown="handleQtyKeydown(event, ${index})">
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
        let subtotal = total + deliveryCharges;
        let discount = getDiscountAmount();
        let grandTotal = Math.max(0, subtotal - discount);

        $list.html(html);
        $('#cartItemBadge').text(count + ' Items');
        $('#cartSubtotalDisplay').text('Rs. ' + subtotal.toLocaleString());
        $('#cartDiscountAmountDisplay').text('- Rs. ' + discount.toLocaleString());
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
            renderCart();
        } else {
            renderCart();
            focusCartItemQty(index);
        }
    }

    function setDirectQty(index, val) {
        if (!cart[index]) return;
        let q = parseInt(val) || 1;
        if (q <= 0) {
            cart.splice(index, 1);
            renderCart();
        } else {
            cart[index].qty = q;
            renderCart();
            focusCartItemQty(index);
        }
    }

    function focusCartItemQty(index) {
        setTimeout(() => {
            let $inp = $(`#cart-qty-${index}`);
            if ($inp.length) {
                $inp[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                $inp.focus();
                $inp.select();
            }
        }, 50);
    }

    function handleQtyKeydown(e, index) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            setDirectQty(index, e.target.value);
            $('#menuSearchInput').val('').focus();
        }
    }

    function updateNote(index, val) {
        if (!cart[index]) return;
        cart[index].notes = val;
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // Customer Selection Management
    let selectedCustomerMode = 'walkin'; // 'walkin' or 'registered'

    function setCustomerMode(mode) {
        selectedCustomerMode = mode;
        if (mode === 'walkin') {
            $('#btnCustomerWalkin').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btnCustomerRegistered').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#walkinCustomerDiv').show();
            $('#registeredCustomerDiv').hide();
        } else {
            $('#btnCustomerRegistered').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btnCustomerWalkin').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#walkinCustomerDiv').hide();
            $('#registeredCustomerDiv').show();
            if ($.fn.select2 && !$('#posCustomerSelect').hasClass('select2-hidden-accessible')) {
                $('#posCustomerSelect').select2({ width: '100%', placeholder: '-- Choose Customer --' });
            }
        }
    }

    function onCustomerSelectChange() {
        let opt = $('#posCustomerSelect option:selected');
        let cid = opt.val();
        if (cid) {
            let name = opt.data('name') || opt.text();
            let phone = opt.data('phone') || '';
            let address = opt.data('address') || '';
            let balance = parseFloat(opt.data('balance')) || 0;

            $('#badgeCustomerName').text(name + (phone ? ' (' + phone + ')' : ''));
            let balLabel = 'Bal: Rs ' + Math.abs(balance).toLocaleString() + (balance >= 0 ? ' (Dr)' : ' (Cr)');
            $('#badgeCustomerBalance').text(balLabel).removeClass('bg-success bg-danger bg-primary').addClass(balance > 0 ? 'bg-danger' : 'bg-success');
            $('#selectedCustomerBadge').show();

            // Pre-fill delivery info if empty or in delivery mode
            if (currentOrderMode === 'delivery') {
                if (phone && !$('#posCustomerPhone').val()) {
                    $('#posCustomerPhone').val(phone);
                }
                if (address && !$('#posDeliveryAddress').val()) {
                    $('#posDeliveryAddress').val(address);
                }
            }
        } else {
            $('#selectedCustomerBadge').hide();
        }
    }

    function openQuickCustomerModal() {
        $('#quickCustomerForm')[0].reset();
        $('#quickCustomerModal').modal('show');
        setTimeout(() => $('#qc_name').focus(), 300);
    }

    function saveQuickCustomer(e) {
        e.preventDefault();
        let btn = $('#btnSaveQuickCustomer');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('customers.store') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                customer_name: $('#qc_name').val(),
                mobile: $('#qc_mobile').val(),
                address: $('#qc_address').val() || null,
                opening_balance: $('#qc_balance').val() || 0
            },
            success: function(resp) {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Customer');
                let cust = resp.data || resp.customer || resp;
                if (cust && cust.id) {
                    $('#quickCustomerModal').modal('hide');
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Customer added successfully', showConfirmButton: false, timer: 1500 });
                    
                    let bal = parseFloat(cust.opening_balance) || 0;
                    let optText = cust.customer_name + (cust.mobile ? ' (' + cust.mobile + ')' : '') + ' — Bal: Rs ' + Math.abs(bal).toLocaleString() + (bal >= 0 ? ' Dr' : ' Cr');
                    let newOption = $('<option>', {
                        value: cust.id,
                        text: optText,
                        'data-name': cust.customer_name,
                        'data-phone': cust.mobile || '',
                        'data-address': cust.address || '',
                        'data-balance': bal,
                        selected: true
                    });
                    $('#posCustomerSelect').append(newOption);
                    if ($.fn.select2 && $('#posCustomerSelect').hasClass('select2-hidden-accessible')) {
                        $('#posCustomerSelect').trigger('change');
                    }
                    setCustomerMode('registered');
                    onCustomerSelectChange();
                } else {
                    Swal.fire('Error', 'Failed to create customer.', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Customer');
                let msg = 'Failed to create customer.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    function setDiscountType(type) {
        orderDiscountType = type;
        if (type === 'flat') {
            $('#btnDiscTypeFlat, #modalBtnDiscTypeFlat').addClass('active');
            $('#btnDiscTypePercent, #modalBtnDiscTypePercent').removeClass('active');
            $('#orderDiscountInput, #modalDiscountInput').attr('placeholder', 'Rs');
        } else {
            $('#btnDiscTypePercent, #modalBtnDiscTypePercent').addClass('active');
            $('#btnDiscTypeFlat, #modalBtnDiscTypeFlat').removeClass('active');
            $('#orderDiscountInput, #modalDiscountInput').attr('placeholder', '%');
        }
        renderCart();
    }

    function onDiscountInputChanged() {
        let val = parseFloat($('#orderDiscountInput').val());
        orderDiscountValue = isNaN(val) ? 0 : Math.max(0, val);
        $('#modalDiscountInput').val(orderDiscountValue);
        renderCart();
    }

    function syncModalDiscount() {
        let val = parseFloat($('#modalDiscountInput').val());
        orderDiscountValue = isNaN(val) ? 0 : Math.max(0, val);
        $('#orderDiscountInput').val(orderDiscountValue);
        renderCart();

        let subtotal = getSubtotal();
        let disc = getDiscountAmount();
        let total = getTotalBill();
        $('#modalSubtotalAmount').text('Rs. ' + subtotal.toLocaleString());
        $('#modalDiscountDisplay').text('- Rs. ' + disc.toLocaleString());
        $('#modalPayableAmount').text('Rs. ' + total.toLocaleString());
        $('#cashReceivedInput').val(total);
        calculateChange();
    }

    function applyQuickPercent(pct) {
        orderDiscountType = 'percent';
        orderDiscountValue = pct;
        $('#btnDiscTypePercent, #modalBtnDiscTypePercent').addClass('active');
        $('#btnDiscTypeFlat, #modalBtnDiscTypeFlat').removeClass('active');
        $('#orderDiscountInput, #modalDiscountInput').val(pct);
        renderCart();
    }

    function clearDiscount() {
        orderDiscountValue = 0;
        $('#orderDiscountInput, #modalDiscountInput').val(0);
        renderCart();
    }

    function getSubtotal() {
        let itemsTotal = cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
        let deliveryCharges = currentOrderMode === 'delivery' ? (parseFloat($('#posDeliveryCharges').val()) || 0) : 0;
        return itemsTotal + deliveryCharges;
    }

    function getDiscountAmount() {
        let subtotal = getSubtotal();
        if (subtotal <= 0 || orderDiscountValue <= 0) return 0;

        let disc = 0;
        if (orderDiscountType === 'percent') {
            disc = (subtotal * orderDiscountValue) / 100;
        } else {
            disc = orderDiscountValue;
        }
        return Math.min(subtotal, Math.round(disc * 100) / 100);
    }

    function getTotalBill() {
        let subtotal = getSubtotal();
        let disc = getDiscountAmount();
        return Math.max(0, subtotal - disc);
    }

    function resetCart() {
        cart = [];
        activeSaleId = null;
        orderDiscountValue = 0;
        orderDiscountType = 'flat';
        $('#orderDiscountInput, #modalDiscountInput').val(0);
        $('#btnDiscTypeFlat, #modalBtnDiscTypeFlat').addClass('active');
        $('#btnDiscTypePercent, #modalBtnDiscTypePercent').removeClass('active');
        $('#orderRemarksInput').val('');
        $('#customerWalkinName').val('Walk-in Customer');
        $('#posDeliveryCustomerName').val('');
        $('#posCustomerPhone').val('');
        $('#posDeliveryAddress').val('');
        $('#posDeliveryCharges').val(0);
        $('#posDeliveryRemarks').val('');
        $('#posDeliveryCustomerId').val('');
        $('#deliveryCustomerFoundNotice').addClass('d-none');
        $('#posPhoneLookupSpinner').addClass('d-none');
        if (typeof lastSearchedDeliveryPhone !== 'undefined') {
            lastSearchedDeliveryPhone = '';
        }
        $('#posRiderSelect').val('');
        $('#posCustomerSelect').val('');
        if ($.fn.select2 && $('#posCustomerSelect').hasClass('select2-hidden-accessible')) {
            $('#posCustomerSelect').trigger('change');
        }
        setCustomerMode('walkin');
        $('#selectedCustomerBadge').hide();
        renderCart();
    }

    // Payment Settlement Modal
    function openPaymentModal() {
        if (cart.length === 0) {
            Swal.fire('Empty Order', 'Please add items to the order first.', 'warning');
            return;
        }
        let subtotal = getSubtotal();
        let disc = getDiscountAmount();
        let total = getTotalBill();

        $('#modalSubtotalAmount').text('Rs. ' + subtotal.toLocaleString());
        $('#modalDiscountInput').val(orderDiscountValue);
        $('#modalDiscountDisplay').text('- Rs. ' + disc.toLocaleString());
        $('#modalPayableAmount').text('Rs. ' + total.toLocaleString());

        if (orderDiscountType === 'flat') {
            $('#modalBtnDiscTypeFlat').addClass('active');
            $('#modalBtnDiscTypePercent').removeClass('active');
        } else {
            $('#modalBtnDiscTypePercent').addClass('active');
            $('#modalBtnDiscTypeFlat').removeClass('active');
        }

        let isRegistered = selectedCustomerMode === 'registered' && $('#posCustomerSelect').val();
        if (isRegistered) {
            let opt = $('#posCustomerSelect option:selected');
            $('#modalCustName').text(opt.data('name') || opt.text());
            let bal = parseFloat(opt.data('balance')) || 0;
            let balLabel = 'Rs ' + Math.abs(bal).toLocaleString() + (bal >= 0 ? ' (Dr)' : ' (Cr)');
            $('#modalCustBal').text(balLabel).removeClass('bg-success bg-danger bg-secondary').addClass(bal > 0 ? 'bg-danger' : 'bg-success');
            $('#modalCustBalWrapper').show();
            $('#btnPresetCredit').show();
        } else {
            let walkin = $('#customerWalkinName').val() || 'Walk-in Customer';
            $('#modalCustName').text(walkin);
            $('#modalCustBalWrapper').hide();
            $('#btnPresetCredit').hide();
        }

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

    function setFullCredit() {
        $('#cashReceivedInput').val(0);
        calculateChange();
        $('#cashReceivedInput').focus();
    }

    function calculateChange() {
        let total = getTotalBill();
        let received = parseFloat($('#cashReceivedInput').val());
        if (isNaN(received)) received = 0;

        let isRegistered = selectedCustomerMode === 'registered' && $('#posCustomerSelect').val();

        if (received >= total) {
            let change = received - total;
            $('#statusChangeRow').show();
            $('#statusCreditRow').hide();
            $('#statusWalkinWarningRow').hide();
            $('#modalChangeDue').text('Rs. ' + change.toLocaleString());
            $('#btnConfirmPayment').prop('disabled', false).html('<i class="fas fa-print me-1"></i> Complete & Print Receipt [Enter]');
        } else {
            // Received < Total (Partial or Full Credit)
            let creditAmount = total - received;
            if (isRegistered) {
                $('#statusChangeRow').hide();
                $('#statusCreditRow').show();
                $('#statusWalkinWarningRow').hide();
                $('#modalCreditDue').text('Rs. ' + creditAmount.toLocaleString());

                let opt = $('#posCustomerSelect option:selected');
                let prevBal = parseFloat(opt.data('balance')) || 0;
                let newBal = prevBal + creditAmount;
                let newBalText = 'New Customer Balance: Rs. ' + Math.abs(newBal).toLocaleString() + (newBal >= 0 ? ' (Dr)' : ' (Cr)');
                $('#modalNewBalanceText').text(newBalText);

                if (received === 0) {
                    $('#btnConfirmPayment').prop('disabled', false).html('<i class="fas fa-hand-holding-usd me-1"></i> Complete as Credit Sale (Udhar) [Enter]');
                } else {
                    $('#btnConfirmPayment').prop('disabled', false).html('<i class="fas fa-print me-1"></i> Partial Pay & Credit Settle [Enter]');
                }
            } else {
                $('#statusChangeRow').hide();
                $('#statusCreditRow').hide();
                $('#statusWalkinWarningRow').show();
                $('#btnConfirmPayment').prop('disabled', true).html('<i class="fas fa-ban me-1"></i> Cannot Settle (Credit not allowed for Walk-in)');
            }
        }
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

        // Validation for Delivery Mode: ONLY Phone number is mandatory; Name and Address are optional
        if (currentOrderMode === 'delivery' || actionType === 'delivery_dispatch') {
            let dPhone = ($('#posCustomerPhone').val() || '').trim();

            if (!dPhone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Phone Number Lazmi He!',
                    text: 'Delivery order ke lie Customer ka Phone number likhna lazmi he taake rider rabta kar sake.',
                    confirmButtonText: 'Theek He',
                    confirmButtonColor: '#2563eb'
                });
                $('#posCustomerPhone').focus();
                return;
            }

            // Rider selection mandatory for Dispatch
            if (actionType === 'delivery_dispatch') {
                let rId = $('#posRiderSelect').val();
                if (!rId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Rider Select Karein!',
                        text: 'Delivery dispatch karne ke lie Rider select karna lazmi he. Meharbani farma kar Rider select karein.',
                        confirmButtonText: 'Theek He',
                        confirmButtonColor: '#2563eb'
                    });
                    $('#posRiderSelect').focus();
                    return;
                }
            }
        }

        let isRegistered = (currentOrderMode !== 'delivery') && (selectedCustomerMode === 'registered' && $('#posCustomerSelect').val());
        let customerId = isRegistered ? $('#posCustomerSelect').val() : null;
        let walkin = (currentOrderMode === 'delivery')
            ? ($('#posDeliveryCustomerName').val().trim() || 'Delivery Customer')
            : (isRegistered ? null : ($('#customerWalkinName').val().trim() || 'Walk-in Customer'));

        let target = $('#orderTargetInput').val() || '';
        let remarks = (currentOrderMode === 'delivery' && $('#posDeliveryRemarks').val())
            ? $('#posDeliveryRemarks').val()
            : ($('#orderRemarksInput').val() || '');
        let total = getTotalBill();

        let rawCash = parseFloat($('#cashReceivedInput').val());
        let cash = actionType === 'post' ? (isNaN(rawCash) ? total : rawCash) : 0;

        if (actionType === 'post' && !isRegistered && cash < total) {
            Swal.fire('Credit Not Allowed', 'Cash received cannot be less than Total Bill for Walk-in Customer. Please switch to Registered Customer for Credit (Udhar) sales.', 'warning');
            return;
        }

        let selectedAccountId = $('#paymentAccountSelect').val() || 1;

        let riderId = $('#posRiderSelect').val() || null;
        let selectedRiderOpt = $('#posRiderSelect option:selected');
        let riderName = selectedRiderOpt.data('name') || null;
        let customerPhone = $('#posCustomerPhone').val().trim() || null;
        let deliveryAddress = $('#posDeliveryAddress').val().trim() || null;
        let deliveryCharges = parseFloat($('#posDeliveryCharges').val()) || 0;

        let receiptAmount = [];
        let receiptAccountId = [];
        if (actionType === 'post' && cash > 0) {
            receiptAccountId = [selectedAccountId];
            receiptAmount = [Math.min(cash, total)];
        }

        let discount = getDiscountAmount();

        let formData = {
            _token: '{{ csrf_token() }}',
            action: actionType === 'post' ? 'post' : 'running',
            order_type: currentOrderMode,
            table_no: currentOrderMode === 'dine_in' ? target : null,
            token_no: currentOrderMode === 'takeaway' ? target : null,
            rider_id: currentOrderMode === 'delivery' ? riderId : null,
            rider_name: currentOrderMode === 'delivery' ? riderName : null,
            rider_phone: currentOrderMode === 'delivery' ? customerPhone : null,
            delivery_address: currentOrderMode === 'delivery' ? deliveryAddress : null,
            delivery_charges: currentOrderMode === 'delivery' ? deliveryCharges : 0,
            delivery_status: (actionType === 'delivery_dispatch' || riderId) ? 'dispatched' : 'pending',
            walkin_name: walkin,
            customer: customerId,
            is_walkin: isRegistered ? '0' : '1',
            reference: remarks,
            order_notes: remarks,
            total_extra_cost: discount,
            cash: cash,
            change: actionType === 'post' ? Math.max(0, cash - total) : 0,
            receipt_account_id: receiptAccountId,
            receipt_amount: receiptAmount,
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

        let loadingTitle = 'Processing...';
        if (actionType === 'post') loadingTitle = 'Processing Bill...';
        else if (actionType === 'kot') loadingTitle = 'Sending to Kitchen...';
        else if (actionType === 'delivery_dispatch') loadingTitle = 'Dispatching Delivery & Printing Bill...';
        else if (actionType === 'print_bill') loadingTitle = 'Printing Guest Bill...';

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
                    let saleId = res.booking_id || res.sale_id;

                    if (actionType === 'kot') {
                        let kotUrl = res.kot_url || `{{ url('sales') }}/${saleId}/kot`;
                        printThermalDirect(kotUrl);

                        Swal.fire({
                            icon: 'success',
                            title: 'Sent to Kitchen!',
                            html: `
                                <div class="text-muted small mb-2">KOT ticket printed. Order is active.</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="openCenteredPrintWindow('${kotUrl}', 520, 750)">
                                    <i class="fas fa-print me-1"></i> Re-print KOT
                                </button>
                            `,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    } else if (actionType === 'delivery_dispatch') {
                        let billUrl = res.receipt_url || res.invoice_url || `{{ url('sales') }}/${saleId}/recepit?from=pos&provisional=1`;
                        if (!billUrl.includes('provisional=1')) {
                            billUrl += (billUrl.includes('?') ? '&' : '?') + 'provisional=1';
                        }
                        printThermalDirect(billUrl);

                        Swal.fire({
                            icon: 'success',
                            title: 'Delivery Dispatched & Bill Printed!',
                            html: `
                                <div class="text-muted small mb-2">Delivery bill printed for rider. Order is active in <b>Running Orders</b>.</div>
                                <div class="p-2 mb-2 rounded bg-light border">
                                    <span class="text-muted small">Rider collects cash:</span>
                                    <div class="fs-5 fw-bold text-success">Rs. ${total.toLocaleString()} (COD)</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCenteredPrintWindow('${billUrl}', 520, 750)">
                                    <i class="fas fa-print me-1"></i> Re-print Rider Bill
                                </button>
                            `,
                            timer: 2500,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    } else if (actionType === 'print_bill') {
                        let billUrl = `{{ url('restaurant/orders') }}/${saleId}/bill-slip`;
                        printThermalDirect(billUrl);

                        Swal.fire({
                            icon: 'success',
                            title: 'Guest Bill Printed!',
                            html: `
                                <div class="text-muted small mb-2">Provisional bill printed. Order is active in Running Orders.</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openCenteredPrintWindow('${billUrl}', 520, 750)">
                                    <i class="fas fa-print me-1"></i> Re-print Bill
                                </button>
                            `,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        resetCart();
                        refreshActiveOrders();
                    } else {
                        let receiptUrl = res.receipt_url || res.invoice_url || `{{ url('sales') }}/${saleId}/recepit`;
                        printThermalDirect(receiptUrl);

                        Swal.fire({
                            icon: 'success',
                            title: 'Bill Completed!',
                            html: `
                                <div class="text-muted small mb-2">Payment received and receipt printed.</div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="openCenteredPrintWindow('${receiptUrl}', 520, 750)">
                                    <i class="fas fa-print me-1"></i> Re-print Receipt
                                </button>
                            `,
                            timer: 1200,
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

    let drawerOrdersCache = [];
    let currentDrawerFilter = 'all';
    let drawerSearchQuery = '';

    // Instant Search in Running Orders Sidebar
    function onDrawerSearchInput(val) {
        drawerSearchQuery = (val || '').toLowerCase().trim();
        if (drawerSearchQuery.length > 0) {
            $('#roSearchClearBtn').removeClass('d-none');
        } else {
            $('#roSearchClearBtn').addClass('d-none');
        }
        renderDrawerCards();
    }

    function clearDrawerSearch() {
        $('#roSearchInput').val('');
        drawerSearchQuery = '';
        $('#roSearchClearBtn').addClass('d-none');
        renderDrawerCards();
    }

    // Toggle Active Running Orders Sidebar (Desktop collapse/expand or Mobile offcanvas)
    function toggleRunningOrdersSidebar() {
        if ($(window).width() >= 992) {
            $('#runningOrdersDrawer').toggleClass('ro-collapsed');
            if (!$('#runningOrdersDrawer').hasClass('ro-collapsed')) {
                refreshActiveOrders();
            }
        } else {
            if ($('#runningOrdersDrawer').hasClass('active')) {
                closeRunningOrdersDrawer();
            } else {
                openRunningOrdersModal();
            }
        }
    }

    // Open Active Running Orders (Desktop expands docked panel, Mobile slides in drawer)
    function openRunningOrdersModal() {
        refreshActiveOrders();
        if ($(window).width() >= 992) {
            $('#runningOrdersDrawer').removeClass('ro-collapsed');
        } else {
            $('#runningOrdersBackdrop').addClass('active');
            $('#runningOrdersDrawer').addClass('active');
        }
    }

    // Close Active Running Orders
    function closeRunningOrdersDrawer() {
        if ($(window).width() >= 992) {
            $('#runningOrdersDrawer').addClass('ro-collapsed');
        } else {
            $('#runningOrdersBackdrop').removeClass('active');
            $('#runningOrdersDrawer').removeClass('active');
        }
    }

    // Filter cards inside drawer
    function filterDrawerOrders(type) {
        currentDrawerFilter = type;
        $('.ro-filter-pill').removeClass('active');
        $(`.ro-filter-pill[data-filter="${type}"]`).addClass('active');
        renderDrawerCards();
    }

    // Refresh Active Running Orders via AJAX
    function refreshActiveOrders(btn) {
        let $icon = btn ? $(btn).find('i') : $('#roSyncIcon');
        $icon.addClass('fa-spin');
        $.get('{{ route("restaurant.active_orders") }}', function(res) {
            $icon.removeClass('fa-spin');
            if (res.success) {
                drawerOrdersCache = res.orders || [];
                $('#badgeRunningCount, #mHeaderActiveCount, #mBottomBadgeTables').text(res.count);
                $('#drawerActiveSubtitle').text(`${res.count} Active`);

                let totalAmt = drawerOrdersCache.reduce((sum, o) => sum + (parseFloat(o.total_bill) || 0), 0);
                $('#drawerFooterTotal').text(`Rs. ${totalAmt.toLocaleString()}`);

                renderDrawerCards();
            }
        }).fail(function() {
            $icon.removeClass('fa-spin');
        });
    }

    // Render cards into the slide-in drawer
    function renderDrawerCards() {
        let filtered = drawerOrdersCache;

        // 1. Filter by category pill (All / Dine-in / Takeaway / Delivery)
        if (currentDrawerFilter !== 'all') {
            filtered = filtered.filter(o => o.order_type === currentDrawerFilter);
        }

        // 2. Filter by search query (Invoice, Customer, Phone, Table, Token, Rider, Items, Amount)
        if (drawerSearchQuery) {
            filtered = filtered.filter(o => {
                let inv = (o.invoice_no || '').toLowerCase();
                let cust = (o.customer_name || '').toLowerCase();
                let ph = (o.customer_phone || '').toLowerCase();
                let tbl = (o.table_no || '').toLowerCase();
                let tok = (o.token_no ? String(o.token_no) : '').toLowerCase();
                let rdr = (o.rider_name || '').toLowerCase();
                let itms = (o.items_summary || '').toLowerCase();
                let tot = (o.total_bill ? String(o.total_bill) : '').toLowerCase();

                return inv.includes(drawerSearchQuery) ||
                       cust.includes(drawerSearchQuery) ||
                       ph.includes(drawerSearchQuery) ||
                       tbl.includes(drawerSearchQuery) ||
                       tok.includes(drawerSearchQuery) ||
                       rdr.includes(drawerSearchQuery) ||
                       itms.includes(drawerSearchQuery) ||
                       tot.includes(drawerSearchQuery);
            });
        }

        let container = $('#runningOrdersListContainer');

        if (filtered.length === 0) {
            if (drawerSearchQuery) {
                container.html(`
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-search fs-2 text-secondary mb-2"></i>
                        <p class="mb-1 fw-bold text-dark">No orders found</p>
                        <small class="d-block mb-3">No active order matches "<b>${$('<div>').text(drawerSearchQuery).html()}</b>"</small>
                        <button type="button" class="btn btn-xs btn-outline-primary fw-bold px-3 py-1" onclick="clearDrawerSearch()">
                            <i class="fas fa-times me-1"></i> Clear Search
                        </button>
                    </div>
                `);
            } else {
                container.html(`
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-check-circle fs-1 text-success mb-2"></i>
                        <p class="mb-0 fw-bold">No active running orders in this filter.</p>
                        <small>All tables/orders are clear!</small>
                    </div>
                `);
            }
            return;
        }

        let html = '';
        filtered.forEach(o => {
            let typeBadge = '';
            let cardTypeClass = `ro-card-${o.order_type}`;
            if (o.order_type === 'dine_in') {
                typeBadge = `<span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.68rem;"><i class="fas fa-chair"></i> Table ${o.table_no || 'T-1'}</span>`;
            } else if (o.order_type === 'takeaway') {
                typeBadge = `<span class="badge" style="background:#fffbeb; color:#b45309; border:1px solid #fde68a; font-size:0.68rem;"><i class="fas fa-shopping-bag"></i> Token #${o.token_no || '1'}</span>`;
            } else {
                typeBadge = `<span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:0.68rem;"><i class="fas fa-motorcycle"></i> Delivery</span>`;
            }

            let statusBadge = '';
            if (o.order_status === 'served') {
                statusBadge = `<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size: 0.68rem;"><i class="fas fa-concierge-bell"></i> Served</span>`;
            } else if (o.order_status === 'on_delivery') {
                statusBadge = `<span class="badge" style="background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff; font-size: 0.68rem;"><i class="fas fa-motorcycle"></i> On Delivery</span>`;
            } else {
                statusBadge = `<span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size: 0.68rem;"><i class="fas fa-fire"></i> Cooking</span>`;
            }

            let paymentBadge = o.payment_status === 'paid' ? 
                `<span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size: 0.68rem;"><i class="fas fa-check"></i> Paid</span>` : 
                `<span class="badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; font-size: 0.68rem;"><i class="fas fa-clock"></i> Unpaid</span>`;

            let riderBadge = o.rider_name ? `<span class="badge bg-light text-dark border" style="font-size:0.68rem;"><i class="fas fa-user-tag text-primary"></i> ${o.rider_name}</span>` : '';

            html += `
                <div class="ro-drawer-card ${cardTypeClass}">
                    <!-- Card Top Row -->
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">#${o.invoice_no}</span>
                            ${typeBadge}
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark" style="font-size: 0.92rem;">Rs. ${parseFloat(o.total_bill).toLocaleString()}</span>
                        </div>
                    </div>

                    <!-- Customer & Items Row -->
                    <div class="small text-muted mb-1 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark text-truncate" style="max-width: 170px;" title="${o.customer_name}">
                            <i class="fas fa-user-circle text-secondary"></i> ${o.customer_name} ${o.customer_phone ? `(${o.customer_phone})` : ''}
                        </span>
                        <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.66rem;">
                            <i class="far fa-clock text-danger me-1"></i>${o.elapsed_time}
                        </span>
                    </div>

                    <div class="small mb-1.5 text-truncate px-2 py-1 rounded" style="font-size: 0.72rem; background: #f8fafc; border: 1px dashed #cbd5e1; color: #334155; font-weight: 600;">
                        <i class="fas fa-utensils me-1 text-primary"></i> ${o.items_summary}
                    </div>

                    <!-- Status Badges Row -->
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <div class="d-flex gap-1 align-items-center flex-wrap">
                            ${statusBadge}
                            ${paymentBadge}
                            ${riderBadge}
                        </div>
                    </div>

                    <!-- Multi-Action Buttons Grid -->
                    <div class="pt-1.5 border-top d-flex gap-1 flex-wrap align-items-center justify-content-between">
                        <div class="d-flex gap-1 flex-wrap">
                            <!-- View Details -->
                            <button type="button" class="ro-card-btn" onclick="viewOrderDetails(${o.id})" title="View Items & Details">
                                <i class="fas fa-eye text-primary"></i> Details
                            </button>

                            <!-- Edit / Recall into POS -->
                            <button type="button" class="ro-card-btn" onclick="loadRunningOrder(${o.id})" title="Edit Order in POS">
                                <i class="fas fa-edit text-info"></i> Edit
                            </button>

                            <!-- Kitchen Slip KOT -->
                            <button type="button" class="ro-card-btn" onclick="openCenteredPrintWindow('{{ url("sales") }}/${o.id}/kot', 520, 750)" title="Re-print Kitchen KOT">
                                <i class="fas fa-utensils text-warning"></i> KOT
                            </button>

                            <!-- Bill Slip (Pre-payment) -->
                            <button type="button" class="ro-card-btn" onclick="openCenteredPrintWindow('{{ url("restaurant/orders") }}/${o.id}/bill-slip', 520, 750)" title="Print Bill Slip">
                                <i class="fas fa-file-invoice text-secondary"></i> Bill
                            </button>

                            <!-- Serve (Dine-in only, if not served) -->
                            ${o.order_type === 'dine_in' && o.order_status !== 'served' ? `
                                <button type="button" class="ro-card-btn ro-card-btn-serve" onclick="markOrderServed(${o.id})" title="Mark Served to Table">
                                    <i class="fas fa-check"></i> Serve
                                </button>
                            ` : ''}

                            <!-- Deliver (Delivery only, if not on_delivery) -->
                            ${o.order_type === 'delivery' && o.order_status !== 'on_delivery' ? `
                                <button type="button" class="ro-card-btn ro-card-btn-dispatch" onclick="openAssignRiderModal(${o.id}, '${o.invoice_no}', '${o.rider_id || ''}')" title="Assign Rider & Dispatch">
                                    <i class="fas fa-motorcycle"></i> Deliver
                                </button>
                            ` : ''}
                        </div>

                        <div class="d-flex gap-1">
                            <!-- Unified Complete & Settle -->
                            <button type="button" class="ro-card-btn ro-card-btn-complete" onclick="openCompleteSettleModal(${o.id}, '${o.invoice_no}', ${o.total_bill})" title="Receive Payment & Close Order">
                                <i class="fas fa-check-double"></i> Complete
                            </button>

                            <!-- Cancel -->
                            <button type="button" class="ro-card-btn ro-card-btn-cancel" onclick="cancelOrderAction(${o.id}, '${o.invoice_no}')" title="Cancel Order">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
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

                $('#btnOdPrintKot').attr('onclick', `openCenteredPrintWindow('{{ url("sales") }}/${o.id}/kot', 520, 750)`);
                $('#btnOdPrintBill').attr('onclick', `openCenteredPrintWindow('{{ url("restaurant/orders") }}/${o.id}/bill-slip', 520, 750)`);

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
                            timer: 1000,
                            showConfirmButton: false
                        });
                        refreshActiveOrders();
                    } else {
                        Swal.fire('Error', res.message || 'Failed to serve order.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server communication error.', 'error');
                });
            }
        });
    }

    // 3. ASSIGN RIDER (DELIVERY STEP 1 - DIRECT 1-CLICK RIDER LIST)
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
                        });
                        refreshActiveOrders();
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
    let csCurrentPayable = 0;
    function openCompleteSettleModal(orderId, invoiceNo, payable) {
        $('#csOrderId').val(orderId);
        $('#csModalSubtitle').text('Invoice: #' + invoiceNo);
        csCurrentPayable = parseFloat(payable) || 0;
        $('#csTotalPayable').text('Rs. ' + csCurrentPayable.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#csCashReceived').val(csCurrentPayable);
        $('#csDiscount').val('');
        $('#csChangeReturn').val('0.00');
        $('#completeSettleModal').modal('show');
        setTimeout(() => {
            $('#csCashReceived').focus().select();
        }, 350);
    }

    function calculateCsChange() {
        let disc = parseFloat($('#csDiscount').val()) || 0;
        let adjustedPayable = Math.max(0, csCurrentPayable - disc);
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
                    openCenteredPrintWindow(res.receipt_url, 520, 750);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Completed & Settled!',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });

                // If currently editing this order, reset cart
                if (activeSaleId == orderId) {
                    resetCart();
                }
                refreshActiveOrders();
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
                    reason: result.value || 'Cancelled by cashier'
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Cancelled',
                            text: res.message,
                            timer: 1000,
                            showConfirmButton: false
                        });
                        if (activeSaleId == orderId) {
                            resetCart();
                        }
                        refreshActiveOrders();
                    } else {
                        Swal.fire('Error', res.message || 'Failed to cancel order.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Server connection failed.', 'error');
                });
            }
        });
    }

    // Load Running Order back into POS Cart
    function loadRunningOrder(id) {
        Swal.fire({
            title: 'Loading Table Order...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.get(`{{ url('restaurant/order') }}/${id}`, function(res) {
            Swal.close();
            if ($(window).width() < 992) {
                closeRunningOrdersDrawer();
            }

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
                    let rdr = s.rider_name ? ('Rider: ' + s.rider_name) : 'Delivery Order';
                    $('#orderTargetInput').val(rdr);
                    if (s.rider_id) {
                        $('#posRiderSelect').val(s.rider_id);
                    } else {
                        $('#posRiderSelect').val('');
                    }
                    $('#posDeliveryCharges').val(s.delivery_charges !== undefined ? s.delivery_charges : 0);
                    $('#posDeliveryAddress').val(s.delivery_address || '');
                    let phone = s.customer_phone || s.rider_phone || '';
                    $('#posCustomerPhone').val(phone);

                    let dCustName = s.walkin_name || s.customer_name || '';
                    if (dCustName === 'Walk-in Customer' && s.customer_name) {
                        dCustName = s.customer_name;
                    }
                    $('#posDeliveryCustomerName').val(dCustName);
                    $('#customerWalkinName').val(dCustName || 'Walk-in Customer');
                    $('#posDeliveryRemarks').val(s.order_notes || '');
                    if (s.customer_id) {
                        $('#posDeliveryCustomerId').val(s.customer_id);
                    } else {
                        $('#posDeliveryCustomerId').val('');
                    }
                }
                if (s.customer_id) {
                    setCustomerMode('registered');
                    $('#posCustomerSelect').val(s.customer_id);
                    if ($.fn.select2 && $('#posCustomerSelect').hasClass('select2-hidden-accessible')) {
                        $('#posCustomerSelect').trigger('change');
                    } else {
                        onCustomerSelectChange();
                    }
                } else {
                    setCustomerMode('walkin');
                    $('#customerWalkinName').val(s.walkin_name || 'Walk-in Customer');
                    $('#selectedCustomerBadge').hide();
                }

                // Ensure delivery fields are never wiped out after customer mode initialization
                if (s.order_type === 'delivery') {
                    if (s.customer_phone || s.rider_phone) {
                        $('#posCustomerPhone').val(s.customer_phone || s.rider_phone);
                    }
                    if (s.delivery_address) {
                        $('#posDeliveryAddress').val(s.delivery_address);
                    }
                    let dFinalName = s.walkin_name || s.customer_name || '';
                    if (dFinalName && dFinalName !== 'Walk-in Customer') {
                        $('#posDeliveryCustomerName').val(dFinalName);
                    }
                }

                $('#orderRemarksInput').val(s.order_notes || '');

                let extDisc = parseFloat(s.total_extradiscount || s.total_discount || 0);
                if (extDisc > 0) {
                    orderDiscountValue = extDisc;
                    orderDiscountType = 'flat';
                    $('#btnDiscTypeFlat, #modalBtnDiscTypeFlat').addClass('active');
                    $('#btnDiscTypePercent, #modalBtnDiscTypePercent').removeClass('active');
                    $('#orderDiscountInput, #modalDiscountInput').val(orderDiscountValue);
                } else {
                    orderDiscountValue = 0;
                    orderDiscountType = 'flat';
                    $('#orderDiscountInput, #modalDiscountInput').val(0);
                }

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
        // Init Select2 on customer dropdown
        if ($.fn.select2) {
            $('#posCustomerSelect').select2({
                placeholder: '-- Choose Customer --',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                onCustomerSelectChange();
            });
        }

        // Desktop auto-focus search
        if ($(window).width() >= 992) {
            setTimeout(() => {
                $('#menuSearchInput').focus();
                highlightFirstVisible();
            }, 150);
        } else {
            switchMobileView('menu');
        }

        // Check URL parameter ?recall_id=xxx
        let urlParams = new URLSearchParams(window.location.search);
        let recallId = urlParams.get('recall_id');
        if (recallId) {
            loadRunningOrder(recallId);
        }

        // Immediately load Running Orders into docked sidebar
        refreshActiveOrders();

        // Periodic poll for active tables/orders
        setInterval(refreshActiveOrders, 15000);
    });
</script>
@endsection
