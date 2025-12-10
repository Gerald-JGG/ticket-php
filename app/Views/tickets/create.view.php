<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-plus-circle"></i> Crear Nuevo Ticket</h2>
                <a href="/tickets" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Mis Tickets
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-ticket-detailed"></i> Información del Ticket
                </div>
                <div class="card-body">
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

                    <form action="/tickets/store" method="POST">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="titulo" class="form-label">
                                    <i class="bi bi-chat-left-text"></i> Título de la Solicitud *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="titulo" 
                                       name="titulo" 
                                       value="<?= htmlspecialchars($old['titulo'] ?? '') ?>"
                                       placeholder="Describe brevemente tu problema o solicitud"
                                       maxlength="200"
                                       required>
                                <small class="text-muted">Máximo 200 caracteres</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tipo" class="form-label">
                                    <i class="bi bi-tag"></i> Tipo de Solicitud *
                                </label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="Petición" <?= (isset($old['tipo']) && $old['tipo'] === 'Petición') ? 'selected' : '' ?>>
                                        <i class="bi bi-chat-dots"></i> Petición
                                    </option>
                                    <option value="Incidente" <?= (isset($old['tipo']) && $old['tipo'] === 'Incidente') ? 'selected' : '' ?>>
                                        <i class="bi bi-exclamation-triangle"></i> Incidente
                                    </option>
                                </select>
                                <small class="text-muted">
                                    <strong>Petición:</strong> Nuevo servicio o recurso<br>
                                    <strong>Incidente:</strong> Fallo en servicio existente
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="categoria_id" class="form-label">
                                    <i class="bi bi-folder"></i> Categoría
                                </label>
                                <select class="form-select" id="categoria_id" name="categoria_id">
                                    <option value="">Sin categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria->id ?>" 
                                                <?= (isset($old['categoria_id']) && $old['categoria_id'] == $categoria->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria->nombre) ?>
                                            <?php if ($categoria->descripcion): ?>
                                                - <?= htmlspecialchars($categoria->descripcion) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="prioridad_id" class="form-label">
                                    <i class="bi bi-exclamation-circle"></i> Prioridad (Opcional)
                                </label>
                                <select class="form-select" id="prioridad_id" name="prioridad_id">
                                    <option value="">Dejar que el operador la determine</option>
                                    <?php foreach ($prioridades as $prioridad): ?>
                                        <option value="<?= $prioridad->id ?>"
                                                <?= (isset($old['prioridad_id']) && $old['prioridad_id'] == $prioridad->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prioridad->nombre) ?>
                                            <?php if ($prioridad->descripcion): ?>
                                                - <?= htmlspecialchars($prioridad->descripcion) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">
                                <i class="bi bi-card-text"></i> Descripción Detallada *
                            </label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="8" 
                                      placeholder="Describe detalladamente tu problema o solicitud. Incluye toda la información relevante que pueda ayudar a resolver tu caso."
                                      required><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                            <small class="text-muted">
                                <i class="bi bi-lightbulb"></i> 
                                <strong>Tip:</strong> Incluye pasos para reproducir el problema, capturas de pantalla (si aplica), y cualquier mensaje de error que hayas visto.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>¿Qué sucede después de crear el ticket?</strong>
                            <ul class="mb-0 mt-2">
                                <li>Tu ticket será visible para los operadores del sistema</li>
                                <li>Un operador se asignará el ticket y comenzará a trabajar en él</li>
                                <li>Podrás ver el progreso y agregar comentarios adicionales</li>
                                <li>Recibirás actualizaciones sobre el estado de tu solicitud</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/tickets" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Crear Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>