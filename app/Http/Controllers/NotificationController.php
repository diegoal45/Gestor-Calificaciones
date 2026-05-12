<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Enviar notificación de bienvenida manualmente
     */
    public function enviarBienvenidaManual(Request $request)
    {
        try {
            $usuario = auth()->user();
            $this->notificationService->enviarBienvenida($usuario);
            
            return response()->json([
                'message' => 'Notificación de bienvenida enviada exitosamente',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de bienvenida: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error al enviar la notificación',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Probar sistema de notificaciones
     */
    public function probarNotificaciones(Request $request)
    {
        try {
            $usuario = auth()->user();
            $tipo = $request->input('tipo', 'bienvenida');
            
            switch ($tipo) {
                case 'bienvenida':
                    $this->notificationService->enviarBienvenida($usuario);
                    $mensaje = 'Notificación de bienvenida enviada';
                    break;
                    
                default:
                    throw new \Exception('Tipo de notificación no válido');
            }
            
            return response()->json([
                'message' => $mensaje,
                'tipo' => $tipo,
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al probar notificaciones: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error al probar notificaciones',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Verificar configuración de email
     */
    public function verificarConfiguracionEmail()
    {
        try {
            $config = [
                'mail_mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username') ? 'Configurado' : 'No configurado',
                'mail_password' => config('mail.mailers.smtp.password') ? 'Configurado' : 'No configurado',
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_address' => config('mail.from.address'),
                'mail_from_name' => config('mail.from.name'),
            ];
            
            return response()->json([
                'configuracion' => $config,
                'status' => 'success'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al verificar configuración',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
