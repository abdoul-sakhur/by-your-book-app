<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_address')->nullable()->after('relay_point_id');
            $table->string('delivery_phone', 20)->nullable()->after('delivery_address');
            $table->unsignedInteger('delivery_fee')->default(0)->after('total_amount');
            $table->string('payment_method', 30)->default('cash')->after('delivery_fee'); // cash|mobile_money
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_address', 'delivery_phone', 'delivery_fee', 'payment_method']);
        });
    }
};
