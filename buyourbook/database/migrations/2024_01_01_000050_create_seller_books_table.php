<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('official_book_id')->constrained()->cascadeOnDelete();
            $table->string('condition', 20)->default('good'); // new|good|acceptable
            $table->unsignedInteger('price');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->json('images')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending|approved|rejected
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['official_book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_books');
    }
};
