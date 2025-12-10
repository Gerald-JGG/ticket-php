<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        /* Animated Blobs */
        .blob-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 20s infinite ease-in-out;
        }

        .blob-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #ff6b35 0%, #ff8c5f 50%, transparent 70%);
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #ff8c5f 0%, #ffa366 50%, transparent 70%);
            bottom: -100px;
            right: -100px;
            animation-delay: 5s;
            animation-duration: 25s;
        }

        .blob-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #ff4500 0%, #ff6b35 50%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 10s;
            animation-duration: 30s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(100px, -100px) scale(1.1);
            }
            50% {
                transform: translate(-50px, 100px) scale(0.9);
            }
            75% {
                transform: translate(-100px, -50px) scale(1.05);
            }
        }

        .card-header {
            background: linear-gradient(135deg, rgba(255, 108, 54, 0.36) 0%, rgba(255, 107, 53, 0.03) 100%);
            border-bottom: 1px solid rgba(255, 107, 53, 0.38);
            padding: 2.5rem 2rem;
            text-align: center;
        }

    </style>
</head>

<body>
    <div class="blob-container">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="card shadow-lg">
                <div class="card-header">
                    <i class="bi bi-headset"></i>
                    <h3 class="mb-0">Help Desk</h3>
                    <p class="mb-0">Los Patitos S.A.</p>
                </div>
                <div class="card-body p-4">
                    <h5 class="text-center mb-4">Iniciar Sesión</h5>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="/login" method="POST">
                        <div class="mb-4">
                            <label for="username" class="form-label">
                                <i class="bi bi-person"></i> Usuario
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Ingrese su usuario"
                                   required 
                                   autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Ingrese su contraseña"
                                   required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Ingresar
                        </button>
                    </form>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>