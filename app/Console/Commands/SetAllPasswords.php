<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetAllPasswords extends Command
{
    protected $signature = 'users:set-all-passwords {password=12345678 : The new password to set (plaintext)} {--yes : Force without confirmation} {--role= : Optional role filter (admin|courier|customer)} {--csv= : Optional CSV path to write results}';
    protected $description = 'Set password for all users (or by role) to a specified plaintext password.';

    public function handle()
    {
        $password = $this->argument('password');
        $role = $this->option('role');

        $query = User::query();
        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->get();
        if ($users->isEmpty()) {
            $this->info('No users found for the specified criteria.');
            return 0;
        }

        $count = $users->count();
        $this->info("Found $count users. Setting password to plaintext: [$password]");

        if (! $this->option('yes')) {
            if (! $this->confirm('Are you sure you want to set the password for these users?')) {
                $this->info('Aborted. Use --yes to skip confirmation.');
                return 1;
            }
        }

        $rows = [];
        foreach ($users as $user) {
            $user->password = Hash::make($password);
            $user->save();
            $this->line('Updated password for: ' . $user->email);
            $rows[] = [$user->id, $user->email, $password];
        }

        if ($csv = $this->option('csv')) {
            $this->info('Writing CSV to ' . $csv);
            $fh = fopen($csv, 'w');
            fputcsv($fh, ['id','email','plaintext_password']);
            foreach ($rows as $r) fputcsv($fh, $r);
            fclose($fh);
        }

        $this->info('Done. Please inform users to change their password as needed.');
        return 0;
    }
}
