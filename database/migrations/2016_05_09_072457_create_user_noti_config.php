<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNotiConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_noti_config', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('obj_type')->comment = '1- email, 2 - mobile';
            $table->string('obj_value');
            $table->integer('user_id'); 
            $table->tinyInteger('status')->comment = '0-activation required, 1-active, 2 - deleted';
            $table->string('country_code',5)->nullable();
            $table->tinyInteger('is_active')->comment('notification 0-inactive 1-active')->default(0);
            $table->string('token',255)->nullable();
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
        Schema::drop('user_noti_config');
    }
}
