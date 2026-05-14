<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Le vendeur confirme que son exemplaire est préparé / prêt pour envoi
            $table->boolean('seller_ready')->default(false)->after('unit_price');
            $table->timestamp('seller_ready_at')->nullable()->after('seller_ready');
            // L'admin confirme avoir payé le vendeur pour cet article
            $table->boolean('seller_paid')->default(false)->after('seller_ready_at');
            $table->timestamp('seller_paid_at')->nullable()->after('seller_paid');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['seller_ready', 'seller_ready_at', 'seller_paid', 'seller_paid_at']);
        });
    }
};
