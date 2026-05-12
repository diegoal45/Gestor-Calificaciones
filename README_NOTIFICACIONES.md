# Sistema de Notificaciones por Email

## Configuración

### 1. Configurar SMTP en `.env`

```env
# Configuración SMTP para Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Importante:** Para Gmail necesitas:
- Usar una "Contraseña de aplicación" en lugar de tu contraseña normal
- Activar la "Verificación en dos pasos" en tu cuenta de Google
- Generar la contraseña desde: https://myaccount.google.com/apppasswords

### 2. Procesar cola de emails

```bash
# Iniciar el worker de la cola
php artisan queue:work

# O en segundo plano
php artisan queue:work --daemon
```

## Tipos de Notificaciones Implementadas

### 1. Bienvenida al registrarse
- **Evento:** `UsuarioRegistrado`
- **Email:** `BienvenidaEmail`
- **Plantilla:** `emails.bienvenida`

### 2. Inscripción a curso
- **Evento:** `EstudianteInscrito`
- **Email:** `InscripcionCursoEmail`
- **Plantilla:** `emails.inscripcion-curso`

### 3. Registro de calificación
- **Evento:** `CalificacionRegistrada`
- **Email:** `RegistroCalificacionEmail`
- **Plantilla:** `emails.registro-calificacion`

### 4. Reclamos
- **Evento:** `ReclamoCreado`
- **Email:** `ReclamoEmail`
- **Plantillas:**
  - `emails.reclamo-creado`
  - `emails.reclamo-respondido`
  - `emails.reclamo-cerrado`

### 5. Notificaciones de tareas
- **Evento:** `TareaCreada`
- **Email:** `TareaNotificacionEmail`
- **Plantillas:**
  - `emails.tarea-creada`
  - `emails.tarea-actualizada`
  - `emails.tarea-eliminada`

### 6. Notificaciones de cursos
- **Evento:** `CursoCreado`
- **Email:** `CursoNotificacionEmail`
- **Plantillas:**
  - `emails.curso-creado`
  - `emails.curso-actualizado`
  - `emails.curso-eliminado`

### 7. Asistencia registrada
- **Evento:** `AsistenciaRegistrada`
- **Email:** `AsistenciaRegistradaEmail`
- **Plantilla:** `emails.asistencia-registrada`

## Uso del Servicio de Notificaciones

### En Controladores

```php
use App\Services\NotificationService;

class TuController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    public function registrarUsuario(Request $request)
    {
        $usuario = User::create($request->all());
        
        // Enviar email de bienvenida
        $this->notificationService->enviarBienvenida($usuario);
        
        return response()->json(['message' => 'Usuario creado']);
    }
}
```

### Disparar Eventos Manualmente

```php
// Bienvenida
event(new UsuarioRegistrado($usuario));

// Inscripción a curso
event(new EstudianteInscrito($estudiante, $curso));

// Calificación registrada
event(new CalificacionRegistrada($estudiante, $tarea, $calificacion));

// Reclamo creado
event(new ReclamoCreado($estudiante, $reclamo, 'creado'));

// Tarea creada
event(new TareaCreada($tarea, 'creada'));

// Curso creado
event(new CursoCreado($curso, 'creado'));

// Asistencia registrada
event(new AsistenciaRegistrada($estudiante, $asistencia, $curso));
```

## Endpoints de Prueba

### Verificar configuración
```http
GET /notifications/config
```

### Probar notificación de bienvenida
```http
POST /notifications/welcome
```

### Probar cualquier tipo de notificación
```http
POST /notifications/test
Content-Type: application/json

{
    "tipo": "bienvenida"
}
```

## Pruebas

### Ejecutar script de prueba
```bash
php test_notificaciones.php
```

### Probar con Tinker
```bash
php artisan tinker
```

```php
// Probar email de bienvenida
Mail::to('tu-email@example.com')->send(new \App\Mail\BienvenidaEmail(\App\Models\User::first()));

// Probar evento
event(new \App\Events\UsuarioRegistrado(\App\Models\User::first()));
```

## Variables Disponibles en Plantillas

### Usuario
- `nombre`: Nombre del usuario
- `email`: Email del usuario
- `rol`: Rol del usuario (profesor/estudiante)

### Curso
- `nombreCurso`: Nombre del curso
- `descripcionCurso`: Descripción del curso
- `nombreProfesor`: Nombre del profesor

### Tarea
- `nombreTarea`: Nombre de la tarea
- `descripcion`: Descripción de la tarea
- `porcentaje`: Porcentaje de la calificación
- `fechaLimite`: Fecha límite de entrega

### Nota
- `nota`: Valor de la calificación
- `feedback`: Comentarios del profesor

### Reclamo
- `motivo`: Motivo del reclamo
- `respuesta`: Respuesta del profesor
- `estado`: Estado del reclamo

## Personalización

### Modificar diseño de emails
Edita la plantilla base en `resources/views/emails/layouts/email.blade.php`

### Modificar colores y estilos
Los estilos están definidos en el mismo archivo de la plantilla base

### Agregar nuevas notificaciones
1. Crea el Mailable en `app/Mail/`
2. Crea la plantilla en `resources/views/emails/`
3. Crea el Event en `app/Events/`
4. Crea el Listener en `app/Listeners/`
5. Registra en `app/Providers/EventServiceProvider.php`
6. Agrega método en `NotificationService.php`

## Troubleshooting

### Emails no llegan
1. Verifica configuración SMTP en `.env`
2. Revisa que la cola esté funcionando: `php artisan queue:work`
3. Verifica logs: `tail -f storage/logs/laravel.log`
4. Prueba con `php artisan tinker`

### Error de autenticación SMTP
1. Usa contraseña de aplicación de Gmail
2. Verifica que el email y contraseña sean correctos
3. Asegúrate de que la verificación en dos pasos esté activada

### Cola no procesa
```bash
# Reiniciar cola
php artisan queue:restart

# Verificar trabajos fallidos
php artisan queue:failed

# Reintentar trabajos fallidos
php artisan queue:retry all
```

## Buenas Prácticas

1. **Usar colas:** Todas las notificaciones usan colas para no bloquear la aplicación
2. **Manejo de errores:** Los listeners implementan manejo de errores con try-catch
3. **Logging:** Los errores se registran en los logs de Laravel
4. **Testing:** Usa el script de prueba antes de pasar a producción
5. **Variables personalizadas:** Todas las plantillas usan variables consistentes
6. **Diseño responsive:** Los emails funcionan en dispositivos móviles
