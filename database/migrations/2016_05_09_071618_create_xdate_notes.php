<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXdateNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xdate_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('xdate_id');
            $table->integer('user_id');
            $table->text('notes');
            $table->tinyInteger('status')->comment = "1-active,2-deleted"; 
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
        Schema::drop('xdate_notes');
    }
}
