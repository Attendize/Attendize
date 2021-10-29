<?php

namespace Tests;

use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\DatabaseSetup;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AdditionalAssertions, DatabaseSetup;

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
            return $this->assertDatabaseHas($table, $data);
        });
    }
}
