<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('first_name',255);
            $table->string('last_name',255);
            $table->string('email',255)->unique();
            $table->tinyInteger('user_type')->default(1)->comment('default 1 for owner');
            $table->string('password',200);
            $table->string('profile_image',255)->nullable();
            $table->string('choosed_timezone',255)->nullable();
            $table->tinyInteger('status')->comment('0-inactive,1-active,2-deleted'); 
            $table->tinyInteger('verified');
            $table->string('token',255)->nullable();
            $table->string('remember_token',255)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->tinyInteger('is_need_change_pass')->comment('1 - created by other and first login')->nullable();
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
        Schema::drop('admins');
    }
}
