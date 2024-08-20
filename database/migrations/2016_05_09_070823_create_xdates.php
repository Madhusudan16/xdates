<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xdates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id');
            $table->integer('user_id');
            $table->date('xdate');
            $table->string('xname');
            $table->integer('line')->nullable();
            $table->integer('policy_type');
            $table->integer('industry');
            $table->string('xcontact');
            $table->integer('producer');
            $table->string('phone');
            $table->string('city');
            $table->string('state');
            $table->mediumText('website');
            $table->string('email');
            $table->date('follow_up_date');
            $table->tinyInteger('status')->comment = "0- Live, 1- converted, 2 - dead";
            $table->dateTime('last_note_datetime')->nullable(); 
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
        Schema::drop('xdates');
    }
}
