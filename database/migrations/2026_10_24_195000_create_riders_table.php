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
        if (!Schema::hasTable('riders')) {
            Schema::create('riders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('name', 100);
                $table->string('phone', 30);
                $table->string('cnic', 30)->nullable();
                $table->string('vehicle_no', 50)->nullable(); // e.g. KHI-4521 (Honda 70)
                $table->string('duty_status', 30)->default('available'); // available, on_delivery, off_duty
                $table->boolean('is_active')->default(true);
                $table->text('address')->nullable();
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};
