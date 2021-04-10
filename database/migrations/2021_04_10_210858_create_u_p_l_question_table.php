<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUPLQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u_p_l_question', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('u_p_l_id');
            $table->foreign('u_p_l_id')->references('id')
                ->on('u_p_lesson')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')->references('id')
                ->on('questions')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('status', ['-', '*', '!'])->nullable();
            $table->enum('selected_choice', ['1', '2', '3', '4', '#'])->nullable();
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
        Schema::dropIfExists('u_p_l_question');
    }
}
