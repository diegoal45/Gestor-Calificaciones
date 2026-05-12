<?php

namespace App\Listeners;

use App\Events\TareaCreada;
use App\Mail\TareaNotificacionEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarTareaNotificacionEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TareaCreada $event): void
    {
        // Obtener todos los estudiantes inscritos en el curso
        $estudiantes = $event->tarea->curso->estudiantes ?? [];
        
        foreach ($estudiantes as $estudiante) {
            Mail::to($estudiante->email)->send(new TareaNotificacionEmail($event->tarea, $event->tipo));
        }
    }
}
