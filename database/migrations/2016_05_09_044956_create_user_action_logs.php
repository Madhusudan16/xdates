<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserActionLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action');
            $table->text('action_data')->comment="Current all affected data will store in json format";
            $table->text('prev_data')->comment="Prev all affected data will store in json format";
            $table->string('ip_address');
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
        Schema::drop('user_action_logs');
    }
}
