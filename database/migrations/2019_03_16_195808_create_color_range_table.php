<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorRangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cust_id',20);
            $table->bigInteger('from_stitches',false)->default(0);
            $table->bigInteger('to_stitches',false)->default(0);
            $table->string('color_code',20)->nullable();
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
        Schema::dropIfExists('color_ranges');
    }
}
