<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->boolean('invisible')->default(false);
            $table->boolean('likes_notifications')->default(true);
            $table->boolean('matches_notifications')->default(true);
            $table->boolean('invitations_notifications')->default(true);
            $table->boolean('messages_notifications')->default(true);
            $table->boolean('guests_notifications')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('invisible');
            $table->dropColumn('likes_notifications');
            $table->dropColumn('matches_notifications');
            $table->dropColumn('invitations_notifications');
            $table->dropColumn('messages_notifications');
            $table->dropColumn('guests_notifications');
        });
    }
}
