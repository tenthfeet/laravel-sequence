<?php

namespace Tenthfeet\Sequence\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeSequence extends Command
{
    protected $signature = 'make:sequence {name : Name of the sequence}';

    protected $description = 'Create a new SequenceDefinition class';

    public function handle(Filesystem $files): int
    {
        $name = Str::studly($this->argument('name'));
        $class = "{$name}";

        $path = app_path("Sequences/{$class}.php");

        if ($files->exists($path)) {
            $this->error('Sequence definition already exists.');
            return self::FAILURE;
        }

        $files->ensureDirectoryExists(dirname($path));

        $files->put($path, $this->buildClass($class));

        $this->info("Sequence definition created: {$path}");

        return self::SUCCESS;
    }

    protected function buildClass(string $class): string
    {
        return <<<PHP
<?php

namespace App\Sequences;

use Tenthfeet\Sequence\SequenceDefinition;
use Tenthfeet\Sequence\Enums\ResetPolicy;
use Carbon\Month;

final class {$class} extends SequenceDefinition
{
    public function __construct()
    {
        \$this->pattern('{SEQ:3}');
    }

    public function key(): string
    {
        return '{$this->keyName($class)}';
    }
}
PHP;
    }

    protected function keyName(string $class): string
    {
        return Str::of($class)
            ->replace('Sequence', '')
            ->snake()
            ->toString();
    }
}
