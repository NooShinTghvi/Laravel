<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_lesson', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')->references('id')
                ->on('questions')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['question_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_lesson');
    }
}
