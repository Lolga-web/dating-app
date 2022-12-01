<?php

namespace App\Console\Commands;

use App\Models\Users\User;
use Illuminate\Console\Command;

class AddWallets extends Command
{
    protected $signature = 'user:add-wallets';
    protected $description = 'Add wallets to users';

    public function handle()
    {
        User::all()->map(fn($item) => $item->hasWallet('coins-wallet') ? false : $item->createWallet(['name' => 'Coins Wallet', 'slug' => 'coins-wallet']));
    }
}
