<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('baby_datas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baby_incubator_id')->constrained('babies_incubators')->onDelete('cascade');
            $table->tinyInteger('oxigen');
            $table->tinyInteger('heart_rate');
            $table->decimal('temperature');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('baby_datas');
    }
};
