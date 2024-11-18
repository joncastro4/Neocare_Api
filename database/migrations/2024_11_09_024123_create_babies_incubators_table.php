<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('babies_incubators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baby_id')->constrained('babies')->onDelete('cascade');
            $table->foreignId('incubator_id')->constrained('incubators')->onDelete('cascade');
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
