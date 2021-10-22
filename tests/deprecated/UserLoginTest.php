<?php

use App\Attendize\Utils;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserLoginTest extends TestCase
{
    /**
     * Test login page is successful
     *
     * @return void
     */
    public function test_login_is_successful()
    {
        $this->visit(route('login'))
            ->type($this->test_user_email, 'email')
            ->type($this->test_user_password, 'password')
            ->press('Login')
            ->seePageIs(route('showCreateOrganiser', ['first_run' => '1']));
    }

    /**
     * Test login page is unsuccessful with wrong password
     *
     * @return void
     */
    public function test_login_is_unsuccessful_with_wrong_password()
    {
        $this->visit(route('login'))
            ->type($this->test_user_email, 'email')
            ->type('incorrect_password', 'password')
            ->press('Login')
            ->seePageIs(route('login'))
            ->see('Your username/password combination was incorrect');
    }

    /**
     * Test login page is unsuccessful with wrong email address
     *
     * @return void
     */
    public function test_login_is_unsuccessful_with_wrong_email_address()
    {
        $this->visit(route('login'))
            ->type('other@email.com', 'email')
            ->type($this->test_user_password, 'password')
            ->press('Login')
            ->seePageIs(route('login'))
            ->see('Your username/password combination was incorrect');
    }
}
