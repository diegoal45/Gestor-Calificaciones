@extends('emails.layouts.email')

@section('content')
    <h2>📝 Nueva Tarea Creada</h2>
    
    <p>Estimado(a) estudiante,</p>
    
    <p>Se ha creado una nueva tarea en el curso <strong>{{ $nombreCurso }}</strong>. Te invitamos a revisar los detalles y prepararte para completarla.</p>
    
    <div class="info-card">
        <h3>📋 Detalles de la Tarea</h3>
        <div class="info-item">
            <span class="info-label">Tarea:</span>
            <span class="info-value">{{ $nombreTarea }}</span>
        </div>
        @if($descripcion)
        <div class="info-item">
            <span class="info-label">Descripción:</span>
            <span class="info-value">{{ $descripcion }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        @if($porcentaje)
        <div class="info-item">
            <span class="info-label">Valor:</span>
            <span class="info-value">{{ $porcentaje }}% de la calificación final</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Fecha límite:</span>
            <span class="info-value">{{ $fechaLimite }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de creación:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    @if($fechaLimite !== 'No definida')
        @php
        $diasRestantes = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($fechaLimite, 'd/m/Y H:i'), false);
        @endphp
        @if($diasRestantes <= 3)
            <div class="info-card" style="border-left-color: #dc3545;">
                <h3>⏰ ¡Atención! Fecha límite cercana</h3>
                <p>Tienes {{ $diasRestantes }} día(s) para completar esta tarea. Te recomendamos comenzar a trabajar en ella lo antes posible.</p>
            </div>
        @endif
    @endif
    
    <p>Esta tarea es una excelente oportunidad para demostrar lo que has aprendido y mejorar tu calificación en el curso.</p>
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>💡 Recomendaciones</h3>
        <ul style="margin-left: 20px;">
            <li>Lee cuidadosamente todos los requisitos</li>
            <li>Organiza tu tiempo para completarla antes de la fecha límite</li>
            <li>No dudes en consultar con tu profesor si tienes dudas</li>
            <li>Revisa los criterios de evaluación si están disponibles</li>
        </ul>
    </div>
    
    <p>Puedes ver todos los detalles de esta tarea y entregarla directamente desde tu dashboard.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Tarea</a>
    </div>
    
    <p>¡Mucho éxito con esta nueva actividad!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
