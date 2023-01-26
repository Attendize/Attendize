<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        $this->call(CountriesSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(OrderStatusSeeder::class);
        $this->call(PaymentGatewaySeeder::class);
        $this->call(QuestionTypesSeeder::class);
        $this->call(TicketStatusSeeder::class);
        $this->call(TimezoneSeeder::class);
    }
}
