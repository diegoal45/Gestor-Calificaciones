<?php

namespace App\Listeners;

use App\Events\CursoCreado;
use App\Mail\CursoNotificacionEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCursoNotificacionEmail implements ShouldQueue
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
    public function handle(CursoCreado $event): void
    {
        // Obtener el profesor asignado al curso
        $profesor = $event->curso->profesor;
        
        if ($profesor) {
            Mail::to($profesor->email)->send(new CursoNotificacionEmail($event->curso, $event->tipo, $profesor));
        }
    }
}
