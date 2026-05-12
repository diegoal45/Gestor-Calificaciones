@extends('emails.layouts.email')

@section('content')
    <h2>🗑️ Curso Eliminado</h2>
    
    <p>Estimado(a) profesor(a) <strong>{{ $nombreProfesor }}</strong>,</p>
    
    <p>Te informamos que el curso <strong>{{ $nombreCurso }}</strong> ha sido eliminado de la plataforma.</p>
    
    <div class="info-card">
        <h3>📋 Detalles del Curso Eliminado</h3>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Profesor asignado:</span>
            <span class="info-value">{{ $nombreProfesor }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de eliminación:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>Este curso y toda su información asociada han sido eliminados permanentemente del sistema.</p>
    
    <div class="info-card" style="border-left-color: #dc3545;">
        <h3>⚠️ Información importante</h3>
        <ul style="margin-left: 20px;">
            <li>Todas las calificaciones del curso han sido eliminadas</li>
            <li>Los estudiantes han sido desinscritos automáticamente</li>
            <li>Las tareas y actividades ya no están disponibles</li>
            <li>Los registros de asistencia han sido eliminados</li>
        </ul>
    </div>
    
    <p>Si esta eliminación fue accidental, por favor contacta inmediatamente a nuestro equipo de soporte para evaluar si es posible recuperar la información.</p>
    
    <p>Recomendamos que descargues cualquier reporte o información importante antes de eliminar un curso en el futuro.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mis Cursos</a>
    </div>
    
    <p>Si tienes alguna pregunta sobre esta eliminación o necesitas crear un nuevo curso, no dudes en contactar a nuestro equipo de soporte.</p>
    
    <p>Agradecemos tu comprensión y estamos aquí para ayudarte a crear nuevas experiencias educativas cuando lo necesites.</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
