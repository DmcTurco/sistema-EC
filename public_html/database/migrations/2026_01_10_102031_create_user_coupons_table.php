<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('general_id')->nullable();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('order_id'); // Orden que otorgó el cupón
            $table->tinyInteger('status')->default(0); // 0=disponible, 1=usado
            $table->timestamp('used_at')->nullable(); // Fecha de uso
            $table->timestamps();

            // Foreign keys
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Índices
            $table->index(['general_id', 'status']);
            $table->index('coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};
