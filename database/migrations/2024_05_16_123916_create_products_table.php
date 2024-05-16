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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('brand_id')->unsigned();
            $table->string('name');
            $table->boolean('is_trendy')->default(false);
            $table->boolean('is_available')->default(false);
            $table->double('price');
            $table->integer('amount');
            $table->integer('quantity');
            $table->double('discount');
            $table->string('image');
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
