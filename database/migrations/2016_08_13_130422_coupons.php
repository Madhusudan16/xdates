<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Coupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('user_type')->comment('1-current_user 2-new User');
            $table->string('coupon',8);
            $table->integer('coupan_day');
            $table->decimal('coupon_percent',10,2);
            $table->tinyInteger('status')->comment('0-inactive 1-active');
            $table->timestamps();
            $table->date('coupon_expire'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('coupons');
    }
}
