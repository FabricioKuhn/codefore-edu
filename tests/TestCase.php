<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Isso impede que o Laravel procure pelo manifest.json nos testes
        $this->withoutVite();
    }
}