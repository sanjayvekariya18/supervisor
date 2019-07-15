<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id',20)->unique();
            $table->string('parent_id',20)->nullable();
            $table->string('first_name',100);
            $table->string('last_name',100)->nullable();
            $table->string('contact_number_1',20)->unique();
            $table->string('contact_number_2',20)->nullable();
            $table->smallInteger('sms_balance',false)->default(0);
            $table->string('sms_notification_numbers',255)->nullable();
            $table->smallInteger('sms_notification_status',false)->default(0);
            $table->string('whatsapp_notification_numbers',255)->nullable();
            $table->smallInteger('whatsapp_notification_status',false)->default(0);
            $table->string('email',100)->unique()->nullable();
            $table->string('username',50)->unique();
            $table->string('password');
            $table->smallInteger('status_id',false)->default(0);
            $table->string('inactivate_reason',100)->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->time('day_shift')->default('09:00:00');
            $table->time('night_shift')->default('21:00:00');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
