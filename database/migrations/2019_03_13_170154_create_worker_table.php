<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('worker_id',20);
            $table->string('cust_id',20);
            $table->string('first_name',100);
            $table->string('last_name',100)->nullable();
            $table->string('contact_number_1',20)->unique();
            $table->string('contact_number_2',20)->unique()->nullable();
            $table->string('aadhar_card_number',12)->unique()->nullable();
            $table->string('reference_by',100)->nullable();
            $table->smallInteger('shift',false)->default(0);
            $table->string('salary',10)->nullable()->default(0);
            $table->smallInteger('status_id',false)->default(0);
            $table->string('inactivate_reason',100)->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->softDeletes();  
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
        Schema::dropIfExists('workers');
    }
}
