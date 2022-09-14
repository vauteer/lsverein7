<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear storage/logs/laravel.log';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        exec('echo "" > ' . storage_path('logs/laravel.log'));
        $this->info('Log cleared');
    }
}
