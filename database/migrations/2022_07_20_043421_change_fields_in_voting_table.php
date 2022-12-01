<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsInVotingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voting', function (Blueprint $table) {
            $table->dropForeign(['media_id']);
            $table->dropColumn('media_id');
            $table->foreignId('loser_photo')->after('id')->constrained('media')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('winning_photo')->after('id')->constrained('media')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voting', function (Blueprint $table) {
            $table->boolean('status')->after('voter_id');
            $table->dropForeign(['loser_photo']);
            $table->dropColumn('loser_photo');
            $table->dropForeign(['winning_photo']);
            $table->dropColumn('winning_photo');
            $table->foreignId('media_id')->after('id')->nullable()->index()->constrained()->cascadeOnDelete();
        });
    }
}
