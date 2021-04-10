<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('province_id');
            $table->foreign('province_id')->references('id')
                ->on('province')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedSmallInteger('county_id');
            $table->foreign('county_id')->references('id')
                ->on('county')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 50);
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
        Schema::dropIfExists('cities');
    }
}
