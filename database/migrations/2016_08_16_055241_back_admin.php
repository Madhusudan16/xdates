<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BackAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('email',255);
            $table->string('password',255);
            $table->integer('user_type_id');
            $table->string('photo',255);
            $table->tinyInteger('status');
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
        Schema::drop('back_admin');
    }
}
