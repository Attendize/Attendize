<?php

use App\Models\Attendee;
use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AttendeeRefFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->integer('reference_index')->default(0);
        });

        $attendees = Attendee::all();

        foreach ($attendees as $attendee) {
            $attendee->reference_index = explode('-', $attendee->reference)[1];
            $attendee->save();
        }

        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->string('reference');
            $table->dropColumn('reference_index');
        });

        $orders = Order::all();
        foreach ($orders as $order) {
            $attendee_count = 0;

            foreach ($order->attendees as $attendee) {
                $attendee->reference = $order->order_reference.'-'.++$attendee_count;
                $attendee->save();
            }
        }
    }
}
