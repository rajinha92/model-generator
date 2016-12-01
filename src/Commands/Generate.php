<?php

namespace Rafael\ModelGenerator\Commands;

use Illuminate\Console\Command;
use Rafael\ModelGenerator\Generator;

class Generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:model {--namespace=} {--migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate models and migrations (optional) from database';

    private $composer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->composer = app()['composer'];

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line("----------- generating -----------" . PHP_EOL);
        $generator = new Generator($this->option('namespace'), ['migration' => $this->option('migration')]);
        $generator->run();
        $this->line("----------- dumping autoloads -----------" . PHP_EOL);
        $this->composer->dumpAutoloads();

        $this->line("----------- finished -----------");
    }
}
