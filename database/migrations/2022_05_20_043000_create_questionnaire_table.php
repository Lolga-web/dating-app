<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaireTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->string('purpose')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('hair_length')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('kids')->nullable();
            $table->string('education')->nullable();
            $table->string('occupation')->nullable();
            $table->string('about_me')->nullable();
            $table->integer('search_age_min')->nullable();
            $table->integer('search_age_max')->nullable();
            $table->json('socials')->nullable();
            $table->json('hobby')->nullable();
            $table->json('sport')->nullable();
            $table->string('evening_time')->nullable();
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
        Schema::dropIfExists('questionnaire');
    }
}
