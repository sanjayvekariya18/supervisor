<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->string('machine_id',10);
            $table->integer('min_stitches');
            $table->mediumInteger('min_stitches_bonus');
            $table->integer('after_min_per_stitches');
            $table->mediumInteger('after_min_per_stitches_bonus');
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
        Schema::dropIfExists('bonuses');
    }
}
