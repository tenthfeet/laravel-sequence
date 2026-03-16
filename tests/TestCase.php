<?php

namespace Tenthfeet\Sequence\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Tenthfeet\Sequence\SequenceServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $databasePath = $this->getDatabaseFilePath();

        if (! file_exists($databasePath)) {
            touch($databasePath);
        }

        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            SequenceServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => $this->getDatabaseFilePath(),
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getDatabaseFilePath(): string
    {
        return __DIR__ . '/../database/database.sqlite';
    }
}
