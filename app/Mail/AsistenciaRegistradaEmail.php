<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AsistenciaRegistradaEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $estudiante;
    public $asistencia;
    public $curso;

    /**
     * Create a new message instance.
     */
    public function __construct($estudiante, $asistencia, $curso)
    {
        $this->estudiante = $estudiante;
        $this->asistencia = $asistencia;
        $this->curso = $curso;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Asistencia registrada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.asistencia-registrada',
            with: [
                'nombreEstudiante' => $this->estudiante->name,
                'nombreCurso' => $this->curso->nombre,
                'fechaAsistencia' => \Carbon\Carbon::parse($this->asistencia->fecha)->format('d/m/Y'),
                'estado' => $this->asistencia->asistio ? 'Presente' : 'Ausente',
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
