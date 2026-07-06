<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('estimated_duration')->comment('Duration in minutes');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->index('package_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};
