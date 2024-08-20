<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('plan_id',10);
            $table->string('plan_name',20);
            $table->string('plan_amount',20);
            $table->string('paid_amount',20);
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
        Schema::drop('invoice_item');
    }
}
