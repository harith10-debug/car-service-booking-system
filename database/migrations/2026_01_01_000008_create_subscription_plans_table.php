<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name', 120)->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->enum('billing_cycle', ['Monthly', 'Yearly'])->default('Monthly');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->unsignedTinyInteger('priority_level')->default(1)->comment('Higher value means higher booking priority');
            $table->text('benefits')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
            $table->timestamps();

            $table->index(['status', 'monthly_price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
