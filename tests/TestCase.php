<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Exécuter les migrations pour les tests
        $this->artisan('migrate', ['--database' => 'mysql'])->run();
        
        // Exécuter les seeders pour les tests
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder'])->run();
    }
}
