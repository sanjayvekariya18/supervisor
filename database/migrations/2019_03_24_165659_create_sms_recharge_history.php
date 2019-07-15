<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsRechargeHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_recharge_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->string('transaction_type',20);
            $table->smallInteger('sms_volume',false)->default(0);
            $table->string('note',255);
            $table->smallInteger('activity_by',false);
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
        Schema::dropIfExists('sms_recharge_histories');
    }
}
