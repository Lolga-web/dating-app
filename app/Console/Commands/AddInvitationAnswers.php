<?php

namespace App\Console\Commands;

use App\Models\Invitations\InvitationAnswer;
use Illuminate\Console\Command;

class AddInvitationAnswers extends Command
{
    protected $signature = 'invitation:add-invitation-answers';
    protected $description = 'Add invitation answers in DB';

    public function handle()
    {
        // $data = ['en' => ['name' => 'Yes, I agree']];
        // InvitationAnswer::create($data);

        // $data = ['en' => ['name' => 'Yes, I will, but next time']];
        // InvitationAnswer::create($data);

        // $data = ['en' => ['name' => 'Thanks, but I can`t yet']];
        // InvitationAnswer::create($data);

        // $data = ['en' => ['name' => 'No']];
        // InvitationAnswer::create($data);

        $invitation = InvitationAnswer::find(1);
        $invitation->translateOrNew('ru')->name = 'Да, я согласен';
        $invitation->translateOrNew('de')->name = 'Ja, ich bin einverstanden';
        $invitation->translateOrNew('uk')->name = 'Так, я згоден';
        $invitation->save();

        $invitation = InvitationAnswer::find(2);
        $invitation->translateOrNew('ru')->name = 'Да, я буду, но в следующий раз';
        $invitation->translateOrNew('de')->name = 'Ja, ich will, aber das nächste Mal';
        $invitation->translateOrNew('uk')->name = 'Так, я буду, але наступного разу';
        $invitation->save();

        $invitation = InvitationAnswer::find(3);
        $invitation->translateOrNew('ru')->name = 'Спасибо, но пока не могу';
        $invitation->translateOrNew('de')->name = 'Danke, aber ich kann noch nicht';
        $invitation->translateOrNew('uk')->name = 'Дякую, але поки не можу';
        $invitation->save();

        $invitation = InvitationAnswer::find(4);
        $invitation->translateOrNew('ru')->name = 'Нет';
        $invitation->translateOrNew('de')->name = 'Nein';
        $invitation->translateOrNew('uk')->name = 'Ні';
        $invitation->save();
    }
}
