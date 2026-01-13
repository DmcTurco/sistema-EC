<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // usuario final (externo)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total_amount', 10, 2);
            // 0=pending,1=paid,2=shipped,3=completed,9=cancelled
            $table->tinyInteger('status')->default(0);
            // tracking del post
            $table->unsignedBigInteger('referral_post_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
