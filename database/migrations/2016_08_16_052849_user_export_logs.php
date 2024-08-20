<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserExportLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_export_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kindof',255);
            $table->integer('user_id');
            $table->string('exported_file_name',255);
            $table->tinyInteger('status')->comment('1-active,2-deleted');
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
        Schema::drop('user_credit_card');
    }
}
