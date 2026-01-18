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
            $table->string('order_number')->unique();
            
            // Usuario general (puede ser null si compra sin login)
            $table->unsignedBigInteger('general_id')->nullable();
            
            // Datos del cliente (siempre se guarda)
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            
            // Dirección de envío
            $table->text('shipping_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            
            // Totales
            $table->decimal('total_amount', 10, 2);
            
            // Estado: 0=pending, 1=paid, 2=shipped, 3=completed, 9=cancelled
            $table->tinyInteger('status')->default(0);
            
            // Tracking del post que generó la venta
            $table->unsignedBigInteger('referral_post_id')->nullable();
            
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign keys
            // $table->foreign('general_id')->references('id')->on('generals')->onDelete('set null');
            // $table->foreign('referral_post_id')->references('id')->on('posts')->onDelete('set null');
            
            // Índices
            $table->index('order_number');
            $table->index('general_id');
            $table->index('status');
            $table->index('referral_post_id');
            $table->index('created_at');
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