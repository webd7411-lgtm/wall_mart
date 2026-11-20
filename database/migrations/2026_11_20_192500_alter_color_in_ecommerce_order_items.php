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
        if (Schema::hasTable('ecommerce_order_items')) {
            try {
                DB::statement("ALTER TABLE `ecommerce_order_items` MODIFY `color` TEXT NULL");
            } catch (\Exception $e) {
                // Fallback for non-MySQL or already text
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ecommerce_order_items')) {
            try {
                DB::statement("ALTER TABLE `ecommerce_order_items` MODIFY `color` VARCHAR(255) NULL");
            } catch (\Exception $e) {
                //
            }
        }
    }
};
