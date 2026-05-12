<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AsistenciaRegistrada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $estudiante;
    public $asistencia;
    public $curso;

    /**
     * Create a new event instance.
     */
    public function __construct($estudiante, $asistencia, $curso)
    {
        $this->estudiante = $estudiante;
        $this->asistencia = $asistencia;
        $this->curso = $curso;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->estudiante->id),
        ];
    }
}
