@extends('emails.layouts.email')

@section('content')
    <h2>🔒 Tu Reclamo ha sido Cerrado</h2>
    
    <p>Estimado(a) <strong>{{ $nombreEstudiante }}</strong>,</p>
    
    <p>Te informamos que tu reclamo sobre la calificación de la tarea <strong>{{ $nombreTarea }}</strong> del curso <strong>{{ $nombreCurso }}</strong> ha sido cerrado.</p>
    
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
            <span class="info-label">Motivo:</span>
            <span class="info-value">{{ $motivo }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Estado final:</span>
            <span class="info-value">Cerrado</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de cierre:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    @if($respuesta)
    <div class="info-card" style="border-left-color: #6c757d;">
        <h3>💬 Última Respuesta</h3>
        <p style="font-style: italic; color: #555;">{{ $respuesta }}</p>
    </div>
    @endif
    
    <p>El proceso de revisión de tu reclamo ha concluido. La calificación final se mantiene como fue determinada en la última respuesta de tu profesor.</p>
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>📚 Recomendaciones para el Futuro</h3>
        <ul style="margin-left: 20px;">
            <li>Revisa cuidadosamente los criterios de evaluación antes de entregar futuras tareas</li>
            <li>Consulta con tu profesor si tienes dudas sobre los requisitos</li>
            <li>Considera solicitar retroalimentación antes de la fecha límite</li>
            <li>Usa este experiencia como aprendizaje para futuras entregas</li>
        </ul>
    </div>
    
    <p>Recordamos que puedes ver el historial completo de tus calificaciones y reclamos en tu dashboard.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mi Progreso</a>
    </div>
    
    <p>Si tienes alguna pregunta sobre este proceso o necesitas orientación académica, no dudes en contactar a tu profesor o a nuestro equipo de soporte.</p>
    
    <p>Te agradecemos tu comprensión y te deseamos éxito en tus futuras evaluaciones.</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
