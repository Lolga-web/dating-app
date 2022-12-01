<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInQuestionnaireTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('about_me');
            $table->string('expectations')->nullable()->after('purpose');
            $table->string('search_country')->nullable()->after('search_age_max');
            $table->string('search_city')->nullable()->after('search_country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire', function (Blueprint $table) {
            $table->dropColumn('nationality');
            $table->dropColumn('expectations');
            $table->dropColumn('search_country');
            $table->dropColumn('search_city');
        });
    }
}
