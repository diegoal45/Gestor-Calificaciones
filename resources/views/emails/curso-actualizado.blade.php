@extends('emails.layouts.email')

@section('content')
    <h2>🔄 Curso Actualizado</h2>
    
    <p>Estimado(a) profesor(a) <strong>{{ $nombreProfesor }}</strong>,</p>
    
    <p>El curso <strong>{{ $nombreCurso }}</strong> ha sido actualizado exitosamente. Te invitamos a revisar los cambios realizados.</p>
    
    <div class="info-card">
        <h3>📋 Detalles Actualizados del Curso</h3>
        <div class="info-item">
            <span class="info-label">Curso:</span>
            <span class="info-value">{{ $nombreCurso }}</span>
        </div>
        @if($descripcion)
        <div class="info-item">
            <span class="info-label">Descripción:</span>
            <span class="info-value">{{ $descripcion }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Profesor asignado:</span>
            <span class="info-value">{{ $nombreProfesor }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Fecha de actualización:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    
    <p>Los cambios realizados en el curso pueden afectar la forma en que tus estudiantes interactúan con él y cómo se administra el contenido.</p>
    
    <div class="info-card" style="border-left-color: #17a2b8;">
        <h3>📝 Consideraciones importantes</h3>
        <ul style="margin-left: 20px;">
            <li>Informa a tus estudiantes sobre los cambios realizados</li>
            <li>Verifica que las tareas existentes sigan siendo relevantes</li>
            <li>Revisa los criterios de evaluación si los modificaste</li>
            <li>Asegúrate de que la descripción esté clara y completa</li>
        </ul>
    </div>
    
    <p>Te recomendamos comunicar los cambios a tus estudiantes para asegurar que todos estén al tanto de las actualizaciones.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="btn">Ver Curso Actualizado</a>
    </div>
    
    <p>Si necesitas ayuda para gestionar los cambios o tienes preguntas sobre las herramientas disponibles, nuestro equipo de soporte está a tu disposición.</p>
    
    <p>¡Gracias por mantener tu curso actualizado y relevante para tus estudiantes!</p>
    
    <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
@endsection
