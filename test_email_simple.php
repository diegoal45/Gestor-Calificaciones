<?php

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "=== Prueba de Email Simple ===\n\n";

try {
    // Enviar email de prueba usando el sistema de Laravel
    echo "Enviando email de prueba a correossoftware@gmail.com...\n";
    
    Mail::raw('Este es un email de prueba del sistema GestorNotas. Si recibes este mensaje, la configuración SMTP es correcta.', function($message) {
        $message->to('correossoftware@gmail.com')
                ->subject('✅ Email de Prueba - GestorNotas')
                ->from('correossoftware@gmail.com', 'GestorNotas');
    });
    
    echo "✅ Email enviado exitosamente!\n";
    echo "Revisa tu bandeja de entrada en correossoftware@gmail.com\n";
    
} catch (Exception $e) {
    echo "❌ Error al enviar email:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n\n";
    
    echo "Verificando configuración...\n";
    echo "MAIL_MAILER: " . (config('mail.default') ?: 'No definido') . "\n";
    echo "MAIL_HOST: " . (config('mail.mailers.smtp.host') ?: 'No definido') . "\n";
    echo "MAIL_PORT: " . (config('mail.mailers.smtp.port') ?: 'No definido') . "\n";
    echo "MAIL_USERNAME: " . (config('mail.mailers.smtp.username') ?: 'No definido') . "\n";
    echo "MAIL_ENCRYPTION: " . (config('mail.mailers.smtp.encryption') ?: 'No definido') . "\n";
    echo "MAIL_FROM_ADDRESS: " . (config('mail.from.address') ?: 'No definido') . "\n";
    echo "MAIL_FROM_NAME: " . (config('mail.from.name') ?: 'No definido') . "\n";
}

echo "\n=== Prueba Completada ===\n";
