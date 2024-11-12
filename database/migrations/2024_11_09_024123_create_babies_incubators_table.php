<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('babies_incubators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('baby_id');
            $table->foreign('baby_id')->references('id')->on('babies');
            $table->unsignedBigInteger('incubator_id');
            $table->foreign('incubator_id')->references('id')->on('incubators');
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
        Schema::dropIfExists('babies_incubators');
    }
};
