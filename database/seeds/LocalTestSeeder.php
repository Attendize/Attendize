<?php

use App\Models\Account;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventAccessCodes;
use App\Models\EventStats;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organiser;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class LocalTestSeeder extends Seeder
{
    /**
     * Run the seeds to allow for local database test cases.
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Setup Test account
        $this->out('<info>Setting up basic settings</info>');
        $account = $this->setupTestAccountWithTestStripeDetails();
        $user = $this->setupTestAttendizeUserWithLoginDetails($account);

        $this->setupNonTaxOrganisation($account, $user);
        $this->setupTaxOrganisation($account, $user); // Adds VAT @15% per transaction
        $this->setupTaxOrganisationWithFees($account, $user);

        // Write final part about test user logins
        $this->command->alert(
            sprintf('Local Test Seed Finished'
                ."\n\nYou can log in with the Test User using"
                ."\n\nu: %s\np: %s\n\n", $user->email, 'pass')
        );
    }

    /**
     * @param string $message
     */
    protected function out($message)
    {
        $this->command->getOutput()->writeln($message);
    }

    /**
     * @param $account
     * @param $user
     */
    protected function setupNonTaxOrganisation($account, $user)
    {
        // Organiser with no tax (organisers)
        $this->out('<info>Seeding Organiser (no tax)</info>');
        $organiserNoTax = factory(Organiser::class)->create([
            'account_id' => $account->id,
            'name' => 'Test Organiser (No Tax)',
            'charge_tax' => false,
            'tax_name' => '',
            'tax_value' => 0.00,
        ]);

        // Event (events)
        $this->out('<info>Seeding event</info>');
        $event = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'organiser_id' => $organiserNoTax->id,
            'title' => 'Local Orchid Show',
            'is_live' => true,
        ]);

        // Setup event access codes to allow testing hidden code functionality on the tickets public page
        $eventAccessCode = $this->setupEventAccessCodeForHiddenTickets($event);

        // Setup two tickets, one visible and one hidden
        $this->out('<info>Seeding visible ticket</info>');
        $visibleTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
            'edited_by_user_id' => $user->id,
            'account_id' => $account->id,
            'order_id' => null, // We'll create the orders on these later
            'event_id' => $event->id,
            'title' => 'Visible Ticket',
            'price' => 100.00,
            'is_hidden' => false,
        ]);

        $this->out('<info>Seeding hidden ticket</info>');
        $hiddenTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
            'edited_by_user_id' => $user->id,
            'account_id' => $account->id,
            'order_id' => null, // We'll create the orders on these later
            'event_id' => $event->id,
            'title' => 'Hidden Ticket',
            'price' => 100.00,
            'is_hidden' => true,
        ]);

        // Attach unlock code to hidden ticket
        $this->out('<info>Attaching access code to hidden ticket</info>');
        $hiddenTicket->event_access_codes()->attach($eventAccessCode);

        // Event Stats
        $this->out('<info>Seeding Event Stats</info>');
        factory(EventStats::class)->create([
            'date' => Carbon::now()->format('Y-m-d'),
            'views' => 0,
            'unique_views' => 0,
            'tickets_sold' => 6,
            'sales_volume' => 600.00,
            'event_id' => $event->id,
        ]);

        // Orders (order_items, ticket_order) as normie
        $this->out('<info>Seeding single attendee order</info>');
        $singleAttendeeOrder = factory(Order::class)->create([
            'account_id' => $account->id,
            'order_status_id' => 1, // Completed Order
            'discount' => 0.00,
            'booking_fee' => 0.00,
            'organiser_booking_fee' => 0.00,
            'amount' => 100.00,
            'event_id' => $event->id,
            'is_payment_received' => true,
        ]);

        $visibleTicket->order_id = $singleAttendeeOrder->id;
        $visibleTicket->quantity_sold = 1;
        $visibleTicket->sales_volume = 100.00;
        $visibleTicket->save();

        $this->out('<info>Attaching visible ticket to single attendee order</info>');
        $singleAttendeeOrder->tickets()->attach($visibleTicket);

        $this->out('<info>Seeding single attendee order item</info>');
        factory(OrderItem::class)->create([
            'title' => $visibleTicket->title,
            'quantity' => 1,
            'unit_price' => 100.00,
            'unit_booking_fee' => 0.00,
            'order_id' => $singleAttendeeOrder->id,
        ]);

        $this->out('<info>Seeding single attendee</info>');
        factory(Attendee::class)->create([
            'order_id' => $singleAttendeeOrder->id,
            'event_id' => $event->id,
            'ticket_id' => $visibleTicket->id,
            'account_id' => $account->id,
        ]);

        $this->out('<info>Seeding multiple attendees order</info>');
        $multipleAttendeeOrder = factory(Order::class)->create([
            'account_id' => $account->id,
            'order_status_id' => 1, // Completed Order
            'discount' => 0.00,
            'booking_fee' => 0.00,
            'organiser_booking_fee' => 0.00,
            'amount' => 500.00,
            'event_id' => $event->id,
            'is_payment_received' => true,
        ]);

        $hiddenTicket->order_id = $multipleAttendeeOrder->id;
        $hiddenTicket->quantity_sold = 5;
        $hiddenTicket->sales_volume = 500.00;
        $hiddenTicket->save();

        $this->out('<info>Attaching hidden ticket to multiple attendees order</info>');
        $multipleAttendeeOrder->tickets()->attach($hiddenTicket);

        $this->out('<info>Seeding multiple attendees order item</info>');
        factory(OrderItem::class)->create([
            'title' => $visibleTicket->title,
            'quantity' => 5,
            'unit_price' => 100.00,
            'unit_booking_fee' => 0.00,
            'order_id' => $multipleAttendeeOrder->id,
        ]);

        $this->out('<info>Seeding multiple attendees</info>');
        factory(Attendee::class, 5)->create([
            'order_id' => $multipleAttendeeOrder->id,
            'event_id' => $event->id,
            'ticket_id' => $hiddenTicket->id,
            'account_id' => $account->id,
        ]);
    }

    /**
     * @param $account
     * @param $user
     */
    protected function setupTaxOrganisation($account, $user)
    {
        // Organiser with no tax (organisers)
        $this->out('<info>Seeding Organiser (with tax)</info>');
        $organiserTax = factory(Organiser::class)->create([
            'account_id' => $account->id,
            'name' => 'Test Organiser (with tax)',
            'charge_tax' => true,
            'tax_name' => 'VAT',
            'tax_value' => 15.00,
        ]);

        // Event (events)
        $this->out('<info>Seeding event</info>');
        $event = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'organiser_id' => $organiserTax->id,
            'title' => 'Local Bonsai Show',
            'is_live' => true,
        ]);

        $eventAccessCode = $this->setupEventAccessCodeForHiddenTickets($event);

        // Setup two tickets, one visible and one hidden
        $this->out('<info>Seeding visible ticket</info>');
        $visibleTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
            'edited_by_user_id' => $user->id,
            'account_id' => $account->id,
            'order_id' => null, // We'll create the orders on these later
            'event_id' => $event->id,
            'title' => 'Visible Ticket',
            'price' => 100.00,
            'is_hidden' => false,
        ]);

        $this->out('<info>Seeding hidden ticket</info>');
        $hiddenTicket = factory(Ticket::class)->create([
            'user_id' => $user->id,
            'edited_by_user_id' => $user->id,
            'account_id' => $account->id,
            'order_id' => null, // We'll create the orders on these later
            'event_id' => $event->id,
            'title' => 'Hidden Ticket',
            'price' => 50.00,
            'is_hidden' => true,
        ]);

        // Attach unlock code to hidden ticket
        $this->out('<info>Attaching access code to hidden ticket</info>');
        $hiddenTicket->event_access_codes()->attach($eventAccessCode);

        // Event Stats
        $this->out('<info>Seeding Event Stats</info>');
        factory(EventStats::class)->create([
            'date' => Carbon::now()->format('Y-m-d'),
            'views' => 0,
            'unique_views' => 0,
            'tickets_sold' => 6,
            'sales_volume' => 350.00,
            'event_id' => $event->id,
        ]);

        // Orders (order_items, ticket_order) as normie
        $this->out('<info>Seeding single attendee order</info>');
        $singleAttendeeOrder = factory(Order::class)->create([
            'account_id' => $account->id,
            'order_status_id' => 1, // Completed Order
            'discount' => 0.00,
            'booking_fee' => 0.00,
            'organiser_booking_fee' => 0.00,
            'amount' => 100.00,
            'event_id' => $event->id,
            'is_payment_received' => true,
            'taxamt' => 15.00,
        ]);

        $visibleTicket->order_id = $singleAttendeeOrder->id;
        $visibleTicket->quantity_sold = 1;
        $visibleTicket->sales_volume = 100.00;
        $visibleTicket->save();

        $this->out('<info>Attaching visible ticket to single attendee order</info>');
        $singleAttendeeOrder->tickets()->attach($visibleTicket);

        $this->out('<info>Seeding single attendee order item</info>');
        factory(OrderItem::class)->create([
            'title' => $visibleTicket->title,
            'quantity' => 1,
            'unit_price' => 100.00,
            'unit_booking_fee' => 0.00,
            'order_id' => $singleAttendeeOrder->id,
        ]);

        $this->out('<info>Seeding single attendee</info>');
        factory(Attendee::class)->create([
            'order_id' => $singleAttendeeOrder->id,
            'event_id' => $event->id,
            'ticket_id' => $visibleTicket->id,
            'account_id' => $account->id,
        ]);

        $this->out('<info>Seeding multiple attendees order</info>');
        $multipleAttendeeOrder = factory(Order::class)->create([
            'account_id' => $account->id,
            'order_status_id' => 1, // Completed Order
            'discount' => 0.00,
            'booking_fee' => 0.00,
            'organiser_booking_fee' => 0.00,
            'amount' => 250.00,
            'event_id' => $event->id,
            'is_payment_received' => true,
            'taxamt' => 37.5,
        ]);

        $hiddenTicket->order_id = $multipleAttendeeOrder->id;
        $hiddenTicket->quantity_sold = 5;
        $hiddenTicket->sales_volume = 250.00;
        $hiddenTicket->save();

        $this->out('<info>Attaching hidden ticket to multiple attendees order</info>');
        $multipleAttendeeOrder->tickets()->attach($hiddenTicket);

        $this->out('<info>Seeding multiple attendees order item</info>');
        factory(OrderItem::class)->create([
            'title' => $hiddenTicket->title,
            'quantity' => 5,
            'unit_price' => 50.00,
            'unit_booking_fee' => 0.00,
            'order_id' => $multipleAttendeeOrder->id,
        ]);

        $this->out('<info>Seeding multiple attendees</info>');
        factory(Attendee::class, 5)->create([
            'order_id' => $multipleAttendeeOrder->id,
            'event_id' => $event->id,
            'ticket_id' => $hiddenTicket->id,
            'account_id' => $account->id,
        ]);
    }

    protected function setupTaxOrganisationWithFees($account, $user)
    {
        // Organiser with tax and fees (organisers)
        $this->out('<info>Seeding Organiser (with tax and fees)</info>');
        $organiserTaxAndFees = factory(Organiser::class)->create([
            'account_id' => $account->id,
            'name' => 'Test Organiser (with tax and fees)',
            'charge_tax' => true,
            'tax_name' => 'VAT',
            'tax_value' => 20.00,
        ]);

        // Event (events)
        $this->out('<info>Seeding event with percentage fees</info>');
        $eventWithPercentageFee = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'organiser_id' => $organiserTaxAndFees->id,
            'organiser_fee_percentage' => 5.0,
            'title' => 'Local Clivia Show',
            'is_live' => true,
        ]);

        // Setup tickets, single and multiple order
        $this->out('<info>Seeding ticket with organiser fee</info>');
        $ticketWithPercentageFee = factory(Ticket::class)->create([
            'user_id' => $user->id,
            'edited_by_user_id' => $user->id,
            'account_id' => $account->id,
            'order_id' => null, // We'll create the orders on these later
            'event_id' => $eventWithPercentageFee->id,
            'title' => 'Ticket with organiser fee',
            'price' => 100.00,
            'organiser_fees_volume' => 5.00,
            'is_hidden' => false,
        ]);

        // Event Stats
        $this->out('<info>Seeding Event Stats</info>');
        factory(EventStats::class)->create([
            'date' => Carbon::now()->format('Y-m-d'),
            'views' => 0,
            'unique_views' => 0,
            'tickets_sold' => 1,
            'sales_volume' => 100.00,
            'organiser_fees_volume' => 5.00,
            'event_id' => $eventWithPercentageFee->id,
        ]);

        // Orders (order_items, ticket_order) as normie
        $this->out('<info>Seeding single attendee order</info>');
        $singleAttendeeOrder = factory(Order::class)->create([
            'account_id' => $account->id,
            'order_status_id' => 1, // Completed Order
            'discount' => 0.00,
            'booking_fee' => 0.00,
            'organiser_booking_fee' => 5.00,
            'amount' => 100.00,
            'event_id' => $eventWithPercentageFee->id,
            'is_payment_received' => true,
            'taxamt' => 21.00,
        ]);

        $ticketWithPercentageFee->order_id = $singleAttendeeOrder->id;
        $ticketWithPercentageFee->quantity_sold = 1;
        $ticketWithPercentageFee->sales_volume = 100.00;
        $ticketWithPercentageFee->save();

        $this->out('<info>Attaching ticket with percentage fee to single attendee order</info>');
        $singleAttendeeOrder->tickets()->attach($ticketWithPercentageFee);

        $this->out('<info>Seeding single attendee order item</info>');
        factory(OrderItem::class)->create([
            'title' => $ticketWithPercentageFee->title,
            'quantity' => 1,
            'unit_price' => 100.00,
            'unit_booking_fee' => 5.00,
            'order_id' => $singleAttendeeOrder->id,
        ]);

        $this->out('<info>Seeding single attendee</info>');
        factory(Attendee::class)->create([
            'order_id' => $singleAttendeeOrder->id,
            'event_id' => $eventWithPercentageFee->id,
            'ticket_id' => $ticketWithPercentageFee->id,
            'account_id' => $account->id,
        ]);
    }

    /**
     * @param $account
     * @return mixed
     */
    protected function setupTestAttendizeUserWithLoginDetails($account)
    {
        $this->out('<info>Seeding User</info>');
        $user = factory(User::class)->create([
            'account_id' => $account->id,
            'email' => 'local@test.com',
            'password' => Hash::make('pass'),
            'is_parent' => true, // Top level user
            'is_registered' => true,
            'is_confirmed' => true,
        ]);

        return $user;
    }

    /**
     * @return mixed
     */
    protected function setupTestAccountWithTestStripeDetails()
    {
        $this->out('<info>Seeding account</info>');
        $account = factory(Account::class)->create([
            'name' => 'Local Integration Test Account',
            'timezone_id' => 38, // Brussels
            'currency_id' => 2, // Euro
        ]);

        // Set test stripe details
        $this->out('<info>Seeding account payment test details</info>');
        DB::table('account_payment_gateways')->insert([
            'account_id' => $account->id,
            'payment_gateway_id' => 1,
            'config' => '{"apiKey":"","publishableKey":""}',
        ]);

        return $account;
    }

    /**
     * @param $event
     * @return mixed
     */
    protected function setupEventAccessCodeForHiddenTickets($event)
    {
        // Setup event access codes to allow testing hidden code functionality on the tickets public page
        $this->out('<info>Seeding event access code</info>');

        return factory(EventAccessCodes::class)->create([
            'event_id' => $event->id,
            'code' => 'SHOWME',
        ]);
    }
}
