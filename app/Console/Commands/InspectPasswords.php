<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class InspectPasswords extends Command
{
    protected $signature = 'users:inspect-passwords {--csv= : Optional CSV file to write non-bcrypt users}';
    protected $description = 'Inspect users table and list accounts whose passwords are not bcrypt-hashed';

    public function handle()
    {
        $this->info('Inspecting user passwords for bcrypt format...');
        $users = User::all();
        $bad = [];
        foreach ($users as $user) {
            // Check if stored password starts like a bcrypt hash
            if (!preg_match('/^\$2[ayb]\$/', $user->password)) {
                $bad[] = [ 'id' => $user->id, 'email' => $user->email, 'password' => $user->password ];
            }
        }

        if (count($bad) === 0) {
            $this->info('All passwords appear to be using bcrypt format.');
            return 0;
        }

        $this->warn('Found ' . count($bad) . ' users with non-bcrypt password values:');
        foreach ($bad as $b) {
            $this->line("- [ID: {$b['id']}] {$b['email']} => value: {$b['password']}");
        }
        if ($csv = $this->option('csv')) {
            $this->info('Writing CSV to ' . $csv);
            $fh = fopen($csv, 'w');
            fputcsv($fh, ['id','email','password']);
            foreach ($bad as $b) fputcsv($fh, [$b['id'], $b['email'], $b['password']]);
            fclose($fh);
        }

        $this->line('Consider resetting these users passwords or re-seeding the database.');
        return 0;
    }
}
