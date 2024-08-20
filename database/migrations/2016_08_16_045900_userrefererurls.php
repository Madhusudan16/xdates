<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Userrefererurls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userrefererurls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('short_url',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop('userrefererurls');
    }
}
