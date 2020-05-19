<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('fk_user_id');
            $table->unsignedBigInteger('fk_type');
            $table->double('monthly_goal', 8, 2)->nullable();
            $table->double('last_month_value', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('fk_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('fk_type')->references('id')->on('payment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
