<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InscripcionCursoEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $curso;

    /**
     * Create a new message instance.
     */
    public function __construct($estudiante, $curso)
    {
        $this->estudiante = $estudiante;
        $this->curso = $curso;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Te has inscrito a un nuevo curso!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inscripcion-curso',
            with: [
                'nombreEstudiante' => $this->estudiante->name,
                'nombreCurso' => $this->curso->nombre,
                'descripcionCurso' => $this->curso->descripcion,
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
