<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('ip', 45)->nullable();
            $table->string('session_id', 64)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('viewed_at');
            $table->timestamps();

            $table->index('viewed_at');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
