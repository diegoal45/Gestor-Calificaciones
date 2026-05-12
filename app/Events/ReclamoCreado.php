<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReclamoCreado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $estudiante;
    public $reclamo;
    public $tipo;

    /**
     * Create a new event instance.
     */
    public function __construct($estudiante, $reclamo, $tipo = 'creado')
    {
        $this->estudiante = $estudiante;
        $this->reclamo = $reclamo;
        $this->tipo = $tipo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
