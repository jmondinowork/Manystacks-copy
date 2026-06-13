<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshAndSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refreshProducts:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the products and product_images tables and seed them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Disabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $this->info('Refreshing products, product_images tables...');
        $this->call('migrate:refresh', [
            '--path' => [
                'database/migrations/2023_10_01_162817_products.php',
                'database/migrations/2025_02_07_153634_create_ion_licences_table.php',
            ],
        ]);

        $this->info('Re-enabling foreign key checks...');
        $this->call('db:seed', ['--class' => 'DatabaseSeeder']);

        $this->info('Re-enabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $this->info('The products, product_images and ion_licences tables have been refreshed and seeded!');
    }
}
