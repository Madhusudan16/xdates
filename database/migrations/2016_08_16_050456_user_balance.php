<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_balance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner_id',20);
            $table->integer('real_user_id')->nullable();
            $table->integer('balance_from_user_id')->default(0);
            $table->string('amount',20);
            $table->integer('trial_days')->default(0);
            $table->tinyInteger('type')->comment('1-Credit Pay amount/ Referral amt/days | 2- Debit amount/days');
            $table->tinyInteger('status')->default(1)->comment('0-Inactive | 1-Active | 2-Deleted');
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
        Schema::drop('user_balance');
    }
}
