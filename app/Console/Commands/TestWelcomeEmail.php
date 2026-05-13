<?php

namespace App\Console\Commands;

use App\Mail\BienvenidaEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:welcome {email=correossoftware@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar email de bienvenida completo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('=== Prueba de Email de Bienvenida ===');
        $this->info('');
        
        try {
            // Crear usuario de prueba
            $this->info('Creando usuario de prueba...');
            $usuario = new User();
            $usuario->name = 'Usuario de Prueba';
            $usuario->email = $email;
            $usuario->role = 'profesor';
            $this->info('✅ Usuario creado: ' . $usuario->name . ' (' . $usuario->email . ')');
            $this->info('');
            
            // Enviar email de bienvenida
            $this->info('Enviando email de bienvenida...');
            Mail::to($email)->send(new BienvenidaEmail($usuario));
            
            $this->info('✅ Email de bienvenida enviado exitosamente!');
            $this->info('Revisa tu bandeja de entrada en: ' . $email);
            $this->info('');
            $this->info('El email incluye:');
            $this->info('- Diseño responsive');
            $this->info('- Información del usuario');
            $this->info('- Mensaje personalizado según rol');
            $this->info('- Botón para ir al dashboard');
            
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar email de bienvenida:');
            $this->error('Error: ' . $e->getMessage());
            
            if ($e->getPrevious()) {
                $this->error('Causa: ' . $e->getPrevious()->getMessage());
            }
        }
        
        $this->info('');
        $this->info('=== Prueba Completada ===');
        
        return Command::SUCCESS;
    }
}
