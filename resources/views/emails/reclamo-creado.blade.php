@extends('emails.layouts.email')

@section('content')
    <h2>📝 Tu Reclamo ha sido Recibido</h2>
    
    <p>Estimado(a) <strong>{{ $nombreEstudiante }}</strong>,</p>
    
    <p>Hemos recibido tu reclamo sobre la calificación de la tarea <strong>{{ $nombreTarea }}</strong> del curso <strong>{{ $nombreCurso }}</strong>.</p>
    
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
            <span class="info-label">Estado:</span>
            <span class="info-value">{{ ucfirst($estado) }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de registro:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>Tu reclamo ha sido registrado exitosamente y será revisado por tu profesor. Te notificaremos cuando haya una respuesta o actualización sobre tu reclamo.</p>
    
    <p>El proceso de revisión通常 toma entre 24-48 horas hábiles. Te agradecemos tu paciencia mientras tu profesor analiza tu solicitud.</p>
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>ℹ️ ¿Qué sucede ahora?</h3>
        <ol style="margin-left: 20px;">
            <li>Tu profesor recibirá una notificación sobre tu reclamo</li>
            <li>Revisará tu solicitud y la calificación original</li>
            <li>Podrá mantener, modificar o justificar la calificación</li>
            <li>Recibirás una notificación con la respuesta</li>
        </ol>
    </div>
    
    <p>Puedes seguir el estado de tu reclamo en cualquier momento desde tu dashboard.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mis Reclamos</a>
    </div>
    
    <p>Si tienes alguna pregunta adicional, no dudes en contactarnos.</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
