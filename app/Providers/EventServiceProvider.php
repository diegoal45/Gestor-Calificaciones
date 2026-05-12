<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Events
use App\Events\UsuarioRegistrado;
use App\Events\EstudianteInscrito;
use App\Events\CalificacionRegistrada;
use App\Events\ReclamoCreado;
use App\Events\ReclamoRespondido;
use App\Events\ReclamoCerrado;
use App\Events\TareaCreada;
use App\Events\TareaActualizada;
use App\Events\TareaEliminada;
use App\Events\CursoCreado;
use App\Events\CursoActualizado;
use App\Events\CursoEliminado;
use App\Events\AsistenciaRegistrada;

// Listeners
use App\Listeners\EnviarBienvenidaEmail;
use App\Listeners\EnviarInscripcionEmail;
use App\Listeners\EnviarCalificacionEmail;
use App\Listeners\EnviarReclamoEmail;
use App\Listeners\EnviarTareaNotificacionEmail;
use App\Listeners\EnviarCursoNotificacionEmail;
use App\Listeners\EnviarAsistenciaEmail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        UsuarioRegistrado::class => [
            EnviarBienvenidaEmail::class,
        ],
        
        EstudianteInscrito::class => [
            EnviarInscripcionEmail::class,
        ],
        
        CalificacionRegistrada::class => [
            EnviarCalificacionEmail::class,
        ],
        
        ReclamoCreado::class => [
            EnviarReclamoEmail::class,
        ],
        
        ReclamoRespondido::class => [
            EnviarReclamoEmail::class,
        ],
        
        ReclamoCerrado::class => [
            EnviarReclamoEmail::class,
        ],
        
        TareaCreada::class => [
            EnviarTareaNotificacionEmail::class,
        ],
        
        TareaActualizada::class => [
            EnviarTareaNotificacionEmail::class,
        ],
        
        TareaEliminada::class => [
            EnviarTareaNotificacionEmail::class,
        ],
        
        CursoCreado::class => [
            EnviarCursoNotificacionEmail::class,
        ],
        
        CursoActualizado::class => [
            EnviarCursoNotificacionEmail::class,
        ],
        
        CursoEliminado::class => [
            EnviarCursoNotificacionEmail::class,
        ],
        
        AsistenciaRegistrada::class => [
            EnviarAsistenciaEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
