<?php

namespace App\Console\Commands;

use App\Mail\InscripcionCursoEmail;
use App\Mail\RegistroCalificacionEmail;
use App\Mail\ReclamoEmail;
use App\Mail\TareaNotificacionEmail;
use App\Mail\CursoNotificacionEmail;
use App\Mail\AsistenciaRegistradaEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {email=correossoftware@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar todo el sistema de notificaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('=== Prueba Completa del Sistema de Notificaciones ===');
        $this->info('');
        
        // Crear datos de prueba
        $this->info('Creando datos de prueba...');
        $estudiante = new User();
        $estudiante->name = 'Estudiante de Prueba';
        $estudiante->email = $email;
        $estudiante->role = 'estudiante';
        
        $profesor = new User();
        $profesor->name = 'Profesor de Prueba';
        $profesor->email = $email;
        $profesor->role = 'profesor';
        
        // Mock objects para las relaciones
        $cursoMock = (object)[
            'nombre' => 'Matemáticas Avanzadas',
            'descripcion' => 'Curso de cálculo diferencial e integral'
        ];
        
        $tareaMock = (object)[
            'nombre' => 'Examen Parcial',
            'descripcion' => 'Examen sobre límites y derivadas',
            'porcentaje' => '25',
            'fecha_limite' => '2024-12-15 23:59:59',
            'curso' => $cursoMock
        ];
        
        $calificacionMock = (object)[
            'nota' => 4.2,
            'feedback' => 'Excelente trabajo, demuestra buen entendimiento de los conceptos'
        ];
        
        $reclamoMock = (object)[
            'motivo' => 'Solicito revisión de la calificación del problema 3',
            'respuesta' => 'Revisado y confirmado, la calificación es correcta',
            'estado' => 'respondido'
        ];
        
        $asistenciaMock = (object)[
            'fecha' => '2024-11-20',
            'asistio' => true
        ];
        
        $this->info('✅ Datos de prueba creados');
        $this->info('');
        
        // Probar cada tipo de notificación
        $notificaciones = [
            'Inscripción a curso' => new InscripcionCursoEmail($estudiante, $cursoMock),
            'Registro de calificación' => new RegistroCalificacionEmail($estudiante, $tareaMock, $calificacionMock),
            'Reclamo respondido' => new ReclamoEmail($estudiante, $reclamoMock, 'respondido'),
            'Tarea creada' => new TareaNotificacionEmail($tareaMock, 'creada'),
            'Curso creado' => new CursoNotificacionEmail($cursoMock, 'creado', $profesor),
            'Asistencia registrada' => new AsistenciaRegistradaEmail($estudiante, $asistenciaMock, $cursoMock)
        ];
        
        foreach ($notificaciones as $nombre => $mail) {
            $this->info("Enviando: {$nombre}...");
            try {
                Mail::to($email)->send($mail);
                $this->info("✅ {$nombre} enviado");
            } catch (\Exception $e) {
                $this->error("❌ Error en {$nombre}: " . $e->getMessage());
            }
            $this->info('');
        }
        
        $this->info('=== Resumen ===');
        $this->info('Se han enviado ' . count($notificaciones) . ' tipos de notificaciones');
        $this->info('Revisa tu bandeja de entrada en: ' . $email);
        $this->info('');
        $this->info('Cada email incluye:');
        $this->info('- Diseño responsive y moderno');
        $this->info('- Información detallada');
        $this->info('- Botones de acción');
        $this->info('- Colores dinámicos según contenido');
        $this->info('');
        $this->info('=== Prueba Completada ===');
        
        return Command::SUCCESS;
    }
}
