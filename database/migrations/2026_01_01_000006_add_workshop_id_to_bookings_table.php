<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('workshop_id')->nullable()->after('service_package_id')->constrained('workshops')->nullOnDelete();
            $table->index('workshop_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['workshop_id']);
            $table->dropIndex(['workshop_id']);
            $table->dropColumn('workshop_id');
        });
    }
};
