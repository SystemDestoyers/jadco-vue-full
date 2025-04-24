<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with a simple password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // First remove any existing users
        DB::table('users')->truncate();
        
        // Create a new admin user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
        ]);
        
        $this->info('Admin user created successfully!');
        $this->info('Email: admin@admin.com');
        $this->info('Password: admin123');
    }
}
