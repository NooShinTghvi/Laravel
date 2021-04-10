<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestParticipantsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $unknown;

    /**
     * Create a new event instance.
     *
     * @param $unknown
     */
    public function __construct($unknown)
    {
        $this->unknown = $unknown;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * @return mixed
     */
    public function getExamId()
    {
        return $this->unknown->id;
    }
}
