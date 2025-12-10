<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="bi bi-ticket-detailed"></i> 
                    Ticket #<?= $ticket->id ?>
                    <span class="badge badge-<?= strtolower(str_replace(' ', '-', $ticket->estado)) ?>">
                        <?= htmlspecialchars($ticket->estado) ?>
                    </span>
                </h2>
                <a href="/tickets" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'transicion_invalida'): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                La transición de estado solicitada no es válida.
            </div>
        <?php elseif ($_GET['error'] === 'texto_vacio'): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                El comentario no puede estar vacío.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="row">
        <!-- Columna Principal: Información y Historial -->
        <div class="col-md-8">
            <!-- Información del Ticket -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Información del Ticket
                </div>
                <div class="card-body">
                    <h4 class="mb-3"><?= htmlspecialchars($ticket->titulo) ?></h4>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-tag"></i> Tipo:</strong>
                                <span class="badge <?= $ticket->tipo === 'Incidente' ? 'bg-danger' : 'bg-info' ?>">
                                    <i class="bi bi-<?= $ticket->tipo === 'Incidente' ? 'exclamation-triangle' : 'chat-dots' ?>"></i>
                                    <?= htmlspecialchars($ticket->tipo) ?>
                                </span>
                            </p>
                            
                            <?php if ($ticket->categoria_nombre): ?>
                                <p class="mb-2">
                                    <strong><i class="bi bi-folder"></i> Categoría:</strong>
                                    <span class="badge" style="background-color: <?= htmlspecialchars($ticket->categoria_color ?? '#6c757d') ?>">
                                        <?= htmlspecialchars($ticket->categoria_nombre) ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <?php if ($ticket->prioridad_nombre): ?>
                                <p class="mb-2">
                                    <strong><i class="bi bi-exclamation-circle"></i> Prioridad:</strong>
                                    <span class="badge" style="background-color: <?= htmlspecialchars($ticket->prioridad_color ?? '#6c757d') ?>">
                                        <?= htmlspecialchars($ticket->prioridad_nombre) ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-calendar"></i> Creado:</strong>
                                <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                            </p>

                            <p class="mb-2">
                                <strong><i class="bi bi-person"></i> Creado por:</strong>
                                <?= htmlspecialchars($ticket->usuario_creador) ?>
                            </p>

                            <?php if ($ticket->operador_asignado): ?>
                                <p class="mb-2">
                                    <strong><i class="bi bi-person-badge"></i> Operador Asignado:</strong>
                                    <span class="text-success"><?= htmlspecialchars($ticket->operador_asignado) ?></span>
                                </p>
                            <?php else: ?>
                                <p class="mb-2">
                                    <strong><i class="bi bi-person-x"></i> Operador:</strong>
                                    <span class="text-muted">No asignado aún</span>
                                </p>
                            <?php endif; ?>

                            <?php if ($ticket->fecha_cierre): ?>
                                <p class="mb-2">
                                    <strong><i class="bi bi-check-circle"></i> Cerrado:</strong>
                                    <?= date('d/m/Y H:i', strtotime($ticket->fecha_cierre)) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Entradas -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Historial del Ticket
                </div>
                <div class="card-body">
                    <?php if (empty($entradas)): ?>
                        <p class="text-muted mb-0">
                            <i class="bi bi-info-circle"></i> No hay entradas en este ticket aún.
                        </p>
                    <?php else: ?>
                        <?php foreach ($entradas as $entrada): ?>
                            <div class="entry-card <?= $entrada->estado_nuevo_id ? 'status-change' : '' ?>">
                                <div class="entry-header">
                                    <div>
                                        <span class="entry-author">
                                            <i class="bi bi-person-circle"></i>
                                            <?= htmlspecialchars($entrada->autor_nombre ?? 'Usuario desconocido') ?>
                                        </span>
                                        <?php if ($entrada->autor_rol): ?>
                                            <span class="badge badge-orange ms-2">
                                                <?= htmlspecialchars($entrada->autor_rol) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="entry-date">
                                        <i class="bi bi-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($entrada->fecha_creacion)) ?>
                                    </span>
                                </div>

                                <?php if ($entrada->estado_anterior && $entrada->estado_nuevo): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-secondary"><?= htmlspecialchars($entrada->estado_anterior) ?></span>
                                        <i class="bi bi-arrow-right mx-2"></i>
                                        <span class="badge bg-primary"><?= htmlspecialchars($entrada->estado_nuevo) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="entry-content">
                                    <?= nl2br(htmlspecialchars($entrada->texto)) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Agregar Comentario (Solo si no está cerrado) -->
            <?php if ($ticket->estado !== 'Cerrado'): ?>
                <?php if ($userRole === 'Usuario' || in_array($userRole, ['Operador', 'Superadministrador'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-chat-left-text"></i> Agregar Comentario
                        </div>
                        <div class="card-body">
                            <form action="/tickets/<?= $ticket->id ?>/add-entry" method="POST">
                                <div class="mb-3">
                                    <label for="texto" class="form-label">
                                        <?= $userRole === 'Usuario' ? 'Escribe tu comentario o proporciona información adicional' : 'Agregar nota o actualización' ?>
                                    </label>
                                    <textarea class="form-control" 
                                              id="texto" 
                                              name="texto" 
                                              rows="4" 
                                              placeholder="<?= $userRole === 'Usuario' ? 'Agrega información adicional...' : 'Describe el progreso o acciones realizadas...' ?>"
                                              required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Enviar Comentario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Columna Lateral: Acciones -->
        <div class="col-md-4">
            <!-- Acciones para Usuario: Aceptar/Rechazar Solución -->
            <?php if ($userRole === 'Usuario' && $ticket->estado === 'Solucionado'): ?>
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-check-circle"></i> Solución Propuesta
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            El operador ha marcado este ticket como <strong>Solucionado</strong>. 
                            Por favor, verifica si el problema fue resuelto correctamente.
                        </p>

                        <form action="/tickets/<?= $ticket->id ?>/accept-solution" method="POST" class="mb-2">
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Confirmas que el problema fue resuelto satisfactoriamente?')">
                                <i class="bi bi-check-circle"></i> Aceptar Solución
                            </button>
                        </form>

                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Rechazar Solución
                        </button>

                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i>
                            Si aceptas, el ticket será cerrado. Si lo rechazas, volverá al operador.
                        </small>
                    </div>
                </div>

                <!-- Modal para Rechazar Solución -->
                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content bg-dark">
                            <form action="/tickets/<?= $ticket->id ?>/reject-solution" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-x-circle"></i> Rechazar Solución
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Por favor, explica por qué la solución propuesta no resuelve tu problema:</p>
                                    <textarea class="form-control" 
                                              name="motivo" 
                                              rows="4" 
                                              placeholder="Describe qué sigue sin funcionar..."
                                              required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-send"></i> Enviar y Rechazar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Acciones para Operador: Cambiar Estado -->
            <?php if (in_array($userRole, ['Operador', 'Superadministrador']) && $ticket->estado !== 'Cerrado' && !empty($estadosDisponibles)): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-arrow-repeat"></i> Cambiar Estado
                    </div>
                    <div class="card-body">
                        <form action="/tickets/<?= $ticket->id ?>/update-status" method="POST">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Nuevo Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Seleccione un estado</option>
                                    <?php foreach ($estadosDisponibles as $estadoDisp): ?>
                                        <option value="<?= htmlspecialchars($estadoDisp->nombre) ?>">
                                            <?= htmlspecialchars($estadoDisp->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="comentario" class="form-label">Comentario (opcional)</label>
                                <textarea class="form-control" 
                                          id="comentario" 
                                          name="comentario" 
                                          rows="3" 
                                          placeholder="Describe las acciones realizadas o motivo del cambio..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> Actualizar Estado
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Información de Estado -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Estado del Ticket
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge badge-<?= strtolower(str_replace(' ', '-', $ticket->estado)) ?>" style="font-size: 1.2rem; padding: 0.75rem 1.5rem;">
                            <?= htmlspecialchars($ticket->estado) ?>
                        </span>
                    </div>

                    <hr>

                    <h6 class="mb-3"><i class="bi bi-diagram-3"></i> Flujo de Estados:</h6>
                    <ol class="small text-muted mb-0">
                        <li><strong>No Asignado:</strong> Esperando asignación</li>
                        <li><strong>Asignado:</strong> Operador asignado</li>
                        <li><strong>En Proceso:</strong> Trabajando en solución</li>
                        <li><strong>En Espera de Terceros:</strong> Requiere info adicional</li>
                        <li><strong>Solucionado:</strong> Pendiente de confirmación</li>
                        <li><strong>Cerrado:</strong> Ticket finalizado</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>