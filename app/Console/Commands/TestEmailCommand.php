<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email=correossoftware@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la configuración de email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('=== Prueba de Configuración Email ===');
        $this->info('');
        
        // Mostrar configuración
        $this->info('Configuración SMTP actual:');
        $this->info('Mailer: ' . config('mail.default'));
        $this->info('Host: ' . config('mail.mailers.smtp.host'));
        $this->info('Port: ' . config('mail.mailers.smtp.port'));
        $this->info('Username: ' . config('mail.mailers.smtp.username'));
        $this->info('Encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->info('From Name: ' . config('mail.from.name'));
        $this->info('');
        
        try {
            $this->info('Enviando email de prueba a: ' . $email);
            
            Mail::send('emails.test', [], function($message) use ($email) {
                $message->to($email)
                        ->subject('✅ Email de Prueba - GestorNotas')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✅ Email enviado exitosamente!');
            $this->info('Revisa tu bandeja de entrada en: ' . $email);
            
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar email:');
            $this->error('Error: ' . $e->getMessage());
            
            if ($e->getPrevious()) {
                $this->error('Causa: ' . $e->getPrevious()->getMessage());
            }
            
            $this->info('');
            $this->info('Verificaciones:');
            $this->info('- ¿La contraseña de aplicación de Gmail es correcta?');
            $this->info('- ¿La verificación en dos pasos está activada?');
            $this->info('- ¿El puerto 587 está abierto en tu firewall?');
        }
        
        $this->info('');
        $this->info('=== Prueba Completada ===');
        
        return Command::SUCCESS;
    }
}
