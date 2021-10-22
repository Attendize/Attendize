<?php

use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dummyGateway = DB::table('payment_gateways')->where('name', '=', 'Dummy')->first();

        if ($dummyGateway === null) {
            // user doesn't exist
            DB::table('payment_gateways')->insert(
                [
                    'provider_name' => 'Dummy/Test Gateway',
                    'provider_url' => 'none',
                    'is_on_site' => 1,
                    'can_refund' => 1,
                    'name' => 'Dummy',
                    'default' => 0,
                    'admin_blade_template' => '',
                    'checkout_blade_template' => 'Public.ViewEvent.Partials.Dummy',
                ]
            );
        }

        $stripe = DB::table('payment_gateways')->where('name', '=', 'Stripe')->first();
        if ($stripe === null) {
            DB::table('payment_gateways')->insert(
                [
                    'name' => 'Stripe',
                    'provider_name' => 'Stripe',
                    'provider_url' => 'https://www.stripe.com',
                    'is_on_site' => 1,
                    'can_refund' => 1,
                    'default' => 0,
                    'admin_blade_template' => 'ManageAccount.Partials.Stripe',
                    'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripe',
                ]
            );
        }

        $stripePaymentIntents = DB::table('payment_gateways')->where('name', '=', 'Stripe\PaymentIntents')->first();
        if ($stripePaymentIntents === null) {
            DB::table('payment_gateways')->insert(
                [
                    'provider_name' => 'Stripe SCA',
                    'provider_url' => 'https://www.stripe.com',
                    'is_on_site' => 0,
                    'can_refund' => 1,
                    'name' => 'Stripe\PaymentIntents',
                    'default' => 0,
                    'admin_blade_template' => 'ManageAccount.Partials.StripeSCA',
                    'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripeSCA',
                ]
            );
        }
    }
}
