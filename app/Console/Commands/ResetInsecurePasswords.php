<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ResetInsecurePasswords extends Command
{
    protected $signature = 'users:reset-insecure {--yes : Force without confirmation} {--csv= : Path to CSV file to write results}';
    protected $description = 'Find users with non-bcrypt passwords and replace them with a random temporary password (printed to console or CSV)';

    public function handle()
    {
        $this->info('Inspecting users for non-bcrypt passwords...');
        $bad = User::whereRaw("password NOT RLIKE '^\\$2[ayb]\\$'")->get();

        if ($bad->isEmpty()) {
            $this->info('No users found with insecure password formats.');
            return 0;
        }

        $rows = [];
        foreach ($bad as $user) {
            $this->line('Found insecure password for: ' . $user->email . ' (ID: ' . $user->id . ')');
            if (! $this->option('yes')) {
                if (! $this->confirm('Reset password for ' . $user->email . '?')) {
                    $this->line('Skipping ' . $user->email);
                    continue;
                }
            }

            $temp = Str::random(12);
            $user->password = Hash::make($temp);
            $user->save();
            $this->line('Reset password for ' . $user->email . ' => temporary: ' . $temp);
            $rows[] = [$user->id, $user->email, $temp];
        }

        if ($csv = $this->option('csv')) {
            $this->info('Writing CSV to ' . $csv);
            $fh = fopen($csv, 'w');
            fputcsv($fh, ['id','email','temporary_password']);
            foreach ($rows as $r) fputcsv($fh, $r);
            fclose($fh);
        }

        $this->info('Done. Please inform affected users to change their password on next login.');
        return 0;
    }
}
