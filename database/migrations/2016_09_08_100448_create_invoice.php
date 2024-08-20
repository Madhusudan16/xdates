<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('amount',20);
            $table->integer('plan_id')->nullable();
            $table->string('discount',10);
            $table->string('paid_amount',20);
            $table->date('pay_date');
            $table->date('bill_date');
            $table->string('to_address',255);
            $table->string('from_address',255);
            $table->integer('owner_id');
            $table->tinyInteger('status')->default(0)->comment("0-Inactive 1- active 2-deleted");
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
        Schema::drop('invoices');
    }
}
