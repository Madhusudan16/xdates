<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserCreditCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_credit_card', function (Blueprint $table) {
            $table->increments('id');
            $table->string('billing_first_name',255);
            $table->string('billing_last_name',255);
            $table->string('card_no',20);
            $table->string('expiry_date',20);
            $table->string('address_line_1',255);
            $table->string('address_line_2',255);
            $table->string('city',255);
            $table->string('state',255);
            $table->string('country',50);
            $table->string('zip_code',20);
            $table->string('auth_card_id',255);
            $table->string('customer_payment_profile_id',20)->nullable();
            $table->integer('user_id');
            $table->dateTime('next_bill_date');
            $table->tinyInteger('status')->comment('0-inactive,1-active,2-deleted');
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
