<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingCronLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_cron_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id');
            $table->tinyInteger('status')->default(0)->comment('0 - payment failed 1- payment success');
            
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
        Schema::drop('billing_cron_log');
    }
}
