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
        if (!Schema::hasTable('restaurant_tables')) {
            Schema::create('restaurant_tables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('table_no', 50);
                $table->string('floor_area', 100)->default('Main Hall');
                $table->unsignedSmallInteger('capacity')->default(4);
                $table->string('status', 30)->default('available'); // available, occupied, reserved, maintenance
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                $table->unique(['branch_id', 'table_no']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
