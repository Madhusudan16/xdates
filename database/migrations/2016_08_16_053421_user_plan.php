<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id');
            $table->string('plan_name',20);
            $table->string('plan_amount',20);
            $table->integer('user_id');
            $table->string('n_allowed_users',20);
            $table->date('plan_start_date');
            $table->date('plan_end_date',20)->nullable();
            $table->decimal('plan_pay_amount',10,2);
            $table->string('invoice_no',20);
            $table->integer('trans_id')->nullable();
            $table->tinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_plan');
    }
}
