<?php

namespace App\Console\Commands;

use App\Models\Users\UserLocation;
use Illuminate\Console\Command;

class FixUsersLocation extends Command
{
    protected $signature = 'location:update-location';
    protected $description = 'Update users location bu ip';

    public function handle()
    {
        UserLocation::all()
                    ->map(fn($item) => $item->update(geoip()->getLocation($item->ip)->toArray()));
    }
}
