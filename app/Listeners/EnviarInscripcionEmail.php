<?php

namespace App\Listeners;

use App\Events\EstudianteInscrito;
use App\Mail\InscripcionCursoEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarInscripcionEmail implements ShouldQueue
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
    public function handle(EstudianteInscrito $event): void
    {
        Mail::to($event->estudiante->email)->send(new InscripcionCursoEmail($event->estudiante, $event->curso));
    }
}
