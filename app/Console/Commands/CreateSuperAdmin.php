<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-super-admin
                            {--email= : Super admin email}
                            {--password= : Super admin password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->option('password') ?? $this->secret('Password');

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists.');
            return Command::FAILURE;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $this->info('✅ Super admin user created successfully.');

        return Command::SUCCESS;
    }
}
