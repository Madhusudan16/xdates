<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExportedList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exported_list', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('1-X-Dates 2-Notes');
            $table->integer('number_of_item');
            $table->date('expired_date');
            $table->string('file_name',50);
            $table->string('file_size',20);
            $table->tinyInteger('format')->comment('1 CSV 2-Excel');
            $table->integer('user_id');
            $table->integer('owner_id')->nullable();
            $table->string('user_name',50);
            $table->tinyInteger('status')->comment('0-not expired | 1 - expired');
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
        Schema::drop('exported_list');
    }
}
