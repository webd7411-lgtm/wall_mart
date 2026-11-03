<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_orders', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->default(2)->after('web_customer_id');
            }
            if (!Schema::hasColumn('ecommerce_orders', 'sale_id')) {
                $table->unsignedBigInteger('sale_id')->nullable()->after('branch_id');
            }
            if (!Schema::hasColumn('ecommerce_orders', 'rider_id')) {
                $table->unsignedBigInteger('rider_id')->nullable()->after('sale_id');
            }
            if (!Schema::hasColumn('ecommerce_orders', 'rider_name')) {
                $table->string('rider_name', 191)->nullable()->after('rider_id');
            }
            if (!Schema::hasColumn('ecommerce_orders', 'rider_phone')) {
                $table->string('rider_phone', 100)->nullable()->after('rider_name');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'ecommerce_order_id')) {
                $table->unsignedBigInteger('ecommerce_order_id')->nullable()->after('branch_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'sale_id', 'rider_id', 'rider_name', 'rider_phone']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['ecommerce_order_id']);
        });
    }
};
