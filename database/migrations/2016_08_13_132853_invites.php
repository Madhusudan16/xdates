<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Invites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_user_id',50);
            $table->integer('owner_id')->default(0);
            $table->string('to_user_id',50)->nullable();
            $table->string('friend_email',255);
            $table->tinyInteger('status')->default(0)->comment('0 - Invited , 1-In-Trial , 2-Subscribed ,3-calcelled');
            $table->dateTime('invitation_accept_date')->nullable();
            $table->integer('trial_days')->comment('in days');
            $table->decimal('amount',10,2);
            $table->date('subscribe_date')->nullable();
            $table->date('cancelled_date')->nullable();
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
        Schema::drop('invites');
    }
}
