<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The application keeps CSRF protection enabled; only HTTP tests bypass
        // the framework middleware because they do not submit rendered forms.
        $this->withoutMiddleware(PreventRequestForgery::class);
    }
}
