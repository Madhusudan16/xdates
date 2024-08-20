<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SendNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_notification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id',20);
            $table->tinyInteger('type')->comment('1-xdate 2-follow_up_date 3-trial_expire');
            $table->string('event_id',20)->nullable();
            $table->tinyInteger('status')->comment('0-Not Send | 1 - Send')->default(0);
            $table->date('send_date')->nullable();
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
        Schema::drop('send_notification');
    }
}
