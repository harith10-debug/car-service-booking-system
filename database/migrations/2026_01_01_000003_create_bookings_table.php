<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_package_id')->constrained()->restrictOnDelete();
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'])->default('Pending');
            $table->decimal('total_price', 10, 2);
            $table->text('admin_remarks')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('preferred_date');
            $table->index(['preferred_date', 'preferred_time']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
