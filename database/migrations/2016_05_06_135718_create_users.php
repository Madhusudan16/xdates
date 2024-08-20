<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('com_name')->comment="company name of user";
            $table->string('name')->comment="user  name";
            $table->string('first_name',255);
            $table->string('last_name',255);
            $table->string('email')->comment="user email";
            $table->tinyInteger('user_type')->default(1)->comment="default 1 for owner";
            $table->string('password');
            $table->string('refer_via',50)->nullable();
            $table->string('profile_image')->nullable();
            $table->integer('parent_user_id')->comment="parent user id(owner id) ";
            $table->string('google_id')->comment="google user id";
            $table->integer('current_plan')->comment="0- means trial period, 1..,10 - plan id";
            $table->date('next_bill_date')->nullable();
            $table->date('trial_start_date');
            $table->date('trial_end_date');
            $table->string('choosed_timezone');
            $table->tinyInteger('noti_mob_frequency')->comment = 'in days';
            $table->tinyInteger('noti_email_frequency')->comment = 'in days';
            $table->tinyInteger('noti_email_followup_frequency')->comment('0 : None 1 : Daily 2: weekly')->nullable();
            $table->tinyInteger('noti_mob_followup_frequency')->comment = '0 : None 1 : Daily 2: weekly';
            $table->tinyInteger('status')->comment = "0-inactive,1-active,2-deleted,3-expired_account"; 
			$table->dateTime('account_exp')->nullable();
            $table->tinyInteger('is_expired')->default(0)->comment('0 - not expired 1- expired');
            $table->boolean('verified')->default(false);
            $table->string('token')->nullable();
            $table->rememberToken();
            $table->tinyInteger('is_need_change_pass')->comment('1 - creeated by other and need to change password  ')->nullable();
            
            $table->timestamps();
            $table->timestamp('last_login')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
