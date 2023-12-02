<?php

namespace App\Console\Commands;

use App\Jobs\BunRunner;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class Bun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bun';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        BunRunner::dispatch();

        info('Bun process is running on the background!');
    }
}
