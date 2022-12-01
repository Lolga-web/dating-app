<?php

namespace App\Console\Commands;

use App\Models\Users\User;
use Illuminate\Console\Command;

class AddUserSettings extends Command
{
    protected $signature = 'user:add-settings';
    protected $description = 'Add settings to users';

    public function handle()
    {
        User::all()->map(fn($item) => $item->settings()->create());
    }
}
