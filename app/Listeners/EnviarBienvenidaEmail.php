<?php

namespace App\Listeners;

use App\Events\UsuarioRegistrado;
use App\Mail\BienvenidaEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarBienvenidaEmail implements ShouldQueue
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
    public function handle(UsuarioRegistrado $event): void
    {
        Mail::to($event->usuario->email)->send(new BienvenidaEmail($event->usuario));
    }
}
