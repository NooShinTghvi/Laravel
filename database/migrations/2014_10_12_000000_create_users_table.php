<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 75);
            $table->string('last_name', 75);
            $table->string('mobile', 11)->unique();
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('credit')->default(0);
            $table->string('password');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')
                ->onUpdate('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
