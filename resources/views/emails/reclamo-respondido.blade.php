@extends('emails.layouts.email')

@section('content')
    <h2>✅ Tu Reclamo ha Recibido Respuesta</h2>
    
    <p>Estimado(a) <strong>{{ $nombreEstudiante }}</strong>,</p>
    
    <p>Tu profesor ha respondido a tu reclamo sobre la calificación de la tarea <strong>{{ $nombreTarea }}</strong> del curso <strong>{{ $nombreCurso }}</strong>.</p>
    
    <div class="info-card">
        <h3>📋 Detalles del Reclamo</h3>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tarea:</span>
            <span class="info-value">{{ $nombreTarea }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Motivo original:</span>
            <span class="info-value">{{ $motivo }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Estado actual:</span>
            <span class="info-value">{{ ucfirst($estado) }}</span>
        </div>
    </div>
    
    <div class="info-card" style="border-left-color: #28a745;">
        <h3>💬 Respuesta del Profesor</h3>
        <p style="font-style: italic; color: #555; background: #f8f9fa; padding: 15px; border-radius: 8px;">
            {{ $respuesta }}
        </p>
    </div>
    
    <p>Te recomendamos revisar cuidadosamente la respuesta de tu profesor. Si la calificación fue modificada, podrás ver el cambio reflejado en tu dashboard.</p>
    
    @if($estado === 'aprobado')
        <p>¡Buenas noticias! Tu reclamo ha sido aprobado y la calificación ha sido ajustada según corresponda.</p>
    @elseif($estado === 'rechazado')
        <p>Tu reclamo ha sido rechazado. Sin embargo, la respuesta de tu profesor te proporcionará información valiosa para futuras evaluaciones.</p>
    @else
        <p>Tu reclamo está siendo procesado. Te mantendremos informado sobre cualquier actualización.</p>
    @endif
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mis Calificaciones</a>
    </div>
    
    <p>Si tienes alguna pregunta sobre la respuesta recibida, puedes contactar directamente a tu profesor o crear un nuevo reclamo si consideras necesario.</p>
    
    <p>Agradecemos tu paciencia y comprensión durante este proceso.</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
