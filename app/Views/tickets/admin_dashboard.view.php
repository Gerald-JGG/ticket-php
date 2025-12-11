<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2">
                        <i class="bi bi-speedometer2 text-warning"></i> 
                        Panel de Administración
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle"></i> 
                        Gestión completa de tickets del sistema
                    </p>
                </div>
                <a href="/users" class="btn btn-primary">
                    <i class="bi bi-people"></i> Administrar Usuarios
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="card mb-4 border-orange">
        <div class="card-header">
            <i class="bi bi-funnel"></i> Filtros de Búsqueda
        </div>
        <div class="card-body">
            <form method="GET" action="/tickets" class="row g-3">
                <div class="col-md-3">
                    <label for="estado" class="form-label">
                        <i class="bi bi-circle-fill text-info"></i> Estado
                    </label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>
                            Todos los Estados
                        </option>
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
                        <i class="bi bi-tag-fill text-warning"></i> Tipo
                    </label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option value="todos" <?= $tipo === 'todos' ? 'selected' : '' ?>>
                            Todos
                        </option>
                        <option value="Petición" <?= $tipo === 'Petición' ? 'selected' : '' ?>>
                            Petición
                        </option>
                        <option value="Incidente" <?= $tipo === 'Incidente' ? 'selected' : '' ?>>
                            Incidente
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="operador" class="form-label">
                        <i class="bi bi-person-badge-fill text-success"></i> Operador
                    </label>
                    <select name="operador" id="operador" class="form-select">
                        <option value="todos" <?= $operador === 'todos' ? 'selected' : '' ?>>
                            Todos los Operadores
                        </option>
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
                           placeholder="ID o Título del ticket"
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
    </div>

    <!-- Listado de Tickets -->
    <?php if (empty($tickets)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>No hay tickets</h3>
            <p>No se encontraron tickets con los filtros seleccionados.</p>
        </div>
    <?php else: ?>
        <!-- Vista de Cards Responsiva -->
        <div class="row">
            <?php foreach ($tickets as $ticket): ?>
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card ticket-card-admin">
                        <div class="card-body d-flex flex-column">
                            <!-- Header del Ticket -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="ticket-id-badge">
                                        <i class="bi bi-hash"></i><?= $ticket->id ?>
                                    </span>
                                </div>
                                <span class="badge" style="background-color: <?= htmlspecialchars($ticket->estado_color ?? '#6c757d') ?>">
                                    <?= htmlspecialchars($ticket->estado) ?>
                                </span>
                            </div>

                            <!-- Título -->
                            <h6 class="ticket-card-title mb-3">
                                <?= htmlspecialchars($ticket->titulo) ?>
                            </h6>

                            <!-- Metadatos -->
                            <div class="ticket-meta-compact mb-3">
                                <!-- Tipo -->
                                <div class="meta-item">
                                    <span class="badge <?= $ticket->tipo === 'Incidente' ? 'bg-danger' : 'bg-info' ?>">
                                        <i class="bi bi-<?= $ticket->tipo === 'Incidente' ? 'exclamation-triangle' : 'chat-dots' ?>"></i>
                                        <?= htmlspecialchars($ticket->tipo) ?>
                                    </span>
                                </div>

                                <!-- Prioridad -->
                                <?php if ($ticket->prioridad_nombre): ?>
                                    <div class="meta-item">
                                        <span class="badge" style="background-color: <?= htmlspecialchars($ticket->prioridad_color ?? '#6c757d') ?>">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <?= htmlspecialchars($ticket->prioridad_nombre) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Categoría -->
                                <?php if ($ticket->categoria_nombre): ?>
                                    <div class="meta-item">
                                        <span class="badge" style="background-color: <?= htmlspecialchars($ticket->categoria_color ?? '#6c757d') ?>">
                                            <i class="bi bi-folder"></i>
                                            <?= htmlspecialchars($ticket->categoria_nombre) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Información de Usuario y Operador -->
                            <div class="ticket-users-info mb-3">
                                <div class="user-info-item">
                                    <i class="bi bi-person text-primary"></i>
                                    <span class="small"><?= htmlspecialchars($ticket->usuario_creador) ?></span>
                                </div>
                                <div class="user-info-item">
                                    <?php if ($ticket->operador_asignado): ?>
                                        <i class="bi bi-person-check text-success"></i>
                                        <span class="small text-success">
                                            <?= htmlspecialchars($ticket->operador_asignado) ?>
                                        </span>
                                    <?php else: ?>
                                        <i class="bi bi-person-x text-muted"></i>
                                        <span class="small text-muted">Sin asignar</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Fecha -->
                            <div class="ticket-date mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                                </small>
                            </div>

                            <!-- Botón de Acción -->
                            <div class="mt-auto">
                                <a href="/tickets/<?= $ticket->id ?>" 
                                   class="btn btn-outline-orange btn-sm w-100">
                                    <i class="bi bi-eye"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Información Total -->
        <div class="card border-orange mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <i class="bi bi-info-circle text-warning"></i>
                        <strong>Total de tickets encontrados:</strong>
                        <span class="badge bg-primary ms-2"><?= count($tickets) ?></span>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-funnel"></i> Filtros aplicados: 
                        <span class="text-white">
                            <?= $estado !== 'todos' ? 'Estado, ' : '' ?>
                            <?= $tipo !== 'todos' ? 'Tipo, ' : '' ?>
                            <?= $operador !== 'todos' ? 'Operador, ' : '' ?>
                            <?= !empty($busqueda) ? 'Búsqueda' : '' ?>
                            <?= ($estado === 'todos' && $tipo === 'todos' && $operador === 'todos' && empty($busqueda)) ? 'Ninguno' : '' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>