<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCustomizeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_customize_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->comment('1 - Lines, 2- Industry, 3 - Policy Type, 4- Commercial Policy TYpe');
            $table->string('name');
            $table->integer('owner_id')->comment="this will comes from users table - owner id";
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
        Schema::drop('user_customize_fields');
    }
}
