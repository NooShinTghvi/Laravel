<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->string('factorNumber')->unique();
            $table->text('description')->nullable();
            $table->string('card_number', 16)->nullable();
            $table->unsignedBigInteger('cart_id');
            $table->foreign('cart_id')->references('id')
                ->on('carts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->foreign('discount_id')->references('id')
                ->on('discounts')->onUpdate('cascade')->onDelete('set null');
            $table->enum('condition', ['PENDING', 'SUCCESSFUL', 'FAILED', 'NOSERVICE'])->default('PENDING');
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
        Schema::dropIfExists('transactions');
    }
}
