@extends('emails.layouts.email')

@section('content')
    <h2>🎓 Nuevo Curso Creado</h2>
    
    <p>Estimado(a) profesor(a) <strong>{{ $nombreProfesor }}</strong>,</p>
    
    <p>¡Felicidades! Has creado exitosamente el curso <strong>{{ $nombreCurso }}</strong> en nuestra plataforma de gestión de calificaciones.</p>
    
    <div class="info-card">
        <h3>📋 Detalles del Curso</h3>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        @if($descripcion)
        <div class="info-item">
            <span class="info-label">Descripción:</span>
            <span class="info-value">{{ $descripcion }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Profesor asignado:</span>
            <span class="info-value">{{ $nombreProfesor }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de creación:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>Tu curso está listo para que comiences a administrarlo. Ahora puedes:</p>
    
    <div class="info-card" style="border-left-color: #28a745;">
        <h3>🚀 Próximos pasos</h3>
        <ul style="margin-left: 20px;">
            <li>Crear tareas y actividades para tus estudiantes</li>
            <li>Generar códigos de invitación para los estudiantes</li>
            <li>Configurar los criterios de evaluación</li>
            <li>Registrar asistencia y calificaciones</li>
            <li>Monitorear el progreso de tus estudiantes</li>
        </ul>
    </div>
    
    <p>Los estudiantes podrán inscribirse a tu curso usando el código de invitación que generes, o tú puedes agregarlos manualmente.</p>
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>💡 Consejos para el éxito</h3>
        <ul style="margin-left: 20px;">
            <li>Configura claramente los criterios de evaluación desde el inicio</li>
            <li>Establece fechas límite realistas para las tareas</li>
            <li>Proporciona feedback constructivo a tus estudiantes</li>
            <li>Mantén actualizado el contenido del curso</li>
            <li>Utiliza las herramientas de reportes para seguimiento</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Gestionar Mi Curso</a>
    </div>
    
    <p>Estamos aquí para apoyarte en todo el proceso. Si tienes alguna pregunta sobre cómo utilizar las herramientas disponibles, no dudes en contactar a nuestro equipo de soporte.</p>
    
    <p>¡Te deseamos mucho éxito en tu nueva experiencia docente!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
