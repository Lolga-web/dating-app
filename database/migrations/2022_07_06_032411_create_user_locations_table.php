<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->ipAddress('ip');
            $table->string('iso_code',6)->nullable();
            $table->string('country',64)->nullable();
            $table->string('city',64)->nullable();
            $table->string('state',6)->nullable();
            $table->string('state_name',64)->nullable();
            $table->string('postal_code', 6)->nullable();
            $table->string('lat', 20)->nullable();
            $table->string('lon', 20)->nullable();
            $table->string('timezone',64)->nullable();
            $table->string('continent',10)->nullable();
            $table->string('currency', 3)->nullable();
            $table->boolean('default')->nullable()->default(false);

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
        Schema::dropIfExists('user_locations');
    }
}
