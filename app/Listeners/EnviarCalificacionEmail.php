<?php

namespace App\Listeners;

use App\Events\CalificacionRegistrada;
use App\Mail\RegistroCalificacionEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarCalificacionEmail implements ShouldQueue
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
    public function handle(CalificacionRegistrada $event): void
    {
        Mail::to($event->estudiante->email)->send(new RegistroCalificacionEmail($event->estudiante, $event->tarea, $event->calificacion));
    }
}
