@extends('emails.layouts.email')

@section('content')
    <h2>📅 Asistencia Registrada</h2>
    
    <p>Estimado(a) <strong>{{ $nombreEstudiante }}</strong>,</p>
    
    <p>Se ha registrado tu asistencia para el curso <strong>{{ $nombreCurso }}</strong>.</p>
    
    <div class="info-card">
        <h3>📋 Detalles de la Asistencia</h3>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha:</span>
            <span class="info-value">{{ $fechaAsistencia }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                @if($estado === 'Presente')
                    <span style="color: #28a745; font-weight: bold;">✅ {{ $estado }}</span>
                @else
                    <span style="color: #dc3545; font-weight: bold;">❌ {{ $estado }}</span>
                @endif
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de registro:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    @if($estado === 'Presente')
        <p>¡Excelente! Tu asistencia ha sido registrada como presente. Seguir asistiendo regularmente es fundamental para tu éxito académico.</p>
        
        <div class="info-card" style="border-left-color: #28a745;">
            <h3>🌟 Beneficios de la asistencia regular</h3>
            <ul style="margin-left: 20px;">
                <li>Mejor comprensión de los temas</li>
                <li>Oportunidad de participar activamente</li>
                <li>Acceso a información importante</li>
                <li>Mejor rendimiento en evaluaciones</li>
            </ul>
        </div>
    @else
        <p>Tu asistencia ha sido registrada como ausente. Si tienes alguna justificación para tu inasistencia, te recomendamos comunicarla a tu profesor.</p>
        
        <div class="info-card" style="border-left-color: #ffc107;">
            <h3>📝 Recomendaciones</h3>
            <ul style="margin-left: 20px;">
                <li>Comunícate con tu profesor si tienes una justificación</li>
                <li>Ponte al día con el material que perdiste</li>
                <li>Consulta con tus compañeros sobre lo tratado en clase</li>
                <li>Considera la posibilidad de recuperar la asistencia</li>
            </ul>
        </div>
    @endif
    
    <p>Puedes ver tu historial completo de asistencia en cualquier momento desde tu dashboard.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Mi Asistencia</a>
    </div>
    
    @if($estado === 'Ausente')
        <p>Si esta ausencia fue un error o tienes una justificación válida, por favor contacta a tu profesor lo antes posible.</p>
    @endif
    
    <p>¡Sigue adelante y mantén tu compromiso con tu educación!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
