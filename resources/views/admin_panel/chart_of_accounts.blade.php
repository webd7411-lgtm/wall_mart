@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ==========================================================================
       VIP / EXECUTIVE PROFESSIONAL CHART OF ACCOUNTS DESIGN SYSTEM
       - 100% Strict Zero Horizontal Scroll on ALL Devices
       - Laptop: Single-Row Aligned Header Action Buttons (Ek line me tarteeb se)
       - Mobile: Flawless Pixel-Perfect Responsive Card Box (No overflow)
       ========================================================================== */

    :root {
        --coa-primary: #4f46e5;
        --coa-primary-hover: #4338ca;
        --coa-primary-soft: #eef2ff;
        --coa-secondary: #0f172a;
        --coa-success: #10b981;
        --coa-success-soft: #ecfdf5;
        --coa-warning: #f59e0b;
        --coa-warning-soft: #fffbeb;
        --coa-danger: #ef4444;
        --coa-danger-soft: #fef2f2;
        --coa-info: #0ea5e9;
        --coa-info-soft: #f0f9ff;
        --coa-card-bg: #ffffff;
        --coa-border: #e2e8f0;
        --coa-border-light: #f1f5f9;
        --coa-text-main: #0f172a;
        --coa-text-muted: #64748b;
        --coa-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.04);
        --coa-shadow-md: 0 4px 14px rgba(15, 23, 42, 0.05);
        --coa-radius-sm: 8px;
        --coa-radius-md: 12px;
        --coa-radius-lg: 16px;
    }

    /* Strict Zero Horizontal Scroll Containment */
    html, body {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .coa-page-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
        padding-bottom: 2rem !important;
    }

    @media (max-width: 767px) {
        .coa-page-wrapper {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
    }

    .coa-page-wrapper * {
        box-sizing: border-box !important;
    }

    .coa-page-wrapper .row {
        margin-left: -6px !important;
        margin-right: -6px !important;
    }
    .coa-page-wrapper .row > [class*="col-"] {
        padding-left: 6px !important;
        padding-right: 6px !important;
    }

    /* ==========================================================================
       1. PROFESSIONAL LIGHT EXECUTIVE HEADER
       - Laptop: All 3 Buttons in ONE neat horizontal line (Tarteeb se ek line me)
       - Mobile: Flawless proportional fit, zero overflow
       ========================================================================== */
    .coa-pro-header {
        background: #ffffff;
        border: 1px solid var(--coa-border);
        border-radius: var(--coa-radius-lg);
        padding: 1.15rem 1.4rem;
        box-shadow: var(--coa-shadow-sm);
        margin-bottom: 1.25rem;
        width: 100% !important;
        max-width: 100% !important;
    }

    .coa-header-icon-box {
        width: 44px;
        height: 44px;
        border-radius: var(--coa-radius-md);
        background: var(--coa-primary-soft);
        color: var(--coa-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        border: 1px solid rgba(79, 70, 229, 0.15);
    }

    .coa-header-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--coa-text-main);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .coa-header-subtitle {
        color: var(--coa-text-muted);
        font-size: 0.84rem;
        margin: 3px 0 0 0;
        font-weight: 500;
    }

    .coa-count-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        vertical-align: middle;
    }

    /* Desktop / Laptop: 3 Buttons in a Single Clean Line */
    .coa-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-direction: row;
        flex-wrap: nowrap;
        margin: 0;
    }

    .coa-btn-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-direction: row;
        flex-wrap: nowrap;
    }

    .btn-coa-primary {
        background: var(--coa-primary);
        color: #ffffff !important;
        border: 1px solid var(--coa-primary);
        border-radius: var(--coa-radius-md);
        padding: 0.5rem 1.15rem;
        font-size: 0.82rem;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.18s ease;
        text-decoration: none !important;
        height: 38px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .btn-coa-primary:hover {
        background: var(--coa-primary-hover);
        border-color: var(--coa-primary-hover);
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-coa-outline {
        background: #ffffff;
        color: #334155 !important;
        border: 1.5px solid var(--coa-border);
        border-radius: var(--coa-radius-md);
        padding: 0.5rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.18s ease;
        text-decoration: none !important;
        height: 38px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .btn-coa-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--coa-text-main);
        transform: translateY(-1px);
    }

    /* Mobile Responsive Header Styles (< 768px) */
    @media (max-width: 767px) {
        .coa-pro-header {
            padding: 12px 14px !important;
            margin-bottom: 12px !important;
            border-radius: 12px !important;
        }

        .coa-header-icon-box {
            width: 38px !important;
            height: 38px !important;
            font-size: 1rem !important;
            border-radius: 8px !important;
        }

        .coa-header-title {
            font-size: 1.15rem !important;
        }

        .coa-header-subtitle {
            font-size: 0.78rem !important;
        }

        .coa-count-chip {
            font-size: 0.68rem !important;
            padding: 1px 6px !important;
        }

        .coa-header-actions {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            gap: 8px !important;
            margin-top: 12px !important;
        }

        .coa-header-actions .btn-coa-primary {
            width: 100% !important;
            height: 38px !important;
            justify-content: center !important;
        }

        .coa-btn-row {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .coa-btn-row .btn-coa-outline {
            width: 100% !important;
            min-width: 0 !important;
            height: 36px !important;
            padding: 0 4px !important;
            font-size: 0.74rem !important;
            justify-content: center !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
    }

    /* ==========================================================================
       2. UNIFIED FILTER & INSTANT SEARCH CENTER
       ========================================================================== */
    .coa-filter-card {
        background: #ffffff;
        border: 1px solid var(--coa-border);
        border-radius: var(--coa-radius-lg);
        padding: 0.95rem 1.25rem;
        box-shadow: var(--coa-shadow-sm);
        margin-bottom: 1.25rem;
        width: 100% !important;
        max-width: 100% !important;
    }

    .coa-search-wrap {
        position: relative;
        width: 100%;
    }

    .coa-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
    }

    .coa-search-input {
        width: 100%;
        height: 42px;
        border-radius: var(--coa-radius-md);
        border: 1.5px solid var(--coa-border);
        background: #f8fafc;
        padding-left: 42px;
        padding-right: 36px;
        font-size: 0.86rem;
        color: var(--coa-text-main);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .coa-search-input:focus {
        background: #ffffff;
        border-color: var(--coa-primary);
        box-shadow: 0 0 0 3px var(--coa-primary-soft);
        outline: none;
    }

    .coa-search-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        display: none;
    }

    .coa-pills-strip {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding: 2px 0;
        width: 100%;
    }

    .coa-pills-strip::-webkit-scrollbar {
        display: none;
    }

    .coa-filter-pill {
        border: 1.5px solid var(--coa-border);
        background: #f8fafc;
        color: var(--coa-text-muted);
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.76rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        user-select: none;
        flex-shrink: 0;
        height: 32px;
    }

    .coa-filter-pill:hover {
        background: #f1f5f9;
        color: var(--coa-text-main);
        border-color: #cbd5e1;
    }

    .coa-filter-pill.active {
        background: var(--coa-primary);
        color: #ffffff;
        border-color: var(--coa-primary);
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    }

    .coa-filter-pill .pill-count {
        background: rgba(0, 0, 0, 0.08);
        padding: 1px 6px;
        border-radius: 9999px;
        font-size: 0.68rem;
    }

    .coa-filter-pill.active .pill-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* ==========================================================================
       3. DESKTOP ENTERPRISE TABLE (SCREEN >= 992px)
       ========================================================================== */
    .coa-table-card {
        background: #ffffff;
        border: 1px solid var(--coa-border);
        border-radius: var(--coa-radius-lg);
        box-shadow: var(--coa-shadow-sm);
        overflow: hidden;
        width: 100%;
        max-width: 100%;
    }

    .coa-table-wrapper {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .coa-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .coa-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 0.95rem 1.15rem;
        border-bottom: 1.5px solid var(--coa-border);
        border-top: none;
        white-space: nowrap;
    }

    .coa-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .coa-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .coa-table tbody td {
        padding: 1rem 1.15rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--coa-border-light);
        font-size: 0.85rem;
        color: var(--coa-text-main);
    }

    .coa-code-chip {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.76rem;
        font-weight: 700;
        background: #f1f5f9;
        color: #334155;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .coa-acc-title {
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--coa-text-main);
        margin-bottom: 2px;
    }

    .coa-category-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.74rem;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        padding: 2px 7px;
        border-radius: 6px;
    }

    .badge-coa-debit {
        background: var(--coa-primary-soft);
        color: var(--coa-primary);
        border: 1px solid rgba(79, 70, 229, 0.2);
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 0.72rem;
    }

    .badge-coa-credit {
        background: var(--coa-warning-soft);
        color: #b45309;
        border: 1px solid rgba(245, 158, 11, 0.25);
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 0.72rem;
    }

    .coa-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 700;
    }

    .coa-status-active {
        background: var(--coa-success-soft);
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .coa-status-inactive {
        background: var(--coa-danger-soft);
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .coa-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .coa-status-active .coa-dot {
        background: #10b981;
    }

    .coa-status-inactive .coa-dot {
        background: #ef4444;
    }

    .coa-action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .btn-coa-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid var(--coa-border);
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        transition: all 0.15s ease;
        cursor: pointer;
        text-decoration: none !important;
        padding: 0;
    }

    .btn-coa-action:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
        color: var(--coa-primary);
    }

    .btn-coa-action.btn-ledger:hover { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }
    .btn-coa-action.btn-edit:hover { background: #f5f3ff; border-color: #c4b5fd; color: #6d28d9; }
    .btn-coa-action.btn-history:hover { background: #f8fafc; border-color: #94a3b8; color: #334155; }
    .btn-coa-action.btn-danger-action:hover { background: #fef2f2; border-color: #fca5a5; color: #dc2626; }
    .btn-coa-action.btn-success-action:hover { background: #ecfdf5; border-color: #86efac; color: #059669; }

    /* ==========================================================================
       4. MOBILE VIP ADJUSTMENT (< 992px)
       - Perfect Proportion & Zero Horizontal Overflow
       - Banking Card Touch Interface
       ========================================================================== */
    .coa-mobile-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .coa-m-card {
        background: #ffffff;
        border-radius: var(--coa-radius-md);
        border: 1px solid var(--coa-border);
        box-shadow: var(--coa-shadow-sm);
        padding: 12px 14px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .coa-m-card.is-debit {
        border-left: 4px solid var(--coa-primary);
    }

    .coa-m-card.is-credit {
        border-left: 4px solid var(--coa-warning);
    }

    .coa-m-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: 0.5rem;
    }

    .coa-m-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--coa-text-main);
        margin-bottom: 0.15rem;
        word-break: break-word;
        line-height: 1.3;
    }

    .coa-m-balance-box {
        background: #f8fafc;
        border: 1px solid var(--coa-border-light);
        border-radius: 10px;
        padding: 0.7rem 0.85rem;
        margin: 0.75rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .coa-m-balance-amount {
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .coa-m-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .btn-coa-m-action {
        height: 38px;
        border-radius: var(--coa-radius-sm);
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: 1px solid var(--coa-border);
        background: #ffffff;
        color: #334155;
        text-decoration: none !important;
        transition: all 0.15s ease;
        width: 100%;
        min-width: 0 !important;
        box-sizing: border-box !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0 4px;
    }

    .btn-coa-m-action:active {
        transform: scale(0.98);
    }

    .btn-coa-m-action.btn-m-ledger {
        background: var(--coa-primary-soft);
        border-color: rgba(79, 70, 229, 0.2);
        color: var(--coa-primary);
    }

    .btn-coa-m-action.btn-m-edit {
        background: #f8fafc;
        border-color: var(--coa-border);
        color: #1e293b;
    }

    /* Modals VIP Styling */
    .modal-content.vip-modal {
        border: 1px solid var(--coa-border);
        border-radius: var(--coa-radius-lg);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .vip-modal-header {
        background: #ffffff;
        color: var(--coa-text-main);
        padding: 1.15rem 1.4rem;
        border-bottom: 1.5px solid var(--coa-border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vip-modal-header .close {
        color: #64748b;
        opacity: 0.8;
        padding: 0;
        margin: 0;
        font-size: 1.4rem;
    }

    .vip-modal-body {
        padding: 1.25rem 1.4rem;
        background: #ffffff;
    }

    .vip-modal-footer {
        background: #f8fafc;
        border-top: 1px solid var(--coa-border);
        padding: 0.85rem 1.4rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .vip-form-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 0.35rem;
    }

    .vip-form-control {
        border-radius: var(--coa-radius-sm);
        border: 1.5px solid var(--coa-border);
        font-size: 0.88rem;
        padding: 0.55rem 0.8rem;
        color: var(--coa-text-main);
    }

    .vip-form-control:focus {
        border-color: var(--coa-primary);
        box-shadow: 0 0 0 3px var(--coa-primary-soft);
        outline: none;
    }

    /* DataTables VIP Overrides */
    .dataTables_wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .dataTables_wrapper .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        align-items: center;
        padding: 0.75rem 1rem;
    }

    .dataTables_wrapper .row > [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .dataTables_filter, .dataTables_length {
        display: none !important;
    }

    .dataTables_info {
        font-size: 0.78rem !important;
        color: var(--coa-text-muted) !important;
    }

    .dataTables_paginate {
        margin-top: 0 !important;
        text-align: right !important;
    }

    .dataTables_paginate .pagination {
        margin: 0 !important;
        justify-content: flex-end !important;
    }

    .dataTables_paginate .page-link {
        border-radius: 6px !important;
        margin: 0 2px !important;
        border: 1px solid var(--coa-border) !important;
        font-size: 0.78rem !important;
        color: #475569 !important;
        padding: 5px 10px !important;
    }

    .dataTables_paginate .page-item.active .page-link {
        background-color: var(--coa-primary) !important;
        border-color: var(--coa-primary) !important;
        color: #ffffff !important;
    }

    /* Timeline in history modal */
    .audit-timeline-item {
        border-left: 2px solid #e2e8f0;
        padding-left: 1rem;
        position: relative;
        padding-bottom: 1.15rem;
    }

    .audit-timeline-item:last-child {
        padding-bottom: 0;
        border-left-color: transparent;
    }

    .audit-timeline-item::before {
        content: '';
        position: absolute;
        left: -5px;
        top: 2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--coa-primary);
    }
</style>

@php
    $totalAccounts = $accounts->count();
    $activeCount = $accounts->where('status', 1)->count();
    $inactiveCount = $accounts->where('status', 0)->count();
    $debitCount = $accounts->where('type', 'Debit')->count();
    $creditCount = $accounts->where('type', 'Credit')->count();
@endphp

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid coa-page-wrapper">

            {{-- ═════════════════════════════════════════════════════════════════
                 1. PROFESSIONAL LIGHT EXECUTIVE HEADER
            ═════════════════════════════════════════════════════════════════ --}}
            <div class="coa-pro-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    {{-- Header Left Branding & Titles --}}
                    <div class="d-flex align-items-center gap-2 gap-sm-3">
                        <div class="coa-header-icon-box">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h1 class="coa-header-title">Chart Of Accounts</h1>
                                <span class="coa-count-chip">{{ $totalAccounts }} Accounts</span>
                            </div>
                            <p class="coa-header-subtitle">Manage Cash, Bank &amp; Ledger Accounts Liquidity</p>
                        </div>
                    </div>

                    {{-- Header Right Action Buttons (Laptop: Single Clean Line / Mobile: Balanced Grid) --}}
                    @can('chart.of.accounts.create')
                        <div class="coa-header-actions">
                            <button type="button" class="btn-coa-primary" data-toggle="modal" data-target="#addAccountModal">
                                <i class="fas fa-plus"></i> Add Account
                            </button>
                            <div class="coa-btn-row">
                                <button type="button" class="btn-coa-outline" data-toggle="modal" data-target="#addHeadModal">
                                    <i class="fas fa-folder-plus text-primary"></i> Add Category
                                </button>
                                <button type="button" class="btn-coa-outline" data-toggle="modal" data-target="#manageHeadsModal">
                                    <i class="fas fa-layer-group text-secondary"></i> Categories ({{ $heads->count() }})
                                </button>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center justify-content-between rounded-3 mb-3 shadow-sm border-0" style="background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle fs-5 text-success"></i>
                        <span class="fw-semibold">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="close text-secondary" data-dismiss="alert" aria-label="Close" style="opacity: 0.6;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center justify-content-between rounded-3 mb-3 shadow-sm border-0" style="background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle fs-5 text-danger"></i>
                        <span class="fw-semibold">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="close text-secondary" data-dismiss="alert" aria-label="Close" style="opacity: 0.6;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-3 shadow-sm border-0" style="background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-times-circle fs-5 text-danger"></i>
                        <strong class="fw-bold">Validation Errors:</strong>
                    </div>
                    <ul class="mb-0 ps-4 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ═════════════════════════════════════════════════════════════════
                 2. UNIFIED FILTER & INSTANT SEARCH CENTER
            ═════════════════════════════════════════════════════════════════ --}}
            <div class="coa-filter-card">
                <div class="row align-items-center g-2">
                    {{-- Search Input --}}
                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                        <div class="coa-search-wrap">
                            <i class="fas fa-search coa-search-icon"></i>
                            <input type="text" id="unifiedAccountSearch" class="coa-search-input" placeholder="Search account title, code, category...">
                            <button type="button" id="clearSearchBtn" class="coa-search-clear">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Category Select Dropdown --}}
                    <div class="col-12 col-md-3 mb-2 mb-md-0">
                        <select id="categoryFilterSelect" class="form-control vip-form-control" style="height: 42px; font-weight: 600;">
                            <option value="">All Categories ({{ $heads->count() }})</option>
                            @foreach ($heads as $head)
                                <option value="{{ strtolower($head->name) }}">{{ $head->name }} ({{ $head->accounts_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Quick Filter Pills --}}
                    <div class="col-12 col-md-4">
                        <div class="coa-pills-strip justify-content-md-end">
                            <button type="button" class="coa-filter-pill active" data-filter="all">
                                All <span class="pill-count">{{ $totalAccounts }}</span>
                            </button>
                            <button type="button" class="coa-filter-pill" data-filter="debit">
                                Debit <span class="pill-count">{{ $debitCount }}</span>
                            </button>
                            <button type="button" class="coa-filter-pill" data-filter="credit">
                                Credit <span class="pill-count">{{ $creditCount }}</span>
                            </button>
                            <button type="button" class="coa-filter-pill" data-filter="active">
                                Active <span class="pill-count">{{ $activeCount }}</span>
                            </button>
                            <button type="button" class="coa-filter-pill" data-filter="inactive">
                                Inactive <span class="pill-count">{{ $inactiveCount }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═════════════════════════════════════════════════════════════════
                 3. DESKTOP ENTERPRISE TABLE (SCREEN >= 992px)
            ═════════════════════════════════════════════════════════════════ --}}
            <div class="coa-table-card d-none d-lg-block">
                <div class="coa-table-wrapper">
                    <table class="table coa-table datanew" id="desktopAccountsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th style="width: 11%;">Code</th>
                                <th style="width: 25%;">Account &amp; Category</th>
                                <th style="width: 10%; text-align: center;">Type</th>
                                <th style="width: 16%; text-align: right;">Opening Balance</th>
                                <th style="width: 17%; text-align: right;">Current Ledger</th>
                                <th style="width: 8%; text-align: center;">Status</th>
                                <th style="width: 15%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accounts as $acc)
                                <tr data-type="{{ strtolower($acc->type) }}" 
                                    data-status="{{ $acc->status ? 'active' : 'inactive' }}"
                                    data-head="{{ strtolower($acc->head->name ?? '') }}">
                                    
                                    {{-- Index --}}
                                    <td class="text-center text-muted fw-bold font-monospace" style="font-size: 0.8rem;">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </td>

                                    {{-- Account Code --}}
                                    <td>
                                        <span class="coa-code-chip">{{ $acc->account_code ?? 'N/A' }}</span>
                                    </td>

                                    {{-- Account Title & Category --}}
                                    <td>
                                        <div class="coa-acc-title">{{ $acc->title }}</div>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="coa-category-chip">
                                                <i class="fas fa-folder text-indigo" style="font-size: 0.68rem; color: #4f46e5;"></i>
                                                {{ $acc->head->name ?? 'Uncategorized' }}
                                            </span>
                                            @if ($acc->head && $acc->head->parent_id)
                                                <small class="text-muted" style="font-size: 0.72rem;">&rsaquo; {{ $acc->head->parent->name ?? '' }}</small>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Type --}}
                                    <td class="text-center">
                                        @if ($acc->type == 'Debit')
                                            <span class="badge-coa-debit">
                                                <i class="fas fa-plus-circle me-1" style="font-size: 0.65rem;"></i> Debit
                                            </span>
                                        @else
                                            <span class="badge-coa-credit">
                                                <i class="fas fa-minus-circle me-1" style="font-size: 0.65rem;"></i> Credit
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Opening Balance --}}
                                    <td class="text-end font-monospace">
                                        <span class="text-muted fw-semibold">Rs {{ number_format($acc->opening_balance, 2) }}</span>
                                    </td>

                                    {{-- Current Balance --}}
                                    <td class="text-end font-monospace">
                                        <div class="fw-bold fs-6 {{ $acc->current_balance < 0 ? 'text-danger' : 'text-success' }}">
                                            Rs {{ number_format(abs($acc->current_balance), 2) }}
                                            <span class="badge bg-light border ms-1" style="font-size: 0.68rem; color: #475569;">
                                                {{ $acc->current_balance >= 0 ? 'Dr' : 'Cr' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @if ($acc->status)
                                            <span class="coa-status-pill coa-status-active">
                                                <span class="coa-dot"></span> Active
                                            </span>
                                        @else
                                            <span class="coa-status-pill coa-status-inactive">
                                                <span class="coa-dot"></span> Inactive
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-center">
                                        <div class="coa-action-group justify-content-center">
                                            {{-- Ledger --}}
                                            <a href="{{ route('accounts.ledger', $acc->id) }}" class="btn-coa-action btn-ledger" title="View Detailed Ledger" data-toggle="tooltip">
                                                <i class="fas fa-book-open"></i>
                                            </a>

                                            {{-- Edit --}}
                                            <button type="button" class="btn-coa-action btn-edit" data-toggle="modal" data-target="#editAccountModal{{ $acc->id }}" title="Edit Balance &amp; Details" data-toggle="tooltip">
                                                <i class="fas fa-pen-to-square"></i>
                                            </button>

                                            {{-- History --}}
                                            <button type="button" class="btn-coa-action btn-history" data-toggle="modal" data-target="#historyAccountModal{{ $acc->id }}" title="Audit Logs ({{ $acc->histories->count() }})" data-toggle="tooltip">
                                                <i class="fas fa-clock-rotate-left"></i>
                                            </button>

                                            {{-- Toggle Status --}}
                                            <form action="{{ route('accounts.toggleStatus', $acc->id) }}" method="POST" class="d-inline-block m-0">
                                                @csrf
                                                <button type="button" onclick="this.closest('form').submit()" 
                                                        class="btn-coa-action {{ $acc->status ? 'btn-danger-action' : 'btn-success-action' }}" 
                                                        title="{{ $acc->status ? 'Deactivate Account' : 'Activate Account' }}" 
                                                        data-toggle="tooltip">
                                                    <i class="fas {{ $acc->status ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No Accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═════════════════════════════════════════════════════════════════
                 4. MOBILE VIP ADJUSTMENT (< 992px)
                 - 100% Strict Zero Horizontal Scroll
                 - Clean Banking Card Interface
            ═════════════════════════════════════════════════════════════════ --}}
            <div class="d-block d-lg-none">
                <div class="coa-mobile-grid" id="mobileCardsContainer">
                    @forelse ($accounts as $acc)
                        <div class="coa-m-card {{ $acc->type == 'Debit' ? 'is-debit' : 'is-credit' }} account-m-item"
                             data-title="{{ strtolower($acc->title) }}"
                             data-code="{{ strtolower($acc->account_code ?? '') }}"
                             data-head="{{ strtolower($acc->head->name ?? '') }}"
                             data-type="{{ strtolower($acc->type) }}"
                             data-status="{{ $acc->status ? 'active' : 'inactive' }}"
                             data-balance="{{ $acc->current_balance }}">
                            
                            {{-- Header: Code + Category + Status --}}
                            <div class="coa-m-card-header">
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="coa-code-chip">{{ $acc->account_code ?? 'N/A' }}</span>
                                    <span class="coa-category-chip">
                                        <i class="fas fa-folder text-indigo" style="font-size: 0.65rem; color: #4f46e5;"></i>
                                        {{ $acc->head->name ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    @if ($acc->status)
                                        <span class="coa-status-pill coa-status-active" style="padding: 2px 7px; font-size: 0.7rem;">
                                            <span class="coa-dot"></span> Active
                                        </span>
                                    @else
                                        <span class="coa-status-pill coa-status-inactive" style="padding: 2px 7px; font-size: 0.7rem;">
                                            <span class="coa-dot"></span> Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Title & Type --}}
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                <div class="coa-m-card-title">{{ $acc->title }}</div>
                                @if ($acc->type == 'Debit')
                                    <span class="badge-coa-debit" style="font-size: 0.68rem; padding: 2px 7px;">Debit</span>
                                @else
                                    <span class="badge-coa-credit" style="font-size: 0.68rem; padding: 2px 7px;">Credit</span>
                                @endif
                            </div>

                            {{-- Financial Balance Box --}}
                            <div class="coa-m-balance-box">
                                <div>
                                    <div class="text-muted" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Current Balance</div>
                                    <div class="coa-m-balance-amount {{ $acc->current_balance < 0 ? 'text-danger' : 'text-success' }}">
                                        Rs {{ number_format(abs($acc->current_balance), 2) }}
                                        <small class="text-secondary fw-normal ms-1" style="font-size: 0.72rem;">{{ $acc->current_balance >= 0 ? 'Dr' : 'Cr' }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Opening</div>
                                    <div class="font-monospace fw-semibold text-muted" style="font-size: 0.82rem;">
                                        Rs {{ number_format($acc->opening_balance, 2) }}
                                    </div>
                                </div>
                            </div>

                            {{-- 2x2 Balanced Action Grid (Full Touch-Friendly) --}}
                            <div class="coa-m-actions-grid">
                                <a href="{{ route('accounts.ledger', $acc->id) }}" class="btn-coa-m-action btn-m-ledger">
                                    <i class="fas fa-book-open"></i> Ledger
                                </a>

                                <button type="button" class="btn-coa-m-action btn-m-edit" data-toggle="modal" data-target="#editAccountModal{{ $acc->id }}">
                                    <i class="fas fa-pen-to-square"></i> Edit
                                </button>

                                <button type="button" class="btn-coa-m-action" data-toggle="modal" data-target="#historyAccountModal{{ $acc->id }}">
                                    <i class="fas fa-clock-rotate-left"></i> Logs ({{ $acc->histories->count() }})
                                </button>

                                <form action="{{ route('accounts.toggleStatus', $acc->id) }}" method="POST" class="m-0 p-0 w-100">
                                    @csrf
                                    <button type="button" onclick="this.closest('form').submit()" 
                                            class="btn-coa-m-action {{ $acc->status ? 'text-danger' : 'text-success' }}">
                                        <i class="fas {{ $acc->status ? 'fa-ban text-danger' : 'fa-check-circle text-success' }}"></i>
                                        {{ $acc->status ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="p-4 text-center text-muted bg-white rounded-3 border">
                            <i class="fas fa-folder-open fa-2x mb-2" style="color: #cbd5e1;"></i>
                            <p class="mb-0 fw-semibold">No Accounts Found</p>
                        </div>
                    @endforelse

                    {{-- Empty Search Notice --}}
                    <div id="noMobileResultsNotice" class="p-4 text-center text-muted bg-white rounded-3 border d-none">
                        <i class="fas fa-magnifying-glass fa-2x mb-2" style="color: #cbd5e1;"></i>
                        <h6 class="fw-bold text-dark mb-1">No matching accounts</h6>
                        <p class="text-muted small mb-0">Try clearing filters or search keyword.</p>
                    </div>
                </div>
            </div>

            {{-- ═════════════════════════════════════════════════════════════════
                 5. ALL MODALS (VIP CLEAN REDESIGN)
            ═════════════════════════════════════════════════════════════════ --}}

            {{-- ADD ACCOUNT MODAL --}}
            <div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form class="modal-content vip-modal" action="{{ route('accounts.store') }}" method="POST">
                        @csrf
                        <div class="vip-modal-header">
                            <h5 class="modal-title fw-bold mb-0" id="addAccountModalLabel">
                                <i class="fas fa-plus-circle text-primary me-2"></i>Create Financial Account
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="vip-modal-body">
                            <div class="form-group mb-3">
                                <label class="vip-form-label">Category / Head <span class="text-danger">*</span></label>
                                <select class="form-control vip-form-control" name="head_id" required style="height: 42px;">
                                    <option value="">-- Choose Head Category --</option>
                                    @foreach ($heads as $head)
                                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="vip-form-label">Account Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control vip-form-control" placeholder="e.g. Meezan Bank 0102 or Cash Drawer 1" required>
                            </div>

                            <div class="row g-2">
                                <div class="col-12 col-sm-6 mb-3">
                                    <label class="vip-form-label">Account Type</label>
                                    <select class="form-control vip-form-control" name="type" style="height: 42px;">
                                        <option value="Debit">Debit (Asset / Expense)</option>
                                        <option value="Credit">Credit (Liability / Revenue)</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <label class="vip-form-label">Opening Balance (Rs)</label>
                                    <input type="number" step="0.01" name="opening_balance" class="form-control vip-form-control" value="0.00" required>
                                </div>
                            </div>

                            <div class="form-group mb-0 pt-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="statusCheckNew" name="status" checked>
                                    <label class="custom-control-label fw-semibold text-dark small" for="statusCheckNew">
                                        Mark account as active immediately
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="vip-modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn-coa-primary">
                                <i class="fas fa-check-circle"></i> Save Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ADD CATEGORY / HEAD MODAL --}}
            <div class="modal fade" id="addHeadModal" tabindex="-1" role="dialog" aria-labelledby="addHeadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form class="modal-content vip-modal" action="{{ route('account-heads.store') }}" method="POST">
                        @csrf
                        <div class="vip-modal-header">
                            <h5 class="modal-title fw-bold mb-0" id="addHeadModalLabel">
                                <i class="fas fa-folder-plus text-primary me-2"></i>Add Account Category
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="vip-modal-body">
                            <div class="alert alert-info py-2 px-3 small border-0 rounded-3 mb-3" style="background: #eff6ff; color: #1e40af;">
                                <i class="fas fa-info-circle me-1"></i> Categories represent top-level financial groups (e.g. <strong>Cash</strong>, <strong>Bank Accounts</strong>, <strong>Mobile Wallets</strong>).
                            </div>
                            <div class="form-group mb-0">
                                <label class="vip-form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control vip-form-control" placeholder="e.g. Digital Wallets, Bank Branches" required>
                            </div>
                        </div>
                        <div class="vip-modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn-coa-primary">
                                <i class="fas fa-save"></i> Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MANAGE CATEGORIES MODAL --}}
            <div class="modal fade" id="manageHeadsModal" tabindex="-1" role="dialog" aria-labelledby="manageHeadsLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content vip-modal">
                        <div class="vip-modal-header">
                            <h5 class="modal-title fw-bold mb-0" id="manageHeadsLabel">
                                <i class="fas fa-layer-group text-primary me-2"></i>Manage Categories ({{ $heads->count() }})
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="vip-modal-body p-0">
                            <div class="table-responsive m-0">
                                <table class="table coa-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 8%; text-align: center;">#</th>
                                            <th style="width: 48%;">Category Name</th>
                                            <th style="width: 24%; text-align: center;">Linked Accounts</th>
                                            <th style="width: 20%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($heads as $h)
                                            <tr>
                                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                                <td><span class="fw-bold text-dark">{{ $h->name }}</span></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 0.72rem;">
                                                        {{ $h->accounts_count }} Accounts
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                                        <button type="button" class="btn-coa-action btn-edit" data-toggle="modal" data-target="#editHeadModal{{ $h->id }}" data-dismiss="modal" title="Edit Category">
                                                            <i class="fas fa-pen-to-square"></i>
                                                        </button>
                                                        
                                                        @if ($h->accounts_count == 0)
                                                            <form action="{{ route('account-heads.delete', $h->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Category?');" class="d-inline-block m-0">
                                                                @csrf
                                                                <button type="submit" class="btn-coa-action btn-danger-action" title="Delete Unused Category">
                                                                    <i class="fas fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="text-muted small" title="Cannot delete: Accounts are linked to this head" style="font-size: 0.72rem;">In Use</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">No Categories configured.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="vip-modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" style="border-radius: 8px;">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EDIT HEAD MODALS --}}
            @foreach ($heads as $h)
                <div class="modal fade" id="editHeadModal{{ $h->id }}" tabindex="-1" role="dialog" aria-labelledby="editHeadLabel{{ $h->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form class="modal-content vip-modal" action="{{ route('account-heads.update', $h->id) }}" method="POST">
                            @csrf
                            <div class="vip-modal-header">
                                <h5 class="modal-title fw-bold mb-0" id="editHeadLabel{{ $h->id }}">
                                    <i class="fas fa-pen-to-square text-primary me-2"></i>Edit Category: {{ $h->name }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="vip-modal-body">
                                <div class="form-group mb-0">
                                    <label class="vip-form-label">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control vip-form-control" value="{{ $h->name }}" required>
                                </div>
                            </div>
                            <div class="vip-modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                <button type="submit" class="btn-coa-primary">
                                    <i class="fas fa-save"></i> Update Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- EDIT ACCOUNT & AUDIT HISTORY MODALS --}}
            @foreach ($accounts as $acc)
                {{-- EDIT ACCOUNT MODAL --}}
                <div class="modal fade" id="editAccountModal{{ $acc->id }}" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel{{ $acc->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form class="modal-content vip-modal" action="{{ route('accounts.update', $acc->id) }}" method="POST">
                            @csrf
                            <div class="vip-modal-header">
                                <div>
                                    <h5 class="modal-title fw-bold mb-0" id="editAccountModalLabel{{ $acc->id }}">
                                        <i class="fas fa-pen-to-square text-primary me-2"></i>Edit Account &amp; Ledger
                                    </h5>
                                    <small class="text-muted">{{ $acc->title }} ({{ $acc->account_code ?? 'N/A' }})</small>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="vip-modal-body">
                                <div class="alert alert-info py-2 px-3 small border-0 rounded-3 mb-3" style="background: #eff6ff; color: #1e40af;">
                                    <i class="fas fa-info-circle me-1"></i> You can adjust the <strong>Current Ledger Balance</strong> directly. Every balance edit is logged with an audit trail.
                                </div>

                                <div class="form-group mb-3">
                                    <label class="vip-form-label">Category / Head <span class="text-danger">*</span></label>
                                    <select class="form-control vip-form-control" name="head_id" required style="height: 42px;">
                                        @foreach ($heads as $head)
                                            <option value="{{ $head->id }}" {{ $acc->head_id == $head->id ? 'selected' : '' }}>{{ $head->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="vip-form-label">Account Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control vip-form-control" value="{{ $acc->title }}" required>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                        <label class="vip-form-label">Type</label>
                                        <select class="form-control vip-form-control" name="type" style="height: 42px;">
                                            <option value="Debit" {{ $acc->type == 'Debit' ? 'selected' : '' }}>Debit</option>
                                            <option value="Credit" {{ $acc->type == 'Credit' ? 'selected' : '' }}>Credit</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="vip-form-label">Opening (Rs)</label>
                                        <input type="number" step="0.01" name="opening_balance" class="form-control vip-form-control" value="{{ $acc->opening_balance }}" required>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <label class="vip-form-label text-success">Current (Rs)</label>
                                        <input type="number" step="0.01" name="current_balance" class="form-control vip-form-control fw-bold text-success" value="{{ $acc->current_balance }}" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="vip-form-label">Reason / Audit Note (Optional)</label>
                                    <input type="text" name="note" class="form-control vip-form-control" placeholder="e.g. Periodic reconciliation adjustment">
                                </div>

                                <div class="form-group mb-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="statusCheck{{ $acc->id }}" name="status" {{ $acc->status ? 'checked' : '' }}>
                                        <label class="custom-control-label fw-semibold text-dark small" for="statusCheck{{ $acc->id }}">
                                            Active Account
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="vip-modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                <button type="submit" class="btn-coa-primary">
                                    <i class="fas fa-check-circle"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- AUDIT HISTORY MODAL --}}
                <div class="modal fade" id="historyAccountModal{{ $acc->id }}" tabindex="-1" role="dialog" aria-labelledby="historyAccountModalLabel{{ $acc->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content vip-modal">
                            <div class="vip-modal-header">
                                <div>
                                    <h5 class="modal-title fw-bold mb-0" id="historyAccountModalLabel{{ $acc->id }}">
                                        <i class="fas fa-clock-rotate-left text-primary me-2"></i>Audit Trail &amp; Edit History
                                    </h5>
                                    <small class="text-muted">{{ $acc->title }} ({{ $acc->account_code ?? 'N/A' }})</small>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="vip-modal-body p-3 p-md-4" style="max-height: 65vh; overflow-y: auto;">
                                @if($acc->histories->count() > 0)
                                    <div>
                                        @foreach($acc->histories->sortByDesc('id') as $h)
                                            <div class="audit-timeline-item">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1">
                                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">
                                                        <i class="far fa-clock me-1"></i> {{ $h->created_at->format('d M, Y h:i:s A') }}
                                                    </span>
                                                    <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.72rem;">
                                                        <i class="fas fa-user-circle me-1"></i> {{ $h->user_name ?? ($h->user->name ?? 'User') }}
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 my-2">
                                                    <span class="text-muted small">Previous: <strong>Rs {{ number_format($h->old_balance, 2) }}</strong></span>
                                                    <i class="fas fa-arrow-right text-muted" style="font-size: 0.75rem;"></i>
                                                    <span class="text-primary small fw-bold">Updated: <strong>Rs {{ number_format($h->new_balance, 2) }}</strong></span>
                                                </div>
                                                @if($h->note)
                                                    <div class="p-2 rounded bg-light border text-secondary small" style="font-size: 0.78rem;">
                                                        <i class="fas fa-comment-dots text-muted me-1"></i> {{ $h->note }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-history fa-2x mb-2" style="color: #cbd5e1;"></i>
                                        <p class="mb-0 small fw-semibold">No Balance Edits Recorded</p>
                                    </div>
                                @endif
                            </div>
                            <div class="vip-modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" style="border-radius: 8px;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }

        var table = null;
        if ($.fn.DataTable.isDataTable('#desktopAccountsTable')) {
            $('#desktopAccountsTable').DataTable().destroy();
        }

        if ($('#desktopAccountsTable').length) {
            table = $('#desktopAccountsTable').DataTable({
                "pageLength": 15,
                "responsive": false,
                "autoWidth": false,
                "aaSorting": [],
                "language": {
                    "emptyTable": "No matching accounts found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ accounts",
                    "paginate": {
                        "previous": '<i class="fas fa-chevron-left"></i>',
                        "next": '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });
        }

        var currentSearchTerm = '';
        var currentFilter = 'all';
        var currentCategory = '';

        function applyUnifiedFilters() {
            var search = currentSearchTerm.toLowerCase().trim();

            if (table) {
                table.search(search).draw();
            }

            var visibleMobileCount = 0;
            $('.account-m-item').each(function() {
                var $card = $(this);
                var title = ($card.data('title') || '').toString();
                var code = ($card.data('code') || '').toString();
                var head = ($card.data('head') || '').toString();
                var type = ($card.data('type') || '').toString();
                var status = ($card.data('status') || '').toString();

                var matchesSearch = true;
                if (search.length > 0) {
                    matchesSearch = title.includes(search) || code.includes(search) || head.includes(search);
                }

                var matchesFilter = true;
                if (currentFilter === 'debit') {
                    matchesFilter = (type === 'debit');
                } else if (currentFilter === 'credit') {
                    matchesFilter = (type === 'credit');
                } else if (currentFilter === 'active') {
                    matchesFilter = (status === 'active');
                } else if (currentFilter === 'inactive') {
                    matchesFilter = (status === 'inactive');
                }

                var matchesCategory = true;
                if (currentCategory.length > 0) {
                    matchesCategory = (head === currentCategory);
                }

                if (matchesSearch && matchesFilter && matchesCategory) {
                    $card.removeClass('d-none');
                    visibleMobileCount++;
                } else {
                    $card.addClass('d-none');
                }
            });

            if (visibleMobileCount === 0 && $('.account-m-item').length > 0) {
                $('#noMobileResultsNotice').removeClass('d-none');
            } else {
                $('#noMobileResultsNotice').addClass('d-none');
            }
        }

        $('#unifiedAccountSearch').on('input keyup', function() {
            currentSearchTerm = $(this).val();
            if (currentSearchTerm.length > 0) {
                $('#clearSearchBtn').show();
            } else {
                $('#clearSearchBtn').hide();
            }
            applyUnifiedFilters();
        });

        $('#clearSearchBtn').on('click', function() {
            $('#unifiedAccountSearch').val('');
            currentSearchTerm = '';
            $(this).hide();
            applyUnifiedFilters();
        });

        $('.coa-filter-pill').on('click', function() {
            $('.coa-filter-pill').removeClass('active');
            $(this).addClass('active');

            currentFilter = $(this).data('filter');

            if (table) {
                $.fn.dataTable.ext.search.pop();
                if (currentFilter !== 'all' || currentCategory !== '') {
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            var rowNode = table.row(dataIndex).node();
                            var rowType = $(rowNode).data('type');
                            var rowStatus = $(rowNode).data('status');
                            var rowHead = $(rowNode).data('head');

                            var pillMatch = true;
                            if (currentFilter === 'debit') pillMatch = (rowType === 'debit');
                            else if (currentFilter === 'credit') pillMatch = (rowType === 'credit');
                            else if (currentFilter === 'active') pillMatch = (rowStatus === 'active');
                            else if (currentFilter === 'inactive') pillMatch = (rowStatus === 'inactive');

                            var catMatch = true;
                            if (currentCategory !== '') {
                                catMatch = (rowHead === currentCategory);
                            }

                            return pillMatch && catMatch;
                        }
                    );
                }
                table.draw();
            }

            applyUnifiedFilters();
        });

        $('#categoryFilterSelect').on('change', function() {
            currentCategory = $(this).val();

            if (table) {
                table.draw();
            }

            applyUnifiedFilters();
        });
    });
</script>
@endsection
