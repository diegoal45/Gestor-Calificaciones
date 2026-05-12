@extends('emails.layouts.email')

@section('content')
    <h2>📊 Nueva Calificación Registrada</h2>
    
    <p>Estimado(a) <strong>{{ $nombreEstudiante }}</strong>,</p>
    
    <p>Se ha registrado una nueva calificación para la tarea <strong>{{ $nombreTarea }}</strong> del curso <strong>{{ $nombreCurso }}</strong>.</p>
    
    <div class="info-card">
        <h3>📋 Detalles de la Calificación</h3>
        <div class="info-item">
            <span class="info-label">Tarea:</span>
            <span class="info-value">{{ $nombreTarea }}</span>
        </div>
        @if($descripcionTarea)
        <div class="info-item">
            <span class="info-label">Descripción:</span>
            <span class="info-value">{{ $descripcionTarea }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Nota Obtenida:</span>
            @php
            $colorNota = $nota >= 3.5 ? '#28a745' : ($nota >= 3.0 ? '#ffc107' : '#dc3545');
            @endphp
            <span class="info-value" style="font-size: 24px; font-weight: bold; color: {{ $colorNota }};">
                {{ number_format($nota, 2) }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    @if($feedback)
    <div class="info-card">
        <h3>💬 Feedback del Profesor</h3>
        <p style="font-style: italic; color: #555;">{{ $feedback }}</p>
    </div>
    @endif
    
    @if($nota >= 3.5)
        <p>¡Excelente trabajo! Has obtenido una calificación sobresaliente. Sigue así y sigue esforzándote para mantener este excelente rendimiento.</p>
    @elseif($nota >= 3.0)
        <p>¡Buen trabajo! Has aprobado la tarea. Si tienes alguna duda o necesitas mejorar en algún aspecto, no dudes en consultar con tu profesor.</p>
    @else
        <p>Te recomendamos revisar los comentarios de tu profesor y buscar apoyo adicional si es necesario. Recuerda que siempre puedes mejorar en la próxima oportunidad.</p>
    @endif
    
    <p>Puedes ver el detalle completo de esta y todas tus calificaciones en tu dashboard.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mis Calificaciones</a>
    </div>
    
    @if($nota < 3.0)
    <div class="info-card" style="border-left-color: #ffc107;">
        <h3>💡 Recomendaciones</h3>
        <ul style="margin-left: 20px;">
            <li>Revisa cuidadosamente el feedback proporcionado</li>
            <li>Consulta con tu profesor si tienes dudas</li>
            <li>Considera formar grupos de estudio</li>
            <li>Practica más con ejercicios similares</li>
        </ul>
    </div>
    @endif
    
    <p>Si tienes alguna pregunta sobre tu calificación, puedes crear un reclamo directamente desde la plataforma.</p>
    
    <p>¡Sigue adelante y sigue esforzándote!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
