<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->unique();
            $table->float('velocity');
            $table->boolean('is_busy')->nullable()->default(false);
            $table->dateTime('delivery_time')->default(\Carbon\Carbon::now());
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('deliveries');
    }
}
