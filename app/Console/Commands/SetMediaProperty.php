<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;

class SetMediaProperty extends Command
{
    protected $signature = 'media:set-property';
    protected $description = 'Set media property';

    public function handle()
    {
        Media::where('collection_name', 'user_photo')->get()->map(fn($item) => $item->forgetCustomProperty('match')->save());
    }
}
