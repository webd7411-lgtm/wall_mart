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
            if (!Schema::hasColumn('sales', 'rider_id')) {
                $table->unsignedBigInteger('rider_id')->nullable()->after('rider_phone')->index();
                $table->text('delivery_address')->nullable()->after('rider_id');
                $table->decimal('delivery_charges', 10, 2)->default(0)->after('delivery_address');
                $table->string('delivery_status', 30)->default('pending')->after('delivery_charges'); // pending, dispatched, delivered, cancelled
                $table->timestamp('dispatched_at')->nullable()->after('delivery_status');
                $table->timestamp('delivered_at')->nullable()->after('dispatched_at');
                $table->boolean('rider_cash_settled')->default(false)->after('delivered_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'rider_id',
                'delivery_address',
                'delivery_charges',
                'delivery_status',
                'dispatched_at',
                'delivered_at',
                'rider_cash_settled',
            ]);
        });
    }
};
