<?php

namespace App\Services;

use App\Events\UsuarioRegistrado;
use App\Events\EstudianteInscrito;
use App\Events\CalificacionRegistrada;
use App\Events\ReclamoCreado;
use App\Events\TareaCreada;
use App\Events\CursoCreado;
use App\Events\AsistenciaRegistrada;

class NotificationService
{
    /**
     * Enviar notificación de bienvenida a nuevo usuario
     */
    public function enviarBienvenida($usuario)
    {
        event(new UsuarioRegistrado($usuario));
    }

    /**
     * Enviar notificación de inscripción a curso
     */
    public function enviarInscripcionCurso($estudiante, $curso)
    {
        event(new EstudianteInscrito($estudiante, $curso));
    }

    /**
     * Enviar notificación de nueva calificación
     */
    public function enviarCalificacionRegistrada($estudiante, $tarea, $calificacion)
    {
        event(new CalificacionRegistrada($estudiante, $tarea, $calificacion));
    }

    /**
     * Enviar notificación de reclamo creado
     */
    public function enviarReclamoCreado($estudiante, $reclamo)
    {
        event(new ReclamoCreado($estudiante, $reclamo, 'creado'));
    }

    /**
     * Enviar notificación de reclamo respondido
     */
    public function enviarReclamoRespondido($estudiante, $reclamo)
    {
        event(new ReclamoCreado($estudiante, $reclamo, 'respondido'));
    }

    /**
     * Enviar notificación de reclamo cerrado
     */
    public function enviarReclamoCerrado($estudiante, $reclamo)
    {
        event(new ReclamoCreado($estudiante, $reclamo, 'cerrado'));
    }

    /**
     * Enviar notificación de tarea creada
     */
    public function enviarTareaCreada($tarea)
    {
        event(new TareaCreada($tarea, 'creada'));
    }

    /**
     * Enviar notificación de tarea actualizada
     */
    public function enviarTareaActualizada($tarea)
    {
        event(new TareaCreada($tarea, 'actualizada'));
    }

    /**
     * Enviar notificación de tarea eliminada
     */
    public function enviarTareaEliminada($tarea)
    {
        event(new TareaCreada($tarea, 'eliminada'));
    }

    /**
     * Enviar notificación de curso creado
     */
    public function enviarCursoCreado($curso)
    {
        event(new CursoCreado($curso, 'creado'));
    }

    /**
     * Enviar notificación de curso actualizado
     */
    public function enviarCursoActualizado($curso)
    {
        event(new CursoCreado($curso, 'actualizado'));
    }

    /**
     * Enviar notificación de curso eliminado
     */
    public function enviarCursoEliminado($curso)
    {
        event(new CursoCreado($curso, 'eliminado'));
    }

    /**
     * Enviar notificación de asistencia registrada
     */
    public function enviarAsistenciaRegistrada($estudiante, $asistencia, $curso)
    {
        event(new AsistenciaRegistrada($estudiante, $asistencia, $curso));
    }

    /**
     * Método helper para enviar notificaciones masivas
     */
    public function enviarNotificacionMasiva($estudiantes, $tipo, $data)
    {
        foreach ($estudiantes as $estudiante) {
            switch ($tipo) {
                case 'tarea':
                    $this->enviarTareaCreada($data['tarea']);
                    break;
                case 'curso':
                    // Los cursos solo notifican al profesor
                    break;
                default:
                    break;
            }
        }
    }
}
