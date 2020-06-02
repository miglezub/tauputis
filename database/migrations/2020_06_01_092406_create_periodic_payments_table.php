<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodicPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodic_payments', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('fk_user_id');
            $table->unsignedBigInteger('fk_payment_type_id');
            $table->string('caption', 100)->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_income');
            $table->double('value', 8, 2);
            $table->integer('periodic_type');
            $table->integer('periodic_day');
            $table->date('last_added')->nullable();
            $table->timestamps();

            $table->foreign('fk_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('fk_payment_type_id')->references('id')->on('payment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodic_payments');
    }
}
