<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotificationFrequency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_frequency', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('frequency_keys')->nullable();
            $table->string('frequency',10);
            $table->tinyInteger('type')->comment('1-xdates 2-follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notification_frequency');
    }
}
