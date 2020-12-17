<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 600);
            $table->integer('price');
            $table->string('image', 255);
            $table->float('popularity');
            $table->string('uid', 24)->unique();
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('df_id')->nullable();
            $table->foreign('df_id')->references('id')->on('discounted_food')
                ->onUpdate('cascade');
            $table->unique(['name', 'restaurant_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foods');
    }
}
