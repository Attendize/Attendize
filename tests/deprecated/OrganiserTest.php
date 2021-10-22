<?php

use App\Models\Organiser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class OrganiserTest extends TestCase
{
    /**
     * @group passing
     */
    public function test_create_organiser_is_successful_when_charge_tax_is_no()
    {
        $email = $this->faker->email;

        $this->actingAs($this->test_user)
            ->visit(route('showCreateOrganiser'))
            ->type($this->faker->name, 'name')
            ->type($email, 'email')
            ->type('No', 'charge_tax')
            ->press('Create Organiser')
            ->seeJson([
                'status' => 'success',
            ]);

        //get the most recently created organiser from database
        $this->organiser = Organiser::where('email', '=', $email)->orderBy('created_at', 'desc')->first();
        //check the charge tax flag is 0
        $this->assertEquals($this->organiser->charge_tax, 0);
    }

    /**
     * @group passing
     */
    public function test_create_organiser_is_successful_when_charge_tax_is_yes()
    {
        $email = $this->faker->email;

        $this->actingAs($this->test_user)
            ->visit(route('showCreateOrganiser'))
            ->type($this->faker->name, 'name')
            ->type($email, 'email')
            ->type('organisers', 'tax_name')
            ->type(12323, 'tax_id')
            ->type(15, 'tax_value')
            ->type('Yes', 'charge_tax')
            ->press('Create Organiser')
            ->seeJson([
                'status' => 'success',
            ]);

        //get the most recently created organiser from database
        $this->organiser = Organiser::where('email', '=', $email)->orderBy('created_at', 'desc')->first();
        //check the charge tax flag is 1
        $this->assertEquals($this->organiser->charge_tax, 1);
    }

    /**
     * @group passing
     */
    public function test_create_organiser_fails_when_organiser_details_missing()
    {
        $this->actingAs($this->test_user)
            ->visit(route('showCreateOrganiser'))
            ->type('', 'name')
            ->type('', 'email')
            ->type('No', 'charge_tax')
            ->press('Create Organiser')
            ->seeJson([
                'status' => 'error',
            ]);
    }
}
