<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Agora sim o trait está importado e a limpeza do banco vai funcionar!
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ignora a compilação do frontend nos testes
        $this->withoutVite();
    }
}