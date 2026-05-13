<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReclamoEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $reclamo;
    public $tipo;

    /**
     * Create a new message instance.
     */
    public function __construct($estudiante, $reclamo, $tipo = 'creado')
    {
        $this->estudiante = $estudiante;
        $this->reclamo = $reclamo;
        $this->tipo = $tipo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->tipo) {
            'creado' => 'Tu reclamo ha sido recibido',
            'respondido' => 'Tu reclamo ha recibido una respuesta',
            'cerrado' => 'Tu reclamo ha sido cerrado',
            default => 'Actualización sobre tu reclamo'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = match($this->tipo) {
            'creado' => 'emails.reclamo-creado',
            'respondido' => 'emails.reclamo-respondido',
            'cerrado' => 'emails.reclamo-cerrado',
            default => 'emails.reclamo-creado'
        };

        return new Content(
            view: $view,
            with: [
                'nombreEstudiante' => $this->estudiante->name,
                'motivo' => $this->reclamo->motivo,
                'respuesta' => $this->reclamo->respuesta ?? null,
                'estado' => $this->reclamo->estado,
                'nombreCurso' => $this->reclamo->calificacion->tarea->curso->nombre,
                'nombreTarea' => $this->reclamo->calificacion->tarea->nombre,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
