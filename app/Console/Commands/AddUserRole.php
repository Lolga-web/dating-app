<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AddUserRole extends Command
{
    protected $signature = 'role:add-roles';
    protected $description = 'Add roles in DB';

    public function handle()
    {
        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'api'
        ]);
        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api-admin'
        ]);
    }
}
