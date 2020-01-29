<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testCallCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('test:for', ['name' => 'mft1', 'from' => 1, 'to' => 100,  '--queue' => 'default']);
        $this->call('test:for', ['name' => 'mft2', 'from' => 101, 'to' => 200,  '--queue' => 'default']);
        $this->call('test:for', ['name' => 'mft3', 'from' => 201, 'to' => 300,  '--queue' => 'default']);
    }
}
