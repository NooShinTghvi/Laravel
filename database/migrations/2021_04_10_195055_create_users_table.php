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
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('melli_code', 10)->unique()->nullable();
            $table->string('mobile', 11)->unique();
            $table->unsignedBigInteger('education_base_id')->nullable();
            $table->foreign('education_base_id')->references('id')
                ->on('education_bases')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('field_id')->nullable();
            $table->foreign('field_id')->references('id')
                ->on('fields')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedSmallInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')
                ->on('city')->onUpdate('cascade')->onDelete('cascade');
            $table->string('melli_image_path')->nullable();
            $table->boolean('isAcceptable')->default(false);
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
