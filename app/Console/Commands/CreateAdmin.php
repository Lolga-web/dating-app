<?php

namespace App\Console\Commands;

use App\Models\Admins\Admin;
use Illuminate\Console\Command;

use Illuminate\Auth\Events\Verified;

class CreateAdmin extends Command
{
    protected $signature = 'admins:create-admin';
    protected $description = 'Create admin';

    public function handle()
    {
        $user = Admin::create([
            'name' => 'Админ',
            'email' => 'admin@admin.com',
            'password' => 'admin@admin.com',
            'language' => 'ru'
        ]);
        $user->assignRole('admin');
    }
}
