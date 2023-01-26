<?php

use App\Models\Account;
use App\Models\Event;
use App\Models\Organiser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsLocal extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->out("<info>Seeding account</info>");
        $account = factory(Account::class)->create([
            'name' => 'Local Integration Test Account',
            'timezone_id' => 32, // London
            'currency_id' => 3, // Pound
        ]);

        $this->out("<info>Seeding account payment test details</info>");
        DB::table('account_payment_gateways')->insert([
            'account_id' => $account->id,
            'payment_gateway_id' => 2, // Dummy payment details
            'config' => '{"apiKey":"","publishableKey":""}',
        ]);

        // Add organiser
        $this->out("<info>Seeding Organiser</info>");
        $organiserOne = factory(Organiser::class)->create([
            'account_id' => $account->id,
            'name' => 'Test Organiser 1',
            'charge_tax' => false,
            'tax_name' => '',
            'tax_value' => 0.00
        ]);

        // Add organiser
        $this->out("<info>Seeding Organiser</info>");
        $organiserTwo = factory(Organiser::class)->create([
            'account_id' => $account->id,
            'name' => 'Test Organiser 2',
            'charge_tax' => false,
            'tax_name' => '',
            'tax_value' => 0.00
        ]);

        $this->out("<info>Seeding Users</info>");
        // NOTE:
        // A Super user can see all the organisers
        /** @var $superUser App\Models\User */
        $superUser = factory(User::class)->create([
            'account_id' => $account->id,
            'organiser_id' => $organiserOne->id,
            'email' => 'super@test.com',
            'password' => Hash::make('pass'),
            'is_parent' => true, // Top level user
            'is_registered' => true,
            'is_confirmed' => true,
        ]);

        $superUser->assignRole(Role::findByName('super admin'));
        $superUser->givePermissionTo($superUser->getAllPermissions());

        // NOTE:
        // An Organiser user can see their organiser and all events
        // belonging to that organiser
        $normalUserOne = factory(User::class)->create([
            'account_id' => $account->id,
            'organiser_id' => $organiserOne->id,
            'email' => 'user1@test.com',
            'password' => Hash::make('pass'),
            'is_parent' => true, // Top level user
            'is_registered' => true,
            'is_confirmed' => true,
        ]);

        $normalUserOne->assignRole(Role::findByName('user'));
        $normalUserOne->givePermissionTo($normalUserOne->getAllPermissions());
        $normalUserOne->givePermissionTo(Permission::findByName('manage events', 'web'));

        $normalUserTwo = factory(User::class)->create([
            'account_id' => $account->id,
            'organiser_id' => $organiserTwo->id,
            'email' => 'user2@test.com',
            'password' => Hash::make('pass'),
            'is_parent' => true, // Top level user
            'is_registered' => true,
            'is_confirmed' => true,
        ]);

        $normalUserTwo->assignRole(Role::findByName('user'));
        $normalUserTwo->givePermissionTo($normalUserTwo->getAllPermissions());

        // NOTE:
        // An Attendee user can only see the attendee check in screens
        $checkInUser = factory(User::class)->create([
            'account_id' => $account->id,
            'organiser_id' => $organiserOne->id,
            'email' => 'checkin@test.com',
            'password' => Hash::make('pass'),
            'is_parent' => true, // Top level user
            'is_registered' => true,
            'is_confirmed' => true,
        ]);

        $checkInUser->assignRole(Role::findByName('attendee check in'));
        $checkInUser->givePermissionTo($checkInUser->getAllPermissions());

        // Events
        $this->out("<info>Seeding event</info>");
        $eventOne = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $normalUserOne->id,
            'organiser_id' => $organiserOne->id,
            'title' => 'Event 1',
            'is_live' => true,
        ]);

        $this->out("<info>Seeding event</info>");
        $eventTwo = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $normalUserOne->id,
            'organiser_id' => $organiserOne->id,
            'title' => 'Event 2',
            'is_live' => true,
        ]);

        $this->out("<info>Seeding event</info>");
        $eventThree = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $normalUserTwo->id,
            'organiser_id' => $organiserTwo->id,
            'title' => 'Event 3',
            'is_live' => true,
        ]);

        $this->out("<info>Seeding event</info>");
        $eventFour = factory(Event::class)->create([
            'account_id' => $account->id,
            'user_id' => $normalUserTwo->id,
            'organiser_id' => $organiserTwo->id,
            'title' => 'Event 4',
            'is_live' => true,
        ]);

        // Write final part about test user logins
        $this->command->alert(
            sprintf("Roles/Permissions Demo Seed Finished"
                . "\n\nYou can log in with the Super User using"
                . "\n\nu: %s\np: %s\n\n", $superUser->email, 'pass')
        );
    }

    /**
     * @param string $message
     */
    private function out($message)
    {
        $this->command->getOutput()->writeln("<info>$message</info>");
    }
}
