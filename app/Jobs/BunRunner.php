<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;

class BunRunner implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::forget('bun-build-time');
        Process::path('/')->run('rm -rf /home/akunbeben/Projects/pesenin/public/build');

        Cache::remember('bun-build-time', now()->addMinutes(15), function () {
            $start = microtime(true);
            $process = Process::path('/')->start('/home/akunbeben/.bun/bin/bun --cwd /home/akunbeben/Projects/pesenin/ build');

            $process->wait();

            return number_format((microtime(true) - $start), '2') . 's';
        });
    }
}
