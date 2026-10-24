<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers\BranchHelper;

class EnforceRestaurantBranchRestriction
{
    /**
     * Whitelist of route names allowed in Restaurant POS mode.
     */
    protected array $allowedRoutePatterns = [
        // Dashboard
        'home',
        'dashboard',

        // POS Billing
        'pos.*',
        'purchase-pos.*',
        'restaurant.*',

        // Products / Catalog / Menu
        'product',
        'product.*',
        'products.*',
        'store',
        'create_prodcut',
        'create-product',
        'store-product',
        'productget',
        'productview',
        'search-product-name',
        'generate-barcode-image',
        'fetch-subcategories',
        'get-all-subcategories',
        'get-categories',
        'get-brands',

        // Sales / Orders / Invoices
        'sale',
        'sale.*',
        'sales.*',
        'invoice_series.*',

        // Categories, Units, Brands (for product management)
        'category.*',
        'Category.*',
        'subcategory.*',
        'unit.*',
        'Unit.*',
        'brand.*',
        'Brand.*',
        'store.category',
        'store.subcategory',
        'store.brand',
        'store.Brand',
        'store.unit',
        'store.Unit',
        'delete.category',
        'delete.subcategory',
        'delete.brand',
        'delete.Brand',
        'delete.unit',
        'delete.Unit',

        // Customer management
        'customer',
        'customer.*',
        'customers.*',

        // Expense Vouchers & Categories
        'all_expense_vochers',
        'expense_vochers',
        'store_expense_vochers',
        'expenseprint',
        'expense_categories.*',

        // Accounts & Chart of Accounts
        'view_all',
        'accounts.*',
        'account-heads.*',
        'get.accounts.by.head',

        // Settings (Branch specific)
        'settings.index',
        'settings.update',

        // Sale Report
        'report.sale',
        'report.sale.*',

        // Branch switching & Auth
        'branch.switch',
        'logout',
        'notifications.*',
        'profile.*',
    ];

    /**
     * Whitelist of exact or prefix URI paths allowed.
     */
    protected array $allowedUriPrefixes = [
        'home',
        'dashboard',
        'pos',
        'restaurant',
        'product',
        'products',
        'product-catalog',
        'create_prodcut',
        'create-product',
        'store-product',
        'productget',
        'productview',
        'search-product-name',
        'generate-barcode-image',
        'get-subcategories',
        'get-all-subcategories',
        'get-categories',
        'get-brands',
        'sale',
        'sales',
        'invoice-series',
        'category',
        'categories',
        'subcategory',
        'subcategories',
        'unit',
        'units',
        'brand',
        'brands',
        'customer',
        'customers',
        'all_expense_vochers',
        'expense_vochers',
        'store_expense_vochers',
        'expenseprint',
        'expense-categories',
        'view_all',
        'accounts',
        'accounts-head',
        'get-accounts-by-head',
        'settings',
        'report/sale',
        'branch/switch',
        'logout',
        'notifications',
        'profile',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        // Only enforce when the active branch is a Restaurant
        if (!BranchHelper::isRestaurantBranch()) {
            return $next($request);
        }

        $routeName = $request->route() ? $request->route()->getName() : null;
        $path = trim($request->path(), '/');

        // Check if route name matches any allowed pattern
        if ($routeName) {
            foreach ($this->allowedRoutePatterns as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return $next($request);
                }
            }
        }

        // Check if URI matches any allowed prefix (case-insensitive)
        $pathLower = strtolower($path);
        foreach ($this->allowedUriPrefixes as $prefix) {
            $prefixLower = strtolower($prefix);
            if ($pathLower === $prefixLower || Str::startsWith($pathLower, $prefixLower . '/')) {
                return $next($request);
            }
        }

        // If not allowed, restrict access!
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'error' => 'This module is restricted in Restaurant POS mode.',
                'message' => 'Your active branch is configured for Restaurant POS only.'
            ], 403);
        }

        return redirect()->route('pos.index')->with('warning', 'Access restricted: Only POS, Products, Sales, and Branch Settings are accessible in Restaurant POS mode.');
    }
}
