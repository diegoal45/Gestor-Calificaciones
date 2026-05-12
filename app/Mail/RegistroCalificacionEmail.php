<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistroCalificacionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $tarea;
    public $calificacion;

    /**
     * Create a new message instance.
     */
    public function __construct($estudiante, $tarea, $calificacion)
    {
        $this->estudiante = $estudiante;
        $this->tarea = $tarea;
        $this->calificacion = $calificacion;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva calificación registrada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registro-calificacion',
            with: [
                'nombreEstudiante' => $this->estudiante->name,
                'nombreTarea' => $this->tarea->nombre,
                'descripcionTarea' => $this->tarea->descripcion,
                'nota' => $this->calificacion->nota,
                'feedback' => $this->calificacion->feedback,
                'nombreCurso' => $this->tarea->curso->nombre,
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
