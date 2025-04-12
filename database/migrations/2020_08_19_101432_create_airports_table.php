<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('country_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('city_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('timezone')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('google_place_id')->nullable();
            $table->string('google_state_long_name')->nullable();
            $table->string('google_state_short_name')->nullable();
            $table->string('google_latitude')->nullable();
            $table->string('google_longitude')->nullable();
            $table->float('google_rating')->nullable();
            $table->string('google_formatted_address')->nullable();
            $table->json('data')->nullable();
            $table->json('google_data')->nullable();
            $table->timestamp('expire_at');
            $table->timestamps();

            $table->unique(['google_place_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airports');
    }
}
