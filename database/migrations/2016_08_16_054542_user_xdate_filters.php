<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserXdateFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_xdate_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('tab_name',50);
            $table->text('filter_obj')->nullable();
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
        Schema::drop('user_xdate_filters');
    }
}
