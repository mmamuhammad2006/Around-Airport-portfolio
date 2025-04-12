<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('_id')->unique();
            $table->string('name');
            $table->string('iata_code')->nullable();
            $table->string('icao_code')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('size_airline')->nullable();
            $table->string('call_sign')->nullable();
            $table->string('status')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airlines');
    }
}
