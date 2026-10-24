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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'order_type')) {
                $table->string('order_type', 50)->default('takeaway')->nullable()->after('sale_status');
            }
            if (!Schema::hasColumn('sales', 'table_no')) {
                $table->string('table_no', 50)->nullable()->after('order_type');
            }
            if (!Schema::hasColumn('sales', 'token_no')) {
                $table->string('token_no', 50)->nullable()->after('table_no');
            }
            if (!Schema::hasColumn('sales', 'order_status')) {
                $table->string('order_status', 50)->default('completed')->nullable()->after('token_no');
            }
            if (!Schema::hasColumn('sales', 'order_notes')) {
                $table->text('order_notes')->nullable()->after('order_status');
            }
            if (!Schema::hasColumn('sales', 'rider_name')) {
                $table->string('rider_name', 100)->nullable()->after('order_notes');
            }
            if (!Schema::hasColumn('sales', 'rider_phone')) {
                $table->string('rider_phone', 50)->nullable()->after('rider_name');
            }
            if (!Schema::hasColumn('sales', 'kot_printed_at')) {
                $table->timestamp('kot_printed_at')->nullable()->after('rider_phone');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'item_notes')) {
                $table->string('item_notes', 255)->nullable()->after('total');
            }
            if (!Schema::hasColumn('sale_items', 'kot_printed_qty')) {
                $table->decimal('kot_printed_qty', 10, 2)->default(0)->after('item_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $cols = ['order_type', 'table_no', 'token_no', 'order_status', 'order_notes', 'rider_name', 'rider_phone', 'kot_printed_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('sales', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $cols = ['item_notes', 'kot_printed_qty'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('sale_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
