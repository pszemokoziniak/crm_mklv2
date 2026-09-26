<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Testy nie budują frontu — @vite w szablonie nie szuka manifestu.
        $this->withoutVite();
    }
}
