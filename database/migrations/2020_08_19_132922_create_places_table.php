<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('airport_id');
            $table->foreign('airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('_id');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('distance_text')->nullable();
            $table->float('distance_value')->nullable();
            $table->json('all_travel_mode_distance')->nullable();
            $table->string('google_place_id')->nullable();
            $table->string('google_latitude')->nullable();
            $table->string('google_longitude')->nullable();
            $table->float('google_rating')->nullable();
            $table->string('google_formatted_address')->nullable();
            $table->json('data');
            $table->json('google_data')->nullable();
            $table->timestamp('expire_at');
            $table->timestamps();

            $table->unique(['_id', 'airport_id', 'category_id']);
            $table->unique(['google_place_id', 'airport_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
}
