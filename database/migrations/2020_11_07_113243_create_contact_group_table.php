<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->foreign('group_id')->references('id')->on('groups')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('contact_id');
            $table->foreign('contact_id')->references('id')->on('contact')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['group_id', 'contact_id']);
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
        Schema::dropIfExists('contact_group');
    }
}
