<?php

namespace App\Events;

use App\Http\Resources\Chat\MessageResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeletePrivateChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var ChatResource $message */
    public MessageResource $message;
    /** @var int $id */
    public int $id;

    /**
     * Create a new event instance.
     *
     * @param MessageResource $message
     * @param int $id
     */
    public function __construct(MessageResource $message, int $id)
    {
        $this->message = $message;
        $this->id = $id;
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
      return 'private-delete';
  }
}
