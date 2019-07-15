<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // rpm_cal
        // shift_change

        Schema::create('machines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('machine_id',20)->unique();;
            $table->string('machine_number',20)->nullable();
            $table->string('machine_name',100);
            $table->smallInteger('status_id',false)->default(0);
            $table->smallInteger('group_id',false)->default(0);
            $table->string('cust_id',20);
            $table->smallInteger('shift',false)->default(0);
            $table->bigInteger('stitches')->nullable();
            $table->mediumInteger('thred_break')->nullable();
            $table->mediumInteger('rpm')->default(0);
            $table->mediumInteger('max_rpm')->default(0);
            $table->mediumInteger('stop_time')->nullable();
            $table->mediumInteger('worker_id')->nullable();
            $table->mediumInteger('day_worker_id')->nullable();
            $table->mediumInteger('night_worker_id')->nullable();
            $table->mediumInteger('total_head')->nullable();
            $table->mediumInteger('working_head')->nullable();
            $table->smallInteger('is_buzzed',false)->default(0);
            $table->smallInteger('buzzed_ack',false)->default(0);
            $table->time('default_buzzer_time')->default('00:00:00');
            $table->text('buzzer_time')->nullable();
            $table->dateTime('last_sync')->nullable();
            $table->string('remark',255)->nullable();
            $table->string('inactivate_reason',100)->nullable();
            $table->time('day_shift')->default('09:00:00');
            $table->time('night_shift')->default('21:00:00');
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machines');
    }
}
