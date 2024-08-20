<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CancelAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_account', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner_id',20);
            $table->date('expired_date');
            $table->tinyInteger('status')->comment('0-cancel | 1 - Not Cancel');
            $table->string('token',50)->nullable();
            $table->string('child_user',500)->nullable(); 
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
        Schema::drop('cancel_account');
    }
}
