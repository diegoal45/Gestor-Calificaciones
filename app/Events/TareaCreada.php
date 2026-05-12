<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TareaCreada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tarea;
    public $tipo;

    /**
     * Create a new event instance.
     */
    public function __construct($tarea, $tipo = 'creada')
    {
        $this->tarea = $tarea;
        $this->tipo = $tipo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('course.'.$this->tarea->curso_id),
        ];
    }
}
