@extends('emails.layouts.email')

@section('content')
    <h2>¡Te has inscrito exitosamente! 🎉</h2>
    
    <p>Estimado(a) <strong>{{ $nombreEstudiante }}</strong>,</p>
    
    <p>¡Felicitaciones! Te has inscrito exitosamente en el curso <strong>{{ $nombreCurso }}</strong>. Estamos emocionados de tenerte como parte de este curso.</p>
    
    <div class="info-card">
        <h3>📚 Información del Curso</h3>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        @if($descripcionCurso)
        <div class="info-item">
            <span class="info-label">Descripción:</span>
            <span class="info-value">{{ $descripcionCurso }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Fecha de inscripción:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>A partir de este momento, podrás:</p>
    <ul style="margin-left: 20px; margin-bottom: 20px;">
        <li>Ver todas las tareas y actividades del curso</li>
        <li>Recibir notificaciones sobre nuevas tareas</li>
        <li>Ver tus calificaciones en tiempo real</li>
        <li>Recibir feedback de tu profesor</li>
        <li>Participar en las actividades del curso</li>
    </ul>
    
    <p>Te recomendamos ingresar regularmente a la plataforma para mantenerse actualizado con las nuevas tareas y fechas límite.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ir a mi Dashboard</a>
    </div>
    
    <p>Si tienes alguna pregunta sobre el curso o necesitas ayuda con la plataforma, no dudes en contactar a tu profesor o a nuestro equipo de soporte.</p>
    
    <p>¡Te deseamos mucho éxito en tu aprendizaje!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
