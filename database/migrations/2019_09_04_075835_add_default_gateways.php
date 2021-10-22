<?php

use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AddDefaultGateways extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $paypal = PaymentGateway::where('name', 'PayPal_Express')->first();

        //Log::info('Removing?');
        if ($paypal) {
            // Set the Paypal gateway relationship to null to avoid errors when removing it
            Order::where('payment_gateway_id', $paypal->id)->update(['payment_gateway_id' => null]);
            Log::info('Removed');

            $paypal->delete();
        }

        Schema::table('payment_gateways', function ($table) {
            $table->boolean('default')->default(0);
            $table->string('admin_blade_template', 150)->default('');
            $table->string('checkout_blade_template', 150)->default('');
        });

        Schema::table('orders', function ($table) {
            $table->string('payment_intent', 150)->default('');
        });

        $stripe = DB::table('payment_gateways')->where('provider_name', 'Stripe')->first();

        if ($stripe) {
            $stripe->update([
                'admin_blade_template'    => 'ManageAccount.Partials.Stripe',
                'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripe',
            ]);
        } else {
            DB::table('payment_gateways')->insert(
                [
                    'provider_name'           => 'Stripe',
                    'provider_url'            => 'https://www.stripe.com',
                    'is_on_site'              => 1,
                    'can_refund'              => 1,
                    'name'                    => 'Stripe',
                    'default'                 => 0,
                    'admin_blade_template'    => 'ManageAccount.Partials.Stripe',
                    'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripe',
                ]
            );
        }

        $dummyGateway = DB::table('payment_gateways')->where('name', '=', 'Dummy')->first();

        if ($dummyGateway === null) {
            // user doesn't exist
            DB::table('payment_gateways')->insert(
                [
                    'provider_name'           => 'Dummy/Test Gateway',
                    'provider_url'            => 'none',
                    'is_on_site'              => 1,
                    'can_refund'              => 1,
                    'name'                    => 'Dummy',
                    'default'                 => 0,
                    'admin_blade_template'    => '',
                    'checkout_blade_template' => 'Public.ViewEvent.Partials.Dummy',
                ]
            );
        }

        $stripePaymentIntents = DB::table('payment_gateways')
            ->where('name', '=', 'Stripe\PaymentIntents')->first();

        if ($stripePaymentIntents === null) {
            DB::table('payment_gateways')->insert(
                [
                    'provider_name'           => 'Stripe SCA',
                    'provider_url'            => 'https://www.stripe.com',
                    'is_on_site'              => 0,
                    'can_refund'              => 1,
                    'name'                    => 'Stripe\PaymentIntents',
                    'default'                 => 0,
                    'admin_blade_template'    => 'ManageAccount.Partials.StripeSCA',
                    'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripeSCA',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
