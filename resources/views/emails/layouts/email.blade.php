<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notificación' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.7;
        }
        
        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .info-card h3 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .info-item {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }
        
        .info-value {
            color: #333;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .footer .social {
            margin-top: 15px;
        }
        
        .footer .social a {
            color: #6c757d;
            text-decoration: none;
            margin: 0 10px;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Gestión de Calificaciones</p>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
            <div class="social">
                <a href="#">Contacto</a> | 
                <a href="#">Soporte</a> | 
                <a href="#">Términos y Condiciones</a>
            </div>
        </div>
    </div>
</body>
</html>
