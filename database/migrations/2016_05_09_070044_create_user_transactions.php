<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trans_id');
            $table->integer('plan_id');
            $table->decimal('amount', 10, 2);
            $table->integer('user_id');
            $table->string('plan_name')->nullable();
            
            $table->string('trans_auth_code')->nullable();
            $table->text('trans_details')->nullable();
            $table->tinyInteger('status')->comment = "0-inactive,1-active,2-deleted"; 
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
        Schema::drop('user_transactions');
    }
}
