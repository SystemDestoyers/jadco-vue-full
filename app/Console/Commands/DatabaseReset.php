<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the database with fresh migrations and seeds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database reset process...');
        
        // Run migrations fresh with seed
        $this->info('Running migrations fresh with seed...');
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->info('Migrations and seeds completed.');
        
        // Storage link
        $this->info('Creating storage link...');
        Artisan::call('storage:link', ['--force' => true]);
        $this->info('Storage link created.');
        
        // Clear all caches
        $this->info('Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');
        $this->info('All caches cleared.');
        
        // Return a success message
        $this->info('Database reset completed successfully!');
        $this->info('Admin credentials:');
        $this->info('Email: admin@admin.com');
        $this->info('Password: admin');
        
        return Command::SUCCESS;
    }
}
