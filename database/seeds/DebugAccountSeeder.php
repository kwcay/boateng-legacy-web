<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */

use App\Models\User;
use Illuminate\Database\Seeder;

class DebugAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Creating debug account...');

        // Debug account details
        $email = env('DEBUG_USER_EMAIL');
        $password = env('DEBUG_USER_PASSWORD');
        if (empty($email) || empty($password))
        {
            $this->command->info('No debug user found.');
            return;
        }

        // Check if debug account already exists.
        if ($debug = User::where('email', $email)->first())
        {
            $this->command->info('Debug user already exists.');
            return;
        }

        // Create debug account.
        $result = User::create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $result ?
            $this->command->info('Debug user "'. $email .'" created.') :
            $this->command->info('Could not create debug user "'. $email .'".');
    }
}
