<?php

namespace Tests;

use App\Models\Organiser;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\DatabaseSetup;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseSetup;

    /** @var Organiser $organiser */
    protected $organiser;

    /**
     * Initializes the tests
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    /**
     * Checks if there are multiple records in the database.
     *
     * You must pass an associative array with the name of the table
     * as key and an array with the name of the column and the value.
     *
     * For example:
     *
     * [
     *     'table_1' => [
     *                      'column_1' => 'value to check',
     *                      'column_2' => 'value to check',
     *                      'column_3' => 'value to check'
     *                  ],
     *     'table_2' => [
     *                      'column_1' => 'value to check',
     *                      'column_2' => 'value to check',
     *                      'column_3' => 'value to check'
     *                  ]
     * ]
     *
     * @param  array  $expected  Array with tables/columns to check
     */
    public function assertDatabaseHasMany(array $expected = []): void
    {
        collect($expected)->each(function ($data, $table) {
            $this->assertDatabaseHas($table, $data);
        });
    }

    /**
     * Creates an user and sign in
     *
     * @param  User  $user
     * @return $this
     */
    protected function signIn(User $user = null): self
    {
        $user = $user ?: factory(User::class)->create();
        $this->actingAs($user);
        return $this;
    }

    /**
     * Creates an organiser
     *
     * @param  array  $attributes Overwrite Attributes
     * @return $this
     */
    protected function withOrganiser(array $attributes = []): self
    {
        $this->organiser = factory(Organiser::class)->create($attributes);

        return $this;
    }
}
