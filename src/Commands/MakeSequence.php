<?php

namespace Tenthfeet\Sequence\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSequence extends GeneratorCommand
{
    protected $name = 'make:sequence';

    protected $description = 'Create a new sequence class';

    protected $type = 'Sequence';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/sequence.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Sequences';
    }
}
