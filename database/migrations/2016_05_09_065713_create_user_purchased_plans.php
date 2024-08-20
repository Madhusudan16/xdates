<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPurchasedPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_purchased_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id');
            $table->integer('user_id');
            $table->datetime('next_bill_date');
            $table->tinyInteger('status')->comment = "0-inactive,1-active,2-deleted"; 
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
        Schema::drop('user_purchased_plans');
    }
}
