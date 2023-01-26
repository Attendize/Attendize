<?php

use App\Models\Organiser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AddOrganiserToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('organiser_id')->after('account_id')->default(0);
        });

        $allUsers = User::all();
        Log::debug(sprintf("Found %d users to retrofit organiser roles to", $allUsers->count()));

        if ($allUsers->count() > 0) {
            $allUsers->each(function($user) {
                /** @var \App\Models\User $user */
                if ($user->organiser_id === 0) {
                    // We are defaulting to the first organiser in the user's account
                    $organiser = Organiser::where('account_id', $user->account_id)->first();
                    Log::debug(sprintf("Assigning organiser [ID:%d] to legacy user [ID:%d]", $organiser->id, $user->id));
                    $user->update(['organiser_id' => $organiser->id]);
                }
            });
        } else {
            Log::debug('No users could be found that needed to be updated with organiser identifiers');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('organiser_id');
        });
    }
}
