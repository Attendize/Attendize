<?php

use App\Attendize\Utils;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserTest extends TestCase
{
    public function test_edit_user_is_successful()
    {
        $this->actingAs($this->test_user);

        factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = 'new@email.com.au';
        $post = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];

        $this->call('post', route('postEditUser'), $post, $server);

        $this->seeJson([
            'status' => 'success',
            'message' => 'Successfully Saved Details',
        ]);

        $user = App\Models\User::find($this->test_user->id);

        $this->assertEquals($firstName, $user->first_name);
        $this->assertEquals($lastName, $user->last_name);
        $this->assertEquals($email, $user->email);
    }

    public function test_edit_user_is_successful_when_changing_password()
    {
        $this->actingAs($this->test_user);

        $previousPassword = $this->test_user->password;

        factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = 'new@email.com.au';
        $post = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $this->test_user_password,
            'new_password' => 'newpassword',
            'new_password_confirmation' => 'newpassword',
        ];

        $this->call('post', route('postEditUser'), $post, $server);

        $this->seeJson([
            'status' => 'success',
            'message' => 'Successfully Saved Details',
        ]);

        $user = App\Models\User::find($this->test_user->id);

        $this->assertEquals($firstName, $user->first_name);
        $this->assertEquals($lastName, $user->last_name);
        $this->assertEquals($email, $user->email);
        $this->assertNotEquals($previousPassword, $user->password);
    }

    public function test_edit_user_is_unsuccessful_because_of_invalid_email()
    {
        $this->actingAs($this->test_user);

        factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = 'new@email';
        $post = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];

        $this->call('post', route('postEditUser'), $post, $server);

        $this->seeJson([
            'status' => 'error',
        ]);
    }

    public function test_edit_user_is_unsuccessful_because_of_no_first_name()
    {
        $this->actingAs($this->test_user);

        factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $lastName = $this->faker->lastName;
        $email = 'new@email';
        $post = [
            'first_name' => '',
            'last_name' => $lastName,
            'email' => $email,
        ];

        $this->call('post', route('postEditUser'), $post, $server);

        $this->seeJson([
            'status' => 'error',
        ]);
    }

    public function test_edit_user_is_unsuccessful_because_of_no_last_name()
    {
        $this->actingAs($this->test_user);

        factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $firstName = $this->faker->firstName;
        $email = 'new@email';
        $post = [
            'first_name' => $firstName,
            'last_name' => '',
            'email' => $email,
        ];

        $this->call('post', route('postEditUser'), $post, $server);

        $this->seeJson([
            'status' => 'error',
        ]);
    }
}
