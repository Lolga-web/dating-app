<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsInUsersInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_invitations', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreignId('answer_id')->nullable()->after('invitation_id')->constrained('invitation_answers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_invitations', function (Blueprint $table) {
            $table->boolean('status')->nullable()->after('invitation_id');
            $table->dropForeign(['answer_id']);
            $table->dropColumn('answer_id');
        });
    }
}
