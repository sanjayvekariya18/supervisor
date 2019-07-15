<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachineDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->string('machine_id',20);
            $table->string('machine_number',20)->nullable();
            $table->string('shift',100)->nullable();
            $table->bigInteger('stitches')->nullable();
            $table->mediumInteger('thred_break')->nullable();
            $table->mediumInteger('rpm')->nullable();
            $table->mediumInteger('max_rpm')->nullable();
            $table->mediumInteger('stop_time')->nullable();
            $table->mediumInteger('worker_id')->nullable();
            $table->mediumInteger('working_head')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_data');
    }
}
