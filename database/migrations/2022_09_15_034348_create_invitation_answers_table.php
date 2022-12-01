<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitation_answers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('invitations_answers_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invitation_answer_id', 'inv_answ_id_foreign')->index()->constrained()->cascadeOnDelete();
            $table->string('locale')->index();

            $table->string('name');

            $table->unique(['invitation_answer_id', 'locale'], 'inv_answ_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations_answers_translations');
        Schema::dropIfExists('invitation_answers');
    }
}
