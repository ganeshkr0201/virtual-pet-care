<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Bypass CSRF for all tests — Laravel 11 uses PreventRequestForgery
        $this->app->bind(PreventRequestForgery::class, \Tests\Support\NoCsrfMiddleware::class);
    }
}
