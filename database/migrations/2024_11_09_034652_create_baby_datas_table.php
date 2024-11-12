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
        Schema::create('baby_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('baby__incubator_id');
            $table->foreign('baby__incubator_id')->references('id')->on('babies_incubators');
            $table->tinyInteger('oxigen');
            $table->tinyInteger('heart_rate');
            $table->decimal('temperature');
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
        Schema::dropIfExists('baby_datas');
    }
};
