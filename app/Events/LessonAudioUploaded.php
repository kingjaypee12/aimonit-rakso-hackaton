<?php

namespace App\Events;

use App\Models\Lesson;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonAudioUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lesson;
    public $audioData;

    /**
     * Create a new event instance.
     */
    public function __construct(Lesson $lesson, array $audioData)
    {
        $this->lesson = $lesson;
        $this->audioData = $audioData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
