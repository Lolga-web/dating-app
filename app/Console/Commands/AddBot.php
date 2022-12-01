<?php

namespace App\Console\Commands;

use App\Models\Users\User;
use Illuminate\Console\Command;

class AddBot extends Command
{
    protected $signature = 'bot:add-bot';
    protected $description = 'Add bot in DB';

    public function handle()
    {
        $user = User::create([
            'name' => 'DatingApp',
            'email' => 'info@***.info',
            'language' => 'en',
            'gender' => 'male',
            'password' => 'info@***.info'
        ]);
        $user->assignRole('bot');
        $user->removeRole('user');
    }
}
