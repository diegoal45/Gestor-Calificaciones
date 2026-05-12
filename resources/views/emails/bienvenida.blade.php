@extends('emails.layouts.email')

@section('content')
    <h2>¡Bienvenido a {{ config('app.name') }}!</h2>
    
    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    
    <p>Estamos muy emocionados de tenerte como parte de nuestra plataforma de gestión de calificaciones. Tu cuenta ha sido creada exitosamente y ya estás listo para comenzar.</p>
    
    <div class="info-card">
        <h3>📋 Información de tu cuenta</h3>
        <div class="info-item">
            <span class="info-label">Nombre:</span>
            <span class="info-value">{{ $nombre }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $email }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Rol:</span>
            <span class="info-value">{{ ucfirst($rol) }}</span>
        </div>
    </div>
    
    @if($rol === 'profesor')
        <p>Como profesor, podrás crear cursos, gestionar tareas, registrar calificaciones y hacer seguimiento del progreso de tus estudiantes.</p>
        
        <p>Algunas de las funciones que puedes disfrutar:</p>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>Crear y administrar cursos</li>
            <li>Diseñar tareas y actividades</li>
            <li>Registrar calificaciones y feedback</li>
            <li>Generar reportes de rendimiento</li>
            <li>Gestionar la asistencia</li>
        </ul>
    @else
        <p>Como estudiante, podrás inscribirte a cursos, ver tus calificaciones, recibir feedback y mantener un seguimiento de tu progreso académico.</p>
        
        <p>Algunas de las funciones que puedes disfrutar:</p>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>Inscribirte a cursos disponibles</li>
            <li>Ver tus calificaciones en tiempo real</li>
            <li>Recibir feedback detallado</li>
            <li>Crear reclamos si es necesario</li>
            <li>Ver tu historial académico</li>
        </ul>
    @endif
    
    <p>Para comenzar, simplemente inicia sesión en tu cuenta y explora todas las herramientas que tenemos disponibles para ti.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/login') }}" class="btn">Iniciar Sesión</a>
    </div>
    
    <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar a nuestro equipo de soporte.</p>
    
    <p>¡Te damos la bienvenida una vez más y esperamos que tengas una excelente experiencia con nosotros!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
