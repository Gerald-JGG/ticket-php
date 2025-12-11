<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Enviada - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <div class="blob-container">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <div class="login-container">
        <div class="login-card" style="max-width: 600px;">
            <div class="card shadow-lg">
                <div class="card-body p-5 text-center">
                    <div class="success-icon mb-4">
                        <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: var(--success);"></i>
                    </div>

                    <h2 class="mb-3">¡Solicitud Enviada Exitosamente!</h2>
                    
                    <p class="lead mb-4">
                        Tu solicitud de acceso ha sido recibida y será revisada por un administrador.
                    </p>

                    <div class="alert alert-info text-start">
                        <h5>
                            <i class="bi bi-info-circle"></i> 
                            ¿Qué sucede ahora?
                        </h5>
                        <ul class="mb-0">
                            <li>Un administrador revisará tu solicitud en las próximas horas</li>
                            <li>Recibirás una notificación por email con la decisión</li>
                            <li>Si es aprobada, obtendrás tus credenciales de acceso</li>
                            <li>Mantente atento a tu correo electrónico</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning text-start">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Nota importante:</strong>
                        <p class="mb-0 small">
                            Asegúrate de revisar tu carpeta de spam si no recibes respuesta en 24 horas.
                            El correo llegará desde: <strong>soporte@lospatitos.com</strong>
                        </p>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid gap-2">
                        <a href="/" class="btn btn-primary btn-lg">
                            <i class="bi bi-house"></i> Volver al Inicio
                        </a>
                        <a href="/login" class="btn btn-outline-orange">
                            <i class="bi bi-box-arrow-in-right"></i> ¿Ya tienes cuenta? Inicia Sesión
                        </a>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-envelope"></i> 
                            ¿Preguntas? Contáctanos en 
                            <a href="mailto:soporte@lospatitos.com" class="text-warning">soporte@lospatitos.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>