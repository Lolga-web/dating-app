<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->foreignId('invitation_id')->index()->constrained('invitations')->cascadeOnDelete();
            $table->boolean('status')->nullable();

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
        Schema::dropIfExists('users_invitations');
    }
}
