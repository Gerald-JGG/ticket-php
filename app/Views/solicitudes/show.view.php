<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="bi bi-person-lines-fill"></i> 
                    Solicitud #<?= $solicitud->id ?>
                    <span class="badge badge-<?= strtolower($solicitud->estado) ?>">
                        <?= $solicitud->estado ?>
                    </span>
                </h2>
                <a href="/solicitudes" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>

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

    <div class="row">
        <!-- Columna Principal: Información de la Solicitud -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person-badge"></i> Información del Solicitante
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-person"></i> Nombre Completo:</strong>
                            <p class="mb-0"><?= htmlspecialchars($solicitud->nombre_completo) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-at"></i> Username Solicitado:</strong>
                            <p class="mb-0"><code><?= htmlspecialchars($solicitud->username_solicitado) ?></code></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-envelope"></i> Email:</strong>
                            <p class="mb-0">
                                <a href="mailto:<?= htmlspecialchars($solicitud->email) ?>" class="text-warning">
                                    <?= htmlspecialchars($solicitud->email) ?>
                                </a>
                            </p>
                        </div>
                        <?php if ($solicitud->telefono): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-telephone"></i> Teléfono:</strong>
                                <p class="mb-0"><?= htmlspecialchars($solicitud->telefono) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($solicitud->departamento_solicitado): ?>
                        <div class="mb-3">
                            <strong><i class="bi bi-building"></i> Departamento:</strong>
                            <p class="mb-0"><?= htmlspecialchars($solicitud->departamento_solicitado) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <strong><i class="bi bi-calendar3"></i> Fecha de Solicitud:</strong>
                        <p class="mb-0"><?= date('d/m/Y H:i:s', strtotime($solicitud->fecha_solicitud)) ?></p>
                    </div>

                    <hr>

                    <div>
                        <strong><i class="bi bi-chat-text"></i> Motivo de la Solicitud:</strong>
                        <div class="alert alert-info mt-2">
                            <?= nl2br(htmlspecialchars($solicitud->motivo)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Respuesta (si existe) -->
            <?php if ($solicitud->estado !== 'Pendiente'): ?>
                <div class="card mb-3 border-<?= $solicitud->estado === 'Aprobada' ? 'success' : 'danger' ?>">
                    <div class="card-header bg-<?= $solicitud->estado === 'Aprobada' ? 'success' : 'danger' ?>">
                        <i class="bi bi-<?= $solicitud->estado === 'Aprobada' ? 'check-circle' : 'x-circle' ?>"></i>
                        Respuesta del Administrador
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Estado Final:</strong>
                            <span class="badge bg-<?= $solicitud->estado === 'Aprobada' ? 'success' : 'danger' ?> ms-2">
                                <?= $solicitud->estado ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong><i class="bi bi-person-check"></i> Respondido por:</strong>
                            <p class="mb-0"><?= htmlspecialchars($solicitud->respondido_por_nombre ?? 'N/A') ?></p>
                        </div>

                        <div class="mb-3">
                            <strong><i class="bi bi-clock"></i> Fecha de Respuesta:</strong>
                            <p class="mb-0"><?= date('d/m/Y H:i:s', strtotime($solicitud->fecha_respuesta)) ?></p>
                        </div>

                        <?php if ($solicitud->comentario_respuesta): ?>
                            <div>
                                <strong><i class="bi bi-chat-dots"></i> Comentario:</strong>
                                <div class="alert alert-secondary mt-2 mb-0">
                                    <?= nl2br(htmlspecialchars($solicitud->comentario_respuesta)) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Columna Lateral: Acciones -->
        <div class="col-md-4">
            <?php if ($solicitud->estado === 'Pendiente'): ?>
                <!-- Formulario de Aprobación -->
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-check-circle"></i> Aprobar Solicitud
                    </div>
                    <div class="card-body">
                        <form action="/solicitudes/<?= $solicitud->id ?>/aprobar" method="POST">
                            <div class="mb-3">
                                <label for="rol" class="form-label">
                                    <i class="bi bi-shield-check"></i> Rol *
                                </label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Seleccione un rol</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol->id ?>" <?= $rol->nombre === 'Usuario' ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rol->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Por defecto se asigna "Usuario"</small>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Contraseña Inicial *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Contraseña temporal"
                                       required>
                                <small class="text-muted">El usuario podrá cambiarla después</small>
                            </div>

                            <div class="mb-3">
                                <label for="departamento" class="form-label">
                                    <i class="bi bi-building"></i> Departamento
                                </label>
                                <select class="form-select" id="departamento" name="departamento">
                                    <option value="">Sin departamento</option>
                                    <?php foreach ($departamentos as $depto): ?>
                                        <option value="<?= $depto->id ?>"
                                                <?= ($solicitud->departamento_solicitado === $depto->nombre) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($depto->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="comentario_aprobacion" class="form-label">
                                    <i class="bi bi-chat-dots"></i> Comentario (opcional)
                                </label>
                                <textarea class="form-control" 
                                          id="comentario_aprobacion" 
                                          name="comentario" 
                                          rows="3"
                                          placeholder="Mensaje adicional para el registro..."></textarea>
                            </div>

                            <button type="submit" 
                                    class="btn btn-success w-100"
                                    onclick="return confirm('¿Confirma que desea aprobar esta solicitud y crear el usuario?')">
                                <i class="bi bi-check-circle"></i> Aprobar y Crear Usuario
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Formulario de Rechazo -->
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="bi bi-x-circle"></i> Rechazar Solicitud
                    </div>
                    <div class="card-body">
                        <form action="/solicitudes/<?= $solicitud->id ?>/rechazar" method="POST">
                            <div class="mb-3">
                                <label for="comentario_rechazo" class="form-label">
                                    <i class="bi bi-chat-dots"></i> Motivo del Rechazo *
                                </label>
                                <textarea class="form-control" 
                                          id="comentario_rechazo" 
                                          name="comentario" 
                                          rows="4"
                                          placeholder="Explique por qué se rechaza la solicitud..."
                                          required></textarea>
                            </div>

                            <button type="submit" 
                                    class="btn btn-danger w-100"
                                    onclick="return confirm('¿Confirma que desea rechazar esta solicitud?')">
                                <i class="bi bi-x-circle"></i> Rechazar Solicitud
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Información de Estado Final -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i> Estado de la Solicitud
                    </div>
                    <div class="card-body text-center">
                        <i class="bi bi-<?= $solicitud->estado === 'Aprobada' ? 'check-circle' : 'x-circle' ?>-fill" 
                           style="font-size: 4rem; color: var(--<?= $solicitud->estado === 'Aprobada' ? 'success' : 'danger' ?>);"></i>
                        <h4 class="mt-3">Solicitud <?= $solicitud->estado ?></h4>
                        <p class="text-muted">Esta solicitud ya ha sido procesada</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>