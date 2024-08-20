<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserResetPassword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reset_password', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('token');
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
        Schema::drop('user_reset_password');
    }
}
