<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('reviews')->default(0);
            $table->integer('discount')->default(0);
            $table->decimal('rate', 3, 2)->default(0.00);
            $table->integer('sell')->default(0);
            $table->integer('trend')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
