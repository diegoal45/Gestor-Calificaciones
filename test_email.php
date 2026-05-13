<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Mail;

echo "=== Prueba de Configuración Email ===\n\n";

try {
    // Verificar configuración
    echo "Configuración actual:\n";
    echo "MAIL_MAILER: " . config('mail.default') . "\n";
    echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
    echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
    echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
    echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

    // Enviar email de prueba
    echo "Enviando email de prueba...\n";
    
    Mail::raw('Este es un email de prueba del sistema GestorNotas. Si recibes este mensaje, la configuración SMTP es correcta.', function($message) {
        $message->to('correossoftware@gmail.com')
                ->subject('✅ Email de Prueba - GestorNotas')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "✅ Email enviado exitosamente!\n";
    echo "Revisa tu bandeja de entrada en correossoftware@gmail.com\n";
    
} catch (Exception $e) {
    echo "❌ Error al enviar email:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n\n";
    
    echo "Posibles soluciones:\n";
    echo "1. Verifica que la contraseña de aplicación de Gmail sea correcta\n";
    echo "2. Asegúrate de tener activada la verificación en dos pasos\n";
    echo "3. Revisa que el email y puerto sean correctos\n";
    echo "4. Verifica que no haya firewall bloqueando el puerto 587\n";
}

echo "\n=== Prueba Completada ===\n";
