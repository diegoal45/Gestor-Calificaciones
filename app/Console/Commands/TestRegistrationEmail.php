<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Events\UsuarioRegistrado;
use Illuminate\Console\Command;

class TestRegistrationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:registration {email=correossoftware@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar registro completo con email de bienvenida';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('=== Prueba de Registro con Email ===');
        $this->info('');
        
        try {
            // Crear usuario real en la base de datos
            $this->info('Creando usuario en la base de datos...');
            $usuario = Usuario::create([
                'nombre' => 'Usuario Test Email',
                'email' => $email,
                'password' => bcrypt('password123'),
                'rol' => 'estudiante'
            ]);
            
            $this->info('✅ Usuario creado: ' . $usuario->nombre . ' (' . $usuario->email . ')');
            $this->info('');
            
            // Disparar evento de bienvenida
            $this->info('Disparando evento de bienvenida...');
            event(new UsuarioRegistrado($usuario));
            
            $this->info('✅ Evento disparado exitosamente!');
            $this->info('✅ Email de bienvenida enviado a la cola');
            $this->info('');
            $this->info('Revisa tu bandeja de entrada en: ' . $email);
            $this->info('El email debería llegar en los próximos segundos.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la prueba:');
            $this->error('Error: ' . $e->getMessage());
            
            if ($e->getPrevious()) {
                $this->error('Causa: ' . $e->getPrevious()->getMessage());
            }
            
            $this->info('');
            $this->info('Verificaciones:');
            $this->info('- ¿La cola está funcionando? (php artisan queue:work)');
            $this->info('- ¿La configuración SMTP es correcta?');
            $this->info('- ¿Hay errores en los logs?');
        }
        
        $this->info('');
        $this->info('=== Prueba Completada ===');
        
        return Command::SUCCESS;
    }
}
