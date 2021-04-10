<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonPhaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_phase', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('phase_id');
            $table->foreign('phase_id')->references('id')
                ->on('phases')->onUpdate('cascade')->onDelete('cascade');
            $table->double('average')->nullable();
            $table->double('standard_deviation')->nullable();
            $table->double('highest_balance')->nullable();
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
        Schema::dropIfExists('lesson_phase');
    }
}
