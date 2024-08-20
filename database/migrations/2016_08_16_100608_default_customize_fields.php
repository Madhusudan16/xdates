<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultCustomizeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_customize_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->nullable()->comment('1 - Lines, 2- Industry, 3 - Policy Type, 4- Commercial Policy TYpe');
            $table->string('name',255);
            $table->tinyInteger('is_permanent')->default(0);
            $table->integer('display_order')->default(0);
            $table->tinyInteger('status')->comment('0-inactive,1-active,2-deleted');
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
        Schema::drop('default_customize_fields');
    }
}
