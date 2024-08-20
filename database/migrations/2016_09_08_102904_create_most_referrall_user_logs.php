<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMostReferrallUserLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('most_referrall_user_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('user_id');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->tinyInteger('status')->comment("0-monthly 1- yearly");
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
        Schema::drop('most_referrall_user_logs');
    }
}
