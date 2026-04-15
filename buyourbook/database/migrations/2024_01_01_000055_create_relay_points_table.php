<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relay_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('city', 100)->index();
            $table->string('district', 100)->nullable()->index();
            $table->string('contact_phone', 20);
            $table->string('schedule')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('coordinates')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relay_points');
    }
};
