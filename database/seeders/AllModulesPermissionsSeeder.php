<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AllModulesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Active Standard modules with (view, create, edit, delete)
        $standardModules = [
            'products',
            'discount.products',
            'categories',
            'subcategories',
            'brands',
            'units',
            'warehouse',
            'warehouse.stock',
            'stock.transfer',
            'stock.adjust',
            'purchases',
            'purchase.returns',
            'vendors',
            'vendor.payments',
            'vendor.bilties',
            'inward.gatepass',
            'sales',
            'sales.returns',
            'customers',
            'customer.payments',
            'bookings',
            'checkbook',
            'zones',
            'sales.officers',
            'chart.of.accounts',
            'expense.voucher',
            'receipts.voucher',
            'journal.voucher',
            'payment.voucher',
            'income.voucher',
            'restaurant.tables',
            'restaurant.riders',
            'restaurant.deliveries',
            'users',
            'roles',
            'permissions',
            'settings'
        ];

        // Standard actions
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($standardModules as $module) {
            foreach ($actions as $action) {
                $permissionName = strtolower("{$module}.{$action}");
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        // Custom / Granular permissions
        $specificPermissions = [
            'dashboard.view',
            'home.view',
            'purchase_pos.create',
            'sales.view_own',
            'customer.payments.reminder',
            'customer.ledger.view',
            'payments.pos_split',
            'payments.refund',
            'payment.voucher.print',
            'settings.update',
            'restaurant.deliveries.status_update',
            'restaurant.kds.view',
            'restaurant.pos.view',
            // Branches (view, edit, delete - NO branches.create to prevent creating more branches)
            'branches.view',
            'branches.edit',
            'branches.delete',
            // Reports (Read-only View)
            'executive.report.view',
            'item.stock.report.view',
            'purchase.report.view',
            'sale.report.view',
            'vendor.ledger.view',
            'inventory.onhand.view',
            'profit.loss.report.view',
            'recovery.report.view',
            'payable.report.view',
            'parties.balance.report.view',
            'aging.report.view',
            'balance.sheet.report.view'
        ];

        foreach ($specificPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Grant all new permissions to Super Admin just in case (though Gate::before handles it)
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }
    }
}
