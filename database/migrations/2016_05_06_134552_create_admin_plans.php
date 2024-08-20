<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('n_allowed_users')->comment="no. of allowed users";
            $table->decimal('cost', 10, 2)->comment="monthly cost of plan";
            $table->decimal('refer_percentage', 5, 2)->comment="referer willl get benfit by this percantage";
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
        Schema::drop('plans');
    }
}
