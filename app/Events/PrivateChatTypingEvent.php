<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateChatTypingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var int $id */
    public int $id;
    /** @var int $sender_id */
    public int $sender_id;

    /**
     * Create a new event instance.
     *
     * @param int $id
     * @param int $senderId
     */
    public function __construct(int $id, int $sender_id)
    {
        $this->id = $id;
        $this->sender_id = $sender_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->id}");
    }

    public function broadcastAs()
  {
      return 'private-typing';
  }
}
