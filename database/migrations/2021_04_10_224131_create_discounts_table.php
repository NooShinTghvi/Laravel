<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();
            $table->enum('type', ['PERCENT', 'CASH'])->default('PERCENT');
            $table->integer('value');
            $table->integer('maximum_value');
            $table->date('expire_date');
            $table->unsignedInteger('count');
            $table->unsignedInteger('used_number')->default(0);
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
        Schema::dropIfExists('discounts');
    }
}
