<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->integer('worker_id');
            $table->integer('machine_id');
            $table->integer('shift');
            $table->integer('stitches');
            $table->integer('bonus_amount');
            $table->date('bonus_date');
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
        Schema::dropIfExists('worker_bonuses');
    }
}
