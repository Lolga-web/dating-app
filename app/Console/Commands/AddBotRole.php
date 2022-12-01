<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Spatie\Permission\Models\Role;

class AddBotRole extends Command
{
    protected $signature = 'role:add-bot-role';
    protected $description = 'Add bot role in DB';

    public function handle()
    {
        Role::firstOrCreate([
            'name' => 'bot',
            'guard_name' => 'api'
        ]);
    }
}
