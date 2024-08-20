<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrialExtendRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trial_extend_request', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id',20);
            $table->string('requester_id',20);
            $table->string('token',100)->nullable();
            $table->tinyInteger('is_approved')->default(0)->comment('0-pending | 1 - approved');
            $table->date('trial_end_date')->nullable();
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
        Schema::drop('trial_extend_request');
    }
}
