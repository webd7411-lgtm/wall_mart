<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentsAndBranchPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $newPermissions = [
            // 1. Customer Payments
            'customer.payments.view',
            'customer.payments.create',
            'customer.payments.edit',
            'customer.payments.delete',
            'customer.payments.reminder',

            // 2. Vendor Payments
            'vendor.payments.view',
            'vendor.payments.create',
            'vendor.payments.edit',
            'vendor.payments.delete',

            // 3. Payment Vouchers & Accounts
            'payment.voucher.view',
            'payment.voucher.create',
            'payment.voucher.edit',
            'payment.voucher.delete',
            'payment.voucher.print',

            // 4. POS & Advanced Payments
            'payments.pos_split',
            'payments.refund',
            'web_orders.verify_payment',

            // 5. Restaurant Exclusive Permissions
            'restaurant.tables.view',
            'restaurant.tables.create',
            'restaurant.tables.edit',
            'restaurant.tables.delete',

            'restaurant.riders.view',
            'restaurant.riders.create',
            'restaurant.riders.edit',
            'restaurant.riders.delete',

            'restaurant.deliveries.view',
            'restaurant.deliveries.create',
            'restaurant.deliveries.edit',
            'restaurant.deliveries.delete',
            'restaurant.deliveries.status_update',

            'restaurant.kds.view',
            'restaurant.pos.view',

            // 6. Stock adjust & operations
            'stock.adjust.view',
            'stock.adjust.create',
            'stock.adjust.edit',
            'stock.adjust.delete',
            'purchase_pos.create'
        ];

        foreach ($newPermissions as $permName) {
            Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => 'web'
            ]);
        }

        // Assign all permissions to Super Admin role
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
