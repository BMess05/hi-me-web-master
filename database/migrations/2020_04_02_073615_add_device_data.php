<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('device_data', function (Blueprint $table) {
             $table->bigIncrements('id');
             $table->bigInteger('user_id');
             $table->tinyInteger('device_type')->default('1')->comment('1: ios; 2: android;')->nullable();
             $table->string('device_id')->nullable();
             $table->string('device_token')->nullable();
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
         Schema::dropIfExists('device_data');
     }
}
