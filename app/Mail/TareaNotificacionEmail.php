<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TareaNotificacionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tarea;
    public $tipo;
    public $estudiantes;

    /**
     * Create a new message instance.
     */
    public function __construct($tarea, $tipo = 'creada', $estudiantes = null)
    {
        $this->tarea = $tarea;
        $this->tipo = $tipo;
        $this->estudiantes = $estudiantes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->tipo) {
            'creada' => 'Nueva tarea creada en tu curso',
            'actualizada' => 'Tarea actualizada en tu curso',
            'eliminada' => 'Tarea eliminada de tu curso',
            default => 'Notificación sobre tarea'
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
            'creada' => 'emails.tarea-creada',
            'actualizada' => 'emails.tarea-actualizada',
            'eliminada' => 'emails.tarea-eliminada',
            default => 'emails.tarea-creada'
        };

        return new Content(
            view: $view,
            with: [
                'nombreTarea' => $this->tarea->nombre,
                'descripcion' => $this->tarea->descripcion,
                'porcentaje' => $this->tarea->porcentaje,
                'fechaLimite' => $this->tarea->fecha_limite ? 
                    \Carbon\Carbon::parse($this->tarea->fecha_limite)->format('d/m/Y H:i') : 
                    'No definida',
                'nombreCurso' => $this->tarea->curso->nombre,
                'descripcionCurso' => $this->tarea->curso->descripcion,
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
