<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! str_ends_with((string) config('database.connections.mysql.database'), '_test')) {
            $this->fail('Tests must run against a *_test database.');
        }
    }
}
