<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Desk - Los Patitos S.A.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/home.css">
</head>

<body class="home-body">
    <!-- Animated Background Blobs -->
    <div class="blob-container">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-headset"></i>
                <span>Help Desk - Los Patitos S.A.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="bi bi-info-circle"></i> Acerca de
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-orange ms-3" href="/login">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">
                        <span class="highlight">Sistema de Soporte</span>
                        <br>Técnico Eficiente
                    </h1>
                    <p class="hero-description">
                        Gestiona y resuelve tickets de soporte de manera rápida y organizada. 
                        Optimiza la comunicación entre usuarios y operadores con nuestro sistema integral.
                    </p>
                    <div class="hero-actions">
                        <a href="/request-access" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus"></i> Solicitar Acceso
                        </a>
                        <a href="/login" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <i class="bi bi-people"></i>
                            <div>
                                <strong>Usuarios</strong>
                                <span>Gestión centralizada</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-ticket-perforated"></i>
                            <div>
                                <strong>Tickets</strong>
                                <span>Seguimiento completo</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-lightning"></i>
                            <div>
                                <strong>Rápido</strong>
                                <span>Respuestas ágiles</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 hero-image">
                    <div class="floating-card">
                        <i class="bi bi-headset"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-image">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-4">¿Cómo Funciona?</h2>
                    <div class="workflow-steps">
                        <div class="workflow-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Solicita Acceso</h4>
                                <p>Completa el formulario de solicitud con tus datos. Un administrador revisará tu solicitud.</p>
                            </div>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Recibe Aprobación</h4>
                                <p>Una vez aprobada tu solicitud, recibirás tus credenciales de acceso por email.</p>
                            </div>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Crea Tickets</h4>
                                <p>Inicia sesión y crea tickets de soporte para reportar problemas o solicitar ayuda.</p>
                            </div>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Obtén Soluciones</h4>
                                <p>Un operador atenderá tu ticket y trabajará en la solución de tu problema.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>¿Listo para comenzar?</h2>
                <p>Solicita acceso ahora y empieza a gestionar tus solicitudes de soporte de manera eficiente</p>
                <a href="/request-access" class="btn btn-primary btn-lg">
                    <i class="bi bi-person-plus"></i> Solicitar Acceso Ahora
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="home-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>
                        <i class="bi bi-headset text-warning"></i> 
                        Help Desk - Los Patitos S.A.
                    </h5>
                    <p class="text-muted">Sistema de Gestión de Tickets de Soporte Técnico</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">
                        <i class="bi bi-envelope"></i> soporte@lospatitos.com
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-telephone"></i> +506 1234-5678
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Los Patitos S.A. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // Smooth scroll para los enlaces del navbar
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar transparente en scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'linear-gradient(135deg, var(--secondary-black) 0%, var(--dark-gray) 100%)';
                navbar.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.3)';
            } else {
                navbar.style.background = 'rgba(15, 0, 0, 0.9)';
                navbar.style.boxShadow = 'none';
            }
        });
    </script>
</body>

</html>