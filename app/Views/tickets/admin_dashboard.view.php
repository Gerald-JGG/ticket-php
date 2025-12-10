<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-speedometer2"></i> Panel de Administración - Tickets</h2>
                <a href="/users" class="btn btn-primary">
                    <i class="bi bi-people"></i> Administrar Usuarios
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="filters-section">
        <form method="GET" action="/tickets" class="row g-3">
            <div class="col-md-3">
                <label for="estado" class="form-label">
                    <i class="bi bi-funnel"></i> Estado
                </label>
                <select name="estado" id="estado" class="form-select">
                    <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos los Estados</option>
                    <?php foreach ($estados as $est): ?>
                        <option value="<?= htmlspecialchars($est->nombre) ?>" 
                                <?= $estado === $est->nombre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($est->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="tipo" class="form-label">
                    <i class="bi bi-tag"></i> Tipo
                </label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="todos" <?= $tipo === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="Petición" <?= $tipo === 'Petición' ? 'selected' : '' ?>>Petición</option>
                    <option value="Incidente" <?= $tipo === 'Incidente' ? 'selected' : '' ?>>Incidente</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="operador" class="form-label">
                    <i class="bi bi-person-badge"></i> Operador
                </label>
                <select name="operador" id="operador" class="form-select">
                    <option value="todos" <?= $operador === 'todos' ? 'selected' : '' ?>>Todos los Operadores</option>
                    <?php foreach ($operadores as $op): ?>
                        <option value="<?= $op->id ?>" <?= $operador == $op->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($op->username) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="busqueda" class="form-label">
                    <i class="bi bi-search"></i> Búsqueda
                </label>
                <input type="text" 
                       name="busqueda" 
                       id="busqueda" 
                       class="form-control" 
                       placeholder="ID o Título"
                       value="<?= htmlspecialchars($busqueda) ?>">
            </div>

            <div class="col-md-1">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Listado de Tickets -->
    <?php if (empty($tickets)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>No hay tickets</h3>
            <p>No se encontraron tickets con los filtros seleccionados.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Categoría</th>
                                <th>Creador</th>
                                <th>Operador</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <strong class="text-warning">#<?= $ticket->id ?></strong>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= htmlspecialchars($ticket->titulo) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $ticket->tipo === 'Incidente' ? 'bg-danger' : 'bg-info' ?>">
                                            <i class="bi bi-<?= $ticket->tipo === 'Incidente' ? 'exclamation-triangle' : 'chat-dots' ?>"></i>
                                            <?= htmlspecialchars($ticket->tipo) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= strtolower(str_replace(' ', '-', $ticket->estado)) ?>">
                                            <?= htmlspecialchars($ticket->estado) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($ticket->prioridad_nombre): ?>
                                            <span class="badge" style="background-color: <?= htmlspecialchars($ticket->prioridad_color ?? '#6c757d') ?>">
                                                <?= htmlspecialchars($ticket->prioridad_nombre) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ticket->categoria_nombre): ?>
                                            <span class="badge" style="background-color: <?= htmlspecialchars($ticket->categoria_color ?? '#6c757d') ?>">
                                                <?= htmlspecialchars($ticket->categoria_nombre) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($ticket->usuario_creador) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($ticket->operador_asignado): ?>
                                            <small class="text-success">
                                                <i class="bi bi-person-check"></i>
                                                <?= htmlspecialchars($ticket->operador_asignado) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">
                                                <i class="bi bi-person-x"></i>
                                                No asignado
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?></small>
                                    </td>
                                    <td>
                                        <a href="/tickets/<?= $ticket->id ?>" 
                                           class="btn btn-sm btn-outline-orange" 
                                           title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle"></i>
                        Total de tickets: <strong><?= count($tickets) ?></strong>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>