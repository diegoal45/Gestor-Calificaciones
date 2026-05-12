<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\NotificationService;
use App\Models\User;

// Script para probar el sistema de notificaciones
echo "=== Prueba del Sistema de Notificaciones ===\n\n";

try {
    // Verificar configuración de email
    echo "1. Verificando configuración de email...\n";
    $config = [
        'mailer' => config('mail.default'),
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'username' => config('mail.mailers.smtp.username'),
        'encryption' => config('mail.mailers.smtp.encryption'),
        'from_address' => config('mail.from.address'),
        'from_name' => config('mail.from.name'),
    ];
    
    foreach ($config as $key => $value) {
        echo "  $key: " . ($value ?: 'No configurado') . "\n";
    }
    
    // Probar servicio de notificaciones
    echo "\n2. Creando servicio de notificaciones...\n";
    $notificationService = new NotificationService();
    echo "  ✓ Servicio creado exitosamente\n";
    
    // Probar con un usuario de prueba
    echo "\n3. Buscando usuario de prueba...\n";
    $usuario = User::first();
    
    if (!$usuario) {
        echo "  ⚠️  No se encontraron usuarios. Creando usuario de prueba...\n";
        
        // Crear usuario de prueba
        $usuario = new User();
        $usuario->name = 'Usuario de Prueba';
        $usuario->email = 'test@example.com';
        $usuario->password = bcrypt('password');
        $usuario->role = 'estudiante';
        $usuario->save();
        
        echo "  ✓ Usuario de prueba creado: {$usuario->email}\n";
    } else {
        echo "  ✓ Usuario encontrado: {$usuario->email}\n";
    }
    
    // Enviar notificación de bienvenida
    echo "\n4. Enviando notificación de bienvenida...\n";
    $notificationService->enviarBienvenida($usuario);
    echo "  ✓ Notificación de bienvenida enviada\n";
    
    echo "\n=== Prueba Completada Exitosamente ===\n";
    echo "Revisa tu bandeja de entrada (o log de emails) para ver la notificación.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error durante la prueba:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  Archivo: " . $e->getFile() . "\n";
    echo "  Línea: " . $e->getLine() . "\n";
    
    echo "\nPosibles soluciones:\n";
    echo "1. Verifica que tu archivo .env esté configurado correctamente\n";
    echo "2. Asegúrate de tener credenciales SMTP válidas\n";
    echo "3. Revisa que la cola de emails esté funcionando\n";
    echo "4. Verifica los permisos de los archivos de log\n";
}

echo "\n=== Comandos Útiles ===\n";
echo "• Para procesar la cola de emails: php artisan queue:work\n";
echo "• Para limpiar la cola: php artisan queue:flush\n";
echo "• Para ver emails en log: tail -f storage/logs/laravel.log\n";
echo "• Para probar email: php artisan tinker\n";
echo "  > Mail::to('tu-email@example.com')->send(new \App\Mail\BienvenidaEmail(\App\Models\User::first()));\n";
echo "\n";
