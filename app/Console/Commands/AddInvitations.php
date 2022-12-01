<?php

namespace App\Console\Commands;

use App\Models\Invitations\Invitation;
use Illuminate\Console\Command;

class AddInvitations extends Command
{
    protected $signature = 'invitation:add-invitations';
    protected $description = 'Add invitations in DB';

    public function handle()
    {
        // $data = ['en' => ['name' => 'The Cinema']];
        // Invitation::create($data);

        // $data = ['en' => ['name' => 'To a Restaurant']];
        // Invitation::create($data);

        // $data = ['en' => ['name' => 'Make Love']];
        // Invitation::create($data);

        // $data = ['en' => ['name' => 'For a Walk']];
        // Invitation::create($data);

        // $data = ['en' => ['name' => 'To Play Sports']];
        // Invitation::create($data);

        // $data = ['en' => ['name' => 'Invite to Yourself']];
        // Invitation::create($data);

        $invitation = Invitation::find(1);
        $invitation->translateOrNew('ru')->name = 'В кино';
        $invitation->translateOrNew('de')->name = 'Ins Kino';
        $invitation->translateOrNew('uk')->name = 'У кіно';
        $invitation->save();

        $invitation = Invitation::find(2);
        $invitation->translateOrNew('ru')->name = 'В ресторан';
        $invitation->translateOrNew('de')->name = 'Ins Restaurant';
        $invitation->translateOrNew('uk')->name = 'До ресторану';
        $invitation->save();

        $invitation = Invitation::find(3);
        $invitation->translateOrNew('ru')->name = 'Заняться любовью';
        $invitation->translateOrNew('de')->name = 'Liebe machen';
        $invitation->translateOrNew('uk')->name = 'Кохатися';
        $invitation->save();

        $invitation = Invitation::find(4);
        $invitation->translateOrNew('ru')->name = 'Погулять';
        $invitation->translateOrNew('de')->name = 'Spazierengehen';
        $invitation->translateOrNew('uk')->name = 'Погуляти';
        $invitation->save();

        $invitation = Invitation::find(5);
        $invitation->translateOrNew('ru')->name = 'Заняться спортом';
        $invitation->translateOrNew('de')->name = 'Sport treiben';
        $invitation->translateOrNew('uk')->name = 'Зайнятися спортом';
        $invitation->save();

        $invitation = Invitation::find(6);
        $invitation->translateOrNew('ru')->name = 'Пригласить к себе';
        $invitation->translateOrNew('de')->name = 'Zu sich selbst einladen';
        $invitation->translateOrNew('uk')->name = 'Запросити до себе';
        $invitation->save();
    }
}
