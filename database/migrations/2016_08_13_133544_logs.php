<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Logs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('log_type')->comment('1:customized_field 2:restored Customized Field');
            $table->integer('event_user_id');
            $table->integer('owner_id')->nullable();
            $table->string('event_data',255)->nullable();
            $table->string('notes',255)->nullable(); 
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
        Schema::drop('logs');
    }
}
