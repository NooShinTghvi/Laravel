<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->smallInteger('number');
            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->references('id')
                ->on('exams')->onUpdate('cascade')->onDelete('cascade');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('duration')->default(0);
            $table->smallInteger('negative_score');
            $table->string('image_path')->nullable();
            $table->string('file_of_question_path')->nullable();
            $table->string('file_of_answer_path')->nullable();
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
        Schema::dropIfExists('phases');
    }
}
