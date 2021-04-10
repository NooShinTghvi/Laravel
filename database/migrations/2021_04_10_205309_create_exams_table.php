<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->smallInteger('number_of_phases');
            $table->enum('day_of_holding', ['شنبه', 'یک شنبه', 'دو شنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه']);
            $table->unsignedBigInteger('field_id');
            $table->foreign('field_id')->references('id')
                ->on('fields')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('education_base_id');
            $table->foreign('education_base_id')->references('id')
                ->on('education_bases')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('price');
            $table->string('image_path')->nullable();
            $table->longText('description')->nullable();
            $table->string('description_file')->nullable();
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
        Schema::dropIfExists('exams');
    }
}
