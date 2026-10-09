<!-- =========================================================================
     OFF-CANVAS MOBILE NAVIGATION SIDEBAR DRAWER (VIP FINTECH EDITION)
     - 100% Mobile Friendly Slide-In Drawer
     - Accordion-Based Clean Navigation
     - Instant Search & Active State Highlighting
     ========================================================================= -->

<div id="mobileNavBackdrop" class="mobile-nav-backdrop"></div>

<aside id="mobileOffCanvasSidebar" class="mobile-offcanvas-drawer">
    {{-- Drawer Header --}}
    <div class="m-drawer-header">
        @php
            $dynCompName = \App\Models\Setting::get('company_name', 'TrendHub');
            $init1 = strtoupper(substr($dynCompName, 0, 1));
            $init2 = strlen($dynCompName) > 1 ? strtoupper(substr($dynCompName, 1, 1)) : '';
        @endphp
        <div class="d-flex align-items-center gap-2">
            <div class="header-pw-badge" style="width: 34px; height: 34px; font-size: 15px; margin-right: 8px;">
                <span style="color: #ffffff;">{{ $init1 }}</span><span style="color: #a1a1aa;">{{ $init2 }}</span>
            </div>
            <div>
                <h6 class="mb-0 text-white font-weight-bold" style="font-size: 14px; line-height: 1.2;">{{ $dynCompName }}</h6>
                <small class="text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Navigation Menu</small>
            </div>
        </div>
        <button type="button" id="closeMobileDrawerBtn" class="m-drawer-close-btn" aria-label="Close Menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- User Profile Card --}}
    @if(auth()->check())
        <div class="m-drawer-user-card">
            <div class="d-flex align-items-center gap-2">
                <div class="m-user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="text-white font-weight-bold text-truncate" style="font-size: 13px;">{{ Auth::user()->name }}</div>
                    <div class="text-muted text-truncate" style="font-size: 11px;">
                        <span class="badge badge-primary py-0 px-1 font-weight-normal" style="font-size: 9px;">Active</span>
                        {{ Auth::user()->email ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Drawer Search Filter --}}
    <div class="m-drawer-search-wrap">
        <div class="position-relative">
            <i class="fas fa-search m-drawer-search-icon"></i>
            <input type="text" id="mDrawerMenuSearch" class="m-drawer-search-input" placeholder="Quick find page...">
        </div>
    </div>

    {{-- Navigation Scrollable List --}}
    <div class="m-drawer-body">
        <ul class="m-drawer-nav">
            @if(!empty($is_restaurant_branch))
                {{-- Restaurant POS Navigation (Mobile) --}}
                <li class="m-nav-item">
                    <a href="{{ route('pos.index') }}" class="m-nav-link {{ request()->is('pos*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register m-nav-icon text-primary"></i>
                        <span class="m-nav-text font-weight-bold">POS Counter</span>
                    </a>
                </li>
                <li class="m-nav-item">
                    <a href="{{ url('product-catalog') }}" class="m-nav-link {{ request()->is('product*') ? 'active' : '' }}">
                        <i class="fas fa-utensils m-nav-icon text-success"></i>
                        <span class="m-nav-text">Products / Menu</span>
                    </a>
                </li>
                <li class="m-nav-item">
                    <a href="{{ url('sale') }}" class="m-nav-link {{ request()->is('sale*') ? 'active' : '' }}">
                        <i class="fas fa-receipt m-nav-icon text-warning"></i>
                        <span class="m-nav-text">Sales / Orders</span>
                    </a>
                </li>
                <li class="m-nav-item">
                    <a href="{{ route('Category.home') }}" class="m-nav-link {{ request()->is('category*') ? 'active' : '' }}">
                        <i class="fas fa-tags m-nav-icon text-info"></i>
                        <span class="m-nav-text">Categories</span>
                    </a>
                </li>
                <li class="m-nav-item">
                    <a href="{{ route('all_expense_vochers') }}" class="m-nav-link {{ request()->is('*expense*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar m-nav-icon text-danger"></i>
                        <span class="m-nav-text">Expenses</span>
                    </a>
                </li>
                <li class="m-nav-item">
                    <a href="{{ route('view_all') }}" class="m-nav-link {{ request()->is('view_all*') || request()->is('accounts*') ? 'active' : '' }}">
                        <i class="fas fa-university m-nav-icon text-primary"></i>
                        <span class="m-nav-text">Accounts</span>
                    </a>
                </li>
                <li class="m-nav-item">
                    <a href="{{ route('settings.index') }}" class="m-nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h m-nav-icon text-secondary"></i>
                        <span class="m-nav-text">POS Settings</span>
                    </a>
                </li>
            @else
            {{-- Dashboard --}}
            @canany(['dashboard.view', 'home.view'])
                <li class="m-nav-item">
                    <a href="{{ url('/home') }}" class="m-nav-link {{ request()->is('home') ? 'active' : '' }}">
                        <i class="fas fa-home m-nav-icon"></i>
                        <span class="m-nav-text">Dashboard</span>
                    </a>
                </li>
            @endcanany

            {{-- Products & Categories --}}
            @canany(['products.view', 'discount.products.view', 'categories.view', 'subcategories.view', 'brands.view', 'units.view'])
                <li class="m-nav-item m-nav-group">
                    <div class="m-nav-group-header" data-target="#mSubProducts">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-boxes m-nav-icon"></i>
                            <span class="m-nav-text">Products &amp; Catalog</span>
                        </div>
                        <i class="fas fa-chevron-right m-chevron"></i>
                    </div>
                    <ul class="m-submenu collapse" id="mSubProducts">
                        @can('products.view')
                            <li><a href="{{ url('product-catalog') }}" class="{{ request()->is('product-catalog*') ? 'active' : '' }}"><i class="fas fa-box"></i> Products Catalog</a></li>
                        @endcan
                        @can('discount.products.view')
                            <li><a href="{{ route('discount.index') }}"><i class="fas fa-tags"></i> Discount Products</a></li>
                        @endcan
                        @can('categories.view')
                            <li><a href="{{ route('Category.home') }}"><i class="fas fa-list"></i> Categories</a></li>
                        @endcan
                        @can('subcategories.view')
                            <li><a href="{{ route('subcategory.home') }}"><i class="fas fa-th-list"></i> Sub Categories</a></li>
                        @endcan
                        @can('brands.view')
                            <li><a href="{{ route('Brand.home') }}"><i class="fas fa-trademark"></i> Brands</a></li>
                        @endcan
                        @can('units.view')
                            <li><a href="{{ route('Unit.home') }}"><i class="fas fa-balance-scale"></i> Units</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            {{-- Purchase & Inventory --}}
            @canany(['vendors.view', 'purchases.view', 'purchase_pos.create', 'warehouse.view', 'warehouse.stock.view', 'stock.transfer.view', 'stock.adjust.view'])
                <li class="m-nav-item m-nav-group">
                    <div class="m-nav-group-header" data-target="#mSubPurchases">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-truck-ramp-box m-nav-icon"></i>
                            <span class="m-nav-text">Purchases &amp; Inventory</span>
                        </div>
                        <i class="fas fa-chevron-right m-chevron"></i>
                    </div>
                    <ul class="m-submenu collapse" id="mSubPurchases">
                        @can('vendors.view')
                            <li><a href="{{ url('vendor') }}"><i class="fas fa-truck"></i> Vendors</a></li>
                        @endcan
                        @can('purchases.view')
                            <li><a href="{{ route('Purchase.home') }}"><i class="fas fa-shopping-cart"></i> Purchases</a></li>
                        @endcan
                        @can('purchase_pos.create')
                            <li><a href="{{ route('purchase-pos.index') }}"><i class="fas fa-cash-register"></i> Purchase POS</a></li>
                        @endcan
                        @can('warehouse.view')
                            <li><a href="{{ url('warehouse') }}"><i class="fas fa-warehouse"></i> Warehouse</a></li>
                        @endcan
                        @can('warehouse.stock.view')
                            <li><a href="{{ url('warehouse_stocks') }}"><i class="fas fa-boxes"></i> Warehouse Stock</a></li>
                        @endcan
                        @can('stock.transfer.view')
                            <li><a href="{{ url('stock_transfers') }}"><i class="fas fa-exchange-alt"></i> Stock Transfers</a></li>
                        @endcan
                        @canany(['stock.adjust.view', 'stock.adjust.create'])
                            <li><a href="{{ route('stock_adjustments.index') }}"><i class="fas fa-sliders-h"></i> Stock Adjustments</a></li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            {{-- Sales & Customers --}}
            @canany(['sales.view', 'sales.view_own', 'sales.create', 'customers.view', 'zones.view', 'sales.officers.view', 'receipts.voucher.view'])
                <li class="m-nav-item m-nav-group">
                    <div class="m-nav-group-header" data-target="#mSubSales">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cash-register m-nav-icon"></i>
                            <span class="m-nav-text">Sales &amp; Customers</span>
                        </div>
                        <i class="fas fa-chevron-right m-chevron"></i>
                    </div>
                    <ul class="m-submenu collapse" id="mSubSales">
                        @canany(['sales.view', 'sales.view_own'])
                            <li><a href="{{ url('sale') }}"><i class="fas fa-receipt"></i> Sales Invoices</a></li>
                        @endcanany
                        @can('sales.create')
                            <li><a href="{{ route('pos.index') }}"><i class="fas fa-calculator"></i> POS Terminal</a></li>
                        @endcan
                        @can('customers.view')
                            <li><a href="{{ url('customers') }}"><i class="fas fa-user-friends"></i> Customers</a></li>
                        @endcan
                        @can('zones.view')
                            <li><a href="{{ url('zone') }}"><i class="fas fa-map-marker-alt"></i> Zones</a></li>
                        @endcan
                        @can('sales.officers.view')
                            <li><a href="{{ url('sales-officers') }}"><i class="fas fa-user-tie"></i> Sales Officers</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            {{-- Accounts & Vouchers (Always Highlighted & Easy to Find) --}}
            @canany(['chart.of.accounts.view', 'expense.voucher.view', 'receipts.voucher.view', 'journal.voucher.view', 'payment.voucher.view', 'income.voucher.view', 'checkbook.view'])
                @php
                    $isAccountsActive = request()->is('view_all*') || request()->is('accounts*') || request()->is('*voucher*') || request()->is('checkbook*');
                @endphp
                <li class="m-nav-item m-nav-group">
                    <div class="m-nav-group-header {{ $isAccountsActive ? 'active' : '' }}" data-target="#mSubAccounts">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wallet m-nav-icon text-indigo" style="color: #818cf8 !important;"></i>
                            <span class="m-nav-text font-weight-bold">Accounts &amp; Finance</span>
                        </div>
                        <i class="fas fa-chevron-right m-chevron {{ $isAccountsActive ? 'rotate' : '' }}"></i>
                    </div>
                    <ul class="m-submenu collapse {{ $isAccountsActive ? 'show' : '' }}" id="mSubAccounts">
                        @can('chart.of.accounts.view')
                            <li class="highlight-item">
                                <a href="{{ route('view_all') }}" class="{{ request()->is('view_all*') ? 'active font-weight-bold' : '' }}">
                                    <i class="fas fa-star text-warning"></i>
                                    <span>Chart Of Accounts</span>
                                    <span class="badge badge-primary ml-auto py-0 px-1" style="font-size: 8px;">VIP</span>
                                </a>
                            </li>
                        @endcan
                        @can('expense.voucher.view')
                            <li><a href="{{ route('all_expense_vochers') }}"><i class="fas fa-file-invoice"></i> Expense Voucher</a></li>
                            <li><a href="{{ route('expense_categories.index') }}"><i class="fas fa-list-check"></i> Expense Categories</a></li>
                        @endcan
                        @can('receipts.voucher.view')
                            <li><a href="{{ route('all_recepit_vochers') }}"><i class="fas fa-receipt"></i> Receipts Voucher</a></li>
                        @endcan
                        @can('journal.voucher.view')
                            <li><a href="{{ route('vouchers.index', 'journal voucher') }}"><i class="fas fa-book"></i> Journal Voucher</a></li>
                        @endcan
                        @can('payment.voucher.view')
                            <li><a href="{{ route('all_Payment_vochers') }}"><i class="fas fa-money-check"></i> Payment Voucher</a></li>
                        @endcan
                        @can('income.voucher.view')
                            <li><a href="{{ route('vouchers.index', 'income voucher') }}"><i class="fas fa-hand-holding-usd"></i> Income Voucher</a></li>
                        @endcan
                        @can('checkbook.view')
                            <li><a href="{{ route('checkbook.index') }}"><i class="fas fa-lock"></i> Day Closings</a></li>
                            <li><a href="{{ route('checkbook.transactions') }}"><i class="fas fa-history"></i> Cashbook History</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            {{-- Reports --}}
            @canany(['item.stock.report.view', 'purchase.report.view', 'sale.report.view', 'customer.ledger.view', 'vendor.ledger.view', 'inventory.onhand.view', 'profit.loss.report.view', 'recovery.report.view', 'payable.report.view', 'parties.balance.report.view', 'aging.report.view', 'balance.sheet.report.view', 'executive.report.view'])
                <li class="m-nav-item m-nav-group">
                    <div class="m-nav-group-header" data-target="#mSubReports">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line m-nav-icon"></i>
                            <span class="m-nav-text">Financial Reports</span>
                        </div>
                        <i class="fas fa-chevron-right m-chevron"></i>
                    </div>
                    <ul class="m-submenu collapse" id="mSubReports">
                        @can('executive.report.view')
                            <li><a href="{{ route('report.executive') }}"><i class="fas fa-crown text-warning"></i> Executive Report</a></li>
                        @endcan
                        @can('item.stock.report.view')
                            <li><a href="{{ route('report.item_stock') }}"><i class="fas fa-boxes"></i> Item Stock Report</a></li>
                        @endcan
                        @can('purchase.report.view')
                            <li><a href="{{ route('report.purchase') }}"><i class="fas fa-shopping-cart"></i> Purchase Report</a></li>
                        @endcan
                        @can('sale.report.view')
                            <li><a href="{{ route('report.sale') }}"><i class="fas fa-receipt"></i> Sale Report</a></li>
                            <li><a href="{{ route('report.product_sale_customer_wise') }}"><i class="fas fa-users-between-lines"></i> Product Sale (Customer)</a></li>
                        @endcan
                        @can('customer.ledger.view')
                            <li><a href="{{ route('report.customer.ledger') }}"><i class="fas fa-book-open"></i> Customer Ledger</a></li>
                        @endcan
                        @can('vendor.ledger.view')
                            <li><a href="{{ route('report.vendor.ledger') }}"><i class="fas fa-truck"></i> Vendor Ledger</a></li>
                        @endcan
                        @can('inventory.onhand.view')
                            <li><a href="{{ route('reports.onhand') }}"><i class="fas fa-warehouse"></i> Inventory On-Hand</a></li>
                        @endcan
                        @can('profit.loss.report.view')
                            <li><a href="{{ route('report.profit_loss') }}"><i class="fas fa-chart-pie"></i> Profit &amp; Loss</a></li>
                        @endcan
                        @can('recovery.report.view')
                            <li><a href="{{ route('report.recovery') }}"><i class="fas fa-file-invoice-dollar"></i> Recovery Report</a></li>
                        @endcan
                        @can('payable.report.view')
                            <li><a href="{{ route('report.payable') }}"><i class="fas fa-money-bill-wave"></i> Payable Report</a></li>
                        @endcan
                        @can('parties.balance.report.view')
                            <li><a href="{{ route('report.parties_balance') }}"><i class="fas fa-users"></i> Parties Balances</a></li>
                        @endcan
                        @can('aging.report.view')
                            <li><a href="{{ route('report.aging') }}"><i class="fas fa-hourglass-half"></i> Aging Report</a></li>
                        @endcan
                        @can('balance.sheet.report.view')
                            <li><a href="{{ route('report.balance_sheet') }}"><i class="fas fa-scale-balanced"></i> Balance Sheet</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany



            {{-- User Management --}}
            @canany(['users.view', 'roles.view', 'permissions.view', 'branches.view'])
                <li class="m-nav-item m-nav-group">
                    <div class="m-nav-group-header" data-target="#mSubUsers">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-shield m-nav-icon"></i>
                            <span class="m-nav-text">User Management</span>
                        </div>
                        <i class="fas fa-chevron-right m-chevron"></i>
                    </div>
                    <ul class="m-submenu collapse" id="mSubUsers">
                        @can('users.view')
                            <li><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a></li>
                        @endcan
                        @can('roles.view')
                            <li><a href="{{ route('roles.index') }}"><i class="fas fa-user-tag"></i> Roles</a></li>
                        @endcan
                        @can('permissions.view')
                            <li><a href="{{ route('permissions.index') }}"><i class="fas fa-key"></i> Permissions</a></li>
                        @endcan
                        @can('branches.view')
                            <li><a href="{{ route('branch.index') }}"><i class="fas fa-code-branch"></i> Branches</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany


            @endif

        </ul>
    </div>

    {{-- Drawer Footer --}}
    <div class="m-drawer-footer">
        @canany(['settings.view', 'settings.read'])
            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center text-white" style="border-color: rgba(255,255,255,0.2); font-size: 12px; gap: 6px;">
                <i class="fas fa-cog"></i> Settings
            </a>
        @endcanany
        <form method="POST" action="{{ route('logout') }}" class="flex-fill m-0">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center font-weight-bold" style="font-size: 12px; gap: 6px;">
                <i class="fas fa-power-off"></i> Logout
            </button>
        </form>
    </div>
</aside>
