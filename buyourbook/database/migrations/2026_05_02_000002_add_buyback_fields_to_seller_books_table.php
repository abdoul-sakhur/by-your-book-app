<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter les champs de rachat sur seller_books
        Schema::table('seller_books', function (Blueprint $table) {
            $table->unsignedInteger('purchase_price')->nullable()->after('price')
                ->comment('Prix d\'achat initial déclaré par le vendeur');
            $table->unsignedInteger('buyback_price')->nullable()->after('purchase_price')
                ->comment('Prix de rachat validé par l\'admin');
            $table->unsignedInteger('counter_price')->nullable()->after('buyback_price')
                ->comment('Contre-proposition de l\'admin');
            $table->enum('buyback_status', ['pending', 'negotiating', 'accepted', 'rejected'])->default('pending')->after('status');
            $table->text('buyback_notes')->nullable()->after('buyback_status');
            $table->boolean('admin_paid_seller')->default(false)->after('buyback_notes');
        });
    }

    public function down(): void
    {
        Schema::table('seller_books', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'buyback_price', 'counter_price', 'buyback_status', 'buyback_notes', 'admin_paid_seller']);
        });
    }
};
