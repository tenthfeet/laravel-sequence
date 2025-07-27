<?php

namespace Tenthfeet\Sequence;

use Illuminate\Support\ServiceProvider;
use Tenthfeet\Sequence\Commands\MakeSequence;

class SequenceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sequences.php', 'sequences');
    }

    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/sequences.php' => config_path('sequences.php'),
        ], 'config');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->commands([
            MakeSequence::class,
        ]);
    }
}
