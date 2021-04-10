<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUPLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u_p_lesson', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('u_p_id');
            $table->foreign('u_p_id')->references('id')
                ->on('user_phase')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('correct_question')->default(0);
            $table->integer('incorrect_question')->default(0);
            $table->integer('unanswered_question')->default(0);
            $table->double('grade')->default(0);
            $table->double('balance')->default(0);
            $table->integer('rating')->default(0);
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
        Schema::dropIfExists('u_p_lesson');
    }
}
