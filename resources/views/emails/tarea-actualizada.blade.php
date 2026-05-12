@extends('emails.layouts.email')

@section('content')
    <h2>🔄 Tarea Actualizada</h2>
    
    <p>Estimado(a) estudiante,</p>
    
    <p>Se ha actualizado la tarea <strong>{{ $nombreTarea }}</strong> en el curso <strong>{{ $nombreCurso }}</strong>. Te invitamos a revisar los cambios realizados.</p>
    
    <div class="info-card">
        <h3>📋 Detalles Actualizados de la Tarea</h3>
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
            <span class="info-label">Fecha de actualización:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>Es importante que revises los cambios realizados, ya que pueden afectar la forma en que debes completar la tarea o los plazos de entrega.</p>
    
    @if($fechaLimite !== 'No definida')
        @php
        $diasRestantes = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($fechaLimite, 'd/m/Y H:i'), false);
        @endphp
        @if($diasRestantes <= 3)
            <div class="info-card" style="border-left-color: #ffc107;">
                <h3>⚠️ Fecha límite actualizada</h3>
                <p>La fecha límite ha sido modificada. Tienes {{ $diasRestantes }} día(s) para completar esta tarea. Asegúrate de ajustar tu planificación.</p>
            </div>
        @endif
    @endif
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>📝 Acciones recomendadas</h3>
        <ul style="margin-left: 20px;">
            <li>Revisa cuidadosamente todos los cambios en los requisitos</li>
            <li>Verifica si la fecha límite ha sido modificada</li>
            <li>Ajusta tu planificación según los nuevos requerimientos</li>
            <li>Consulta con tu profesor si tienes dudas sobre los cambios</li>
        </ul>
    </div>
    
    <p>Si ya habías comenzado a trabajar en esta tarea, asegúrate de que tu trabajo cumpla con los nuevos requisitos.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Tarea Actualizada</a>
    </div>
    
    <p>No ignores esta notificación, ya que los cambios pueden ser importantes para tu evaluación.</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
