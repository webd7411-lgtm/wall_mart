<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modify branches table to be flexible
        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                if (!Schema::hasColumn('branches', 'type')) {
                    $table->string('type', 50)->default('mart')->after('name');
                }
                if (!Schema::hasColumn('branches', 'status')) {
                    $table->boolean('status')->default(1)->after('number');
                }
            });

            // Make user_id nullable and drop unique constraint if exists
            try {
                DB::statement("ALTER TABLE `branches` MODIFY `user_id` BIGINT(20) UNSIGNED NULL");
            } catch (\Exception $e) {}
            try {
                DB::statement("ALTER TABLE `branches` DROP INDEX `branches_user_id_unique`");
            } catch (\Exception $e) {}
        }

        // 2. Add branch_id to users table (nullable, so superadmins can access all branches)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
            });
        }

        // 3. Tables to add branch_id
        $tables = [
            'products',
            'categories',
            'brands',
            'sales',
            'sale_returns',
            'purchase_returns',
            'customers',
            'customer_ledgers',
            'customer_payments',
            'vendors',
            'vendor_ledgers',
            'vendor_payments',
            'accounts',
            'account_heads',
            'journal_entries',
            'vouchers',
            'voucher_masters',
            'expense_vouchers',
            'payment_vouchers',
            'receipts_vouchers',
            'day_closings',
            'drawer_transactions',
            'stock_adjustments',
            'stock_transfers',
            'invoice_series',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->default(1)->index();
                });
            }
        }

        // 4. Seed / Ensure Branch 1 (Mart) and Branch 2 (Restaurant) exist
        if (Schema::hasTable('branches')) {
            $mart = DB::table('branches')->where('id', 1)->first();
            if ($mart) {
                DB::table('branches')->where('id', 1)->update([
                    'name' => 'Mart',
                    'type' => 'mart',
                    'address' => $mart->address ?: 'Main Store',
                    'number' => $mart->number ?: '0000000000',
                    'status' => 1,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('branches')->insert([
                    'id' => 1,
                    'name' => 'Mart',
                    'type' => 'mart',
                    'address' => 'Main Store',
                    'number' => '0000000000',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $restaurant = DB::table('branches')->where('id', 2)->first();
            if (!$restaurant) {
                DB::table('branches')->insert([
                    'id' => 2,
                    'name' => 'Restaurant',
                    'type' => 'restaurant',
                    'address' => 'Food Branch',
                    'number' => '0000000000',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5. Backfill existing records to Branch 1 (Mart)
            foreach ($tables as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                    DB::table($tableName)->whereNull('branch_id')->update(['branch_id' => 1]);
                }
            }

            // Also inward_gatepasses, purchases, stocks, warehouses if any null
            foreach (['inward_gatepasses', 'purchases', 'stocks', 'warehouses'] as $extraTable) {
                if (Schema::hasTable($extraTable) && Schema::hasColumn($extraTable, 'branch_id')) {
                    DB::table($extraTable)->whereNull('branch_id')->update(['branch_id' => 1]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'products',
            'categories',
            'brands',
            'sales',
            'sale_returns',
            'purchase_returns',
            'customers',
            'customer_ledgers',
            'customer_payments',
            'vendors',
            'vendor_ledgers',
            'vendor_payments',
            'accounts',
            'account_heads',
            'journal_entries',
            'vouchers',
            'voucher_masters',
            'expense_vouchers',
            'payment_vouchers',
            'receipts_vouchers',
            'day_closings',
            'drawer_transactions',
            'stock_adjustments',
            'stock_transfers',
            'invoice_series',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('branch_id');
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
