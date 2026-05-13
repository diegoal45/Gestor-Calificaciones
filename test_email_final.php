<?php

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "=== Prueba Final de Configuración Email ===\n\n";

try {
    // Mostrar configuración
    echo "Configuración SMTP actual:\n";
    echo "Mailer: " . config('mail.default') . "\n";
    echo "Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "From Address: " . config('mail.from.address') . "\n";
    echo "From Name: " . config('mail.from.name') . "\n\n";

    // Enviar email de prueba
    echo "Enviando email de prueba...\n";
    
    Mail::send('emails.test', [], function($message) {
        $message->to('correossoftware@gmail.com')
                ->subject('✅ Email de Prueba - GestorNotas')
                ->from('correossoftware@gmail.com', 'GestorNotas');
    });
    
    echo "✅ Email enviado exitosamente!\n";
    echo "Revisa tu bandeja de entrada en correossoftware@gmail.com\n";
    
} catch (Exception $e) {
    echo "❌ Error al enviar email:\n";
    echo "Error: " . $e->getMessage() . "\n";
    
    if ($e->getPrevious()) {
        echo "Causa: " . $e->getPrevious()->getMessage() . "\n";
    }
    
    echo "\nVerificaciones:\n";
    echo "- ¿La contraseña de aplicación de Gmail es correcta?\n";
    echo "- ¿La verificación en dos pasos está activada?\n";
    echo "- ¿El puerto 587 está abierto en tu firewall?\n";
}

echo "\n=== Prueba Completada ===\n";
