<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CursoNotificacionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $curso;
    public $tipo;
    public $profesor;

    /**
     * Create a new message instance.
     */
    public function __construct($curso, $tipo = 'creado', $profesor = null)
    {
        $this->curso = $curso;
        $this->tipo = $tipo;
        $this->profesor = $profesor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->tipo) {
            'creado' => 'Nuevo curso creado',
            'actualizado' => 'Curso actualizado',
            'eliminado' => 'Curso eliminado',
            default => 'Notificación sobre curso'
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
            'creado' => 'emails.curso-creado',
            'actualizado' => 'emails.curso-actualizado',
            'eliminado' => 'emails.curso-eliminado',
            default => 'emails.curso-creado'
        };

        return new Content(
            view: $view,
            with: [
                'nombreCurso' => $this->curso->nombre,
                'descripcion' => $this->curso->descripcion,
                'nombreProfesor' => $this->profesor ? $this->profesor->name : 
                    ($this->curso->profesor ? $this->curso->profesor->name : 'Profesor'),
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
