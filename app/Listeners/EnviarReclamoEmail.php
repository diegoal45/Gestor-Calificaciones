<?php

namespace App\Listeners;

use App\Events\ReclamoCreado;
use App\Mail\ReclamoEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarReclamoEmail implements ShouldQueue
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
    public function handle(ReclamoCreado $event): void
    {
        Mail::to($event->estudiante->email)->send(new ReclamoEmail($event->estudiante, $event->reclamo, $event->tipo));
    }
}
