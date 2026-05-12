@extends('emails.layouts.email')

@section('content')
    <h2>🗑️ Tarea Eliminada</h2>
    
    <p>Estimado(a) estudiante,</p>
    
    <p>Te informamos que la tarea <strong>{{ $nombreTarea }}</strong> del curso <strong>{{ $nombreCurso }}</strong> ha sido eliminada.</p>
    
    <div class="info-card">
        <h3>📋 Detalles de la Tarea Eliminada</h3>
        <div class="info-item">
            <span class="info-label">Tarea:</span>
            <span class="info-value">{{ $nombreTarea }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de eliminación:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>Esta tarea ya no será evaluada y no afectará tu calificación final del curso.</p>
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>ℹ️ ¿Qué significa esto para ti?</h3>
        <ul style="margin-left: 20px;">
            <li>No necesitas completar ni entregar esta tarea</li>
            <li>Esta tarea no será considerada en tu calificación final</li>
            <li>Es posible que se reemplace con una nueva actividad</li>
            <li>Consulta con tu profesor si tienes dudas</li>
        </ul>
    </div>
    
    <p>Si ya habías trabajado en esta tarea, te recomendamos guardar tu progreso, ya que podría ser útil para futuras actividades similares.</p>
    
    <p>Te mantendremos informado si se crea una nueva tarea para reemplazar esta actividad.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mis Cursos</a>
    </div>
    
    <p>Si tienes alguna pregunta sobre esta eliminación o necesitas aclaraciones, no dudes en contactar a tu profesor.</p>
    
    <p>Agradecemos tu comprensión.</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
