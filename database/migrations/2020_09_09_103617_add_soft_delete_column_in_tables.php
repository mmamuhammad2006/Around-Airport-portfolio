<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteColumnInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airports', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('places', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airports', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
