<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id()->index();
            $table->timestamps();
        });

        Schema::create('invitations_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invitation_id')->index()->constrained()->cascadeOnDelete();
            $table->string('locale')->index();

            $table->string('name');

            $table->unique(['invitation_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations_translations');
        Schema::dropIfExists('invitations');
    }
}
