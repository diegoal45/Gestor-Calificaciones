@extends('emails.layouts.email')

@section('content')
    <h2>✅ Email de Prueba Exitoso</h2>
    
    <p>Este es un email de prueba del sistema <strong>GestorNotas</strong>.</p>
    
    <p>Si recibes este mensaje, significa que:</p>
    
    <ul style="margin-left: 20px; margin-bottom: 20px;">
        <li>✅ La configuración SMTP es correcta</li>
        <li>✅ Las credenciales de Gmail funcionan</li>
        <li>✅ El sistema de notificaciones está operativo</li>
        <li>✅ Las plantillas de email se renderizan correctamente</li>
    </ul>
    
    <div class="info-card">
        <h3>📋 Información de Prueba</h3>
        <div class="info-item">
            <span class="info-label">Fecha de prueba:</span>
            <span class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Sistema:</span>
            <span class="info-value">GestorNotas</span>
        </div>
        <div class="info-item">
            <span class="info-label">Versión:</span>
            <span class="info-value">1.0</span>
        </div>
    </div>
    
    <p>El sistema está listo para enviar notificaciones automáticas a usuarios, estudiantes y profesores.</p>
    
    <p>¡Configuración completada exitosamente!</p>
    
    <p>Saludos cordiales,<br>El equipo de GestorNotas</p>
@endsection
