<?php

namespace App\Listeners;

use App\Events\AsistenciaRegistrada;
use App\Mail\AsistenciaRegistradaEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarAsistenciaEmail implements ShouldQueue
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
    public function handle(AsistenciaRegistrada $event): void
    {
        Mail::to($event->estudiante->email)->send(new AsistenciaRegistradaEmail($event->estudiante, $event->asistencia, $event->curso));
    }
}
