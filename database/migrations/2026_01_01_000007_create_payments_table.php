<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('payment_reference', 40)->unique();
            $table->enum('method', ['Cash', 'Card', 'Online Banking', 'E-Wallet']);
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2);
            $table->enum('status', ['Pending', 'Paid', 'Failed', 'Refunded'])->default('Paid')->index();
            $table->timestamp('paid_at')->nullable();
            $table->string('payer_name', 150);
            $table->string('payer_email')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->text('transaction_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['paid_at', 'status']);
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
