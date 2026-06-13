<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run all migrations with foreign key checks disabled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Disabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $this->info('Running migrate:fresh...');
        $this->call('migrate:fresh', ['--force' => true]);

        $this->info('Re-enabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $this->info('Database has been refreshed with foreign key checks disabled and re-enabled.');
    }
}
