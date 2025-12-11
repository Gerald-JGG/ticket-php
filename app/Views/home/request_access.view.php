<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Acceso - Help Desk</title>
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
        <div class="login-card" style="max-width: 700px;">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <i class="bi bi-person-plus" style="font-size: 3rem;"></i>
                    <h3 class="mb-2">Solicitar Acceso al Sistema</h3>
                    <p class="mb-0 small">Complete el formulario para solicitar una cuenta</p>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Errores encontrados:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/request-access" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_completo" class="form-label">
                                    <i class="bi bi-person"></i> Nombre Completo *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre_completo" 
                                       name="nombre_completo" 
                                       value="<?= htmlspecialchars($old['nombre_completo'] ?? '') ?>"
                                       placeholder="Juan Pérez González"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i> Correo Electrónico *
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                       placeholder="juan.perez@ejemplo.com"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username_solicitado" class="form-label">
                                    <i class="bi bi-at"></i> Nombre de Usuario Deseado *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username_solicitado" 
                                       name="username_solicitado" 
                                       value="<?= htmlspecialchars($old['username_solicitado'] ?? '') ?>"
                                       placeholder="jperez"
                                       required>
                                <small class="text-muted">Mínimo 3 caracteres, sin espacios</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="bi bi-telephone"></i> Teléfono
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                                       placeholder="1234-5678">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="departamento_solicitado" class="form-label">
                                <i class="bi bi-building"></i> Departamento
                            </label>
                            <select class="form-select" id="departamento_solicitado" name="departamento_solicitado">
                                <option value="">Seleccione un departamento</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?= htmlspecialchars($depto->nombre) ?>"
                                            <?= (isset($old['departamento_solicitado']) && $old['departamento_solicitado'] == $depto->nombre) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($depto->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Otro" 
                                        <?= (isset($old['departamento_solicitado']) && $old['departamento_solicitado'] == 'Otro') ? 'selected' : '' ?>>
                                    Otro
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label">
                                <i class="bi bi-chat-text"></i> Motivo de la Solicitud *
                            </label>
                            <textarea class="form-control" 
                                      id="motivo" 
                                      name="motivo" 
                                      rows="4" 
                                      placeholder="Explique por qué necesita acceso al sistema de Help Desk..."
                                      required><?= htmlspecialchars($old['motivo'] ?? '') ?></textarea>
                            <small class="text-muted">Mínimo 20 caracteres</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>¿Qué sucede después?</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Un administrador revisará tu solicitud</li>
                                <li>Recibirás una notificación por email con la respuesta</li>
                                <li>Si es aprobada, obtendrás tus credenciales de acceso</li>
                                <li>Podrás iniciar sesión y crear tickets de soporte</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between gap-2">
                            <a href="/" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver al Inicio
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Enviar Solicitud
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">
                            ¿Ya tienes una cuenta? 
                            <a href="/login" class="text-warning">
                                <i class="bi bi-box-arrow-in-right"></i> Inicia Sesión
                            </a>
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