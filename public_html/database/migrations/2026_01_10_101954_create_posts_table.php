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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('product_id');
            $table->string('intro_video_path');
            $table->tinyInteger('status')->default(0); // 0=privado,1=publico
            $table->integer('views')->default(0); // Contador de vistas
            $table->integer('sales')->default(0); // Contador de ventas
            $table->softDeletes();
            $table->timestamps();

            $table->index('staff_id');
            $table->index('product_id');
            $table->index('status');
            $table->index(['staff_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
