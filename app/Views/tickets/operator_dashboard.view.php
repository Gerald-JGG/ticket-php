<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div>
                <h2 class="mb-2">
                    <i class="bi bi-speedometer2 text-warning"></i> 
                    Panel de Operador
                </h2>
                <p class="text-muted mb-0">
                    <i class="bi bi-info-circle"></i> 
                    Gestiona tickets sin asignar y tus tickets activos
                </p>
            </div>
        </div>
    </div>

    <!-- Cola Global de Tickets No Asignados -->
    <div class="card mb-4 border-orange">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-inbox"></i> Cola Global - Tickets No Asignados
                    <span class="badge bg-warning text-dark ms-2"><?= count($ticketsNoAsignados) ?></span>
                </div>
                <?php if (!empty($ticketsNoAsignados)): ?>
                    <small class="text-warning">
                        <i class="bi bi-lightning-charge"></i> Disponibles para asignar
                    </small>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($ticketsNoAsignados)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h5 class="mt-3 mb-2">¡Excelente trabajo!</h5>
                    <p class="mb-0">No hay tickets sin asignar en este momento.</p>
                </div>
            <?php else: ?>
                <!-- Vista de Cards para Tickets No Asignados -->
                <div class="row">
                    <?php foreach ($ticketsNoAsignados as $ticket): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card ticket-card-unassigned">
                                <div class="card-body d-flex flex-column">
                                    <!-- Header del Ticket -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="ticket-id-badge">
                                                <i class="bi bi-hash"></i><?= $ticket->id ?>
                                            </span>
                                        </div>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split"></i> No Asignado
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

                                    <!-- Usuario Creador -->
                                    <div class="ticket-users-info mb-3">
                                        <div class="user-info-item">
                                            <i class="bi bi-person text-primary"></i>
                                            <span class="small">Creado por: <strong><?= htmlspecialchars($ticket->usuario_creador) ?></strong></span>
                                        </div>
                                    </div>

                                    <!-- Fecha -->
                                    <div class="ticket-date mb-3">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                                        </small>
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="/tickets/<?= $ticket->id ?>" 
                                           class="btn btn-outline-orange btn-sm flex-fill">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <form action="/tickets/<?= $ticket->id ?>/assign" method="POST" style="flex: 1;">
                                            <button type="submit" 
                                                    class="btn btn-success btn-sm w-100" 
                                                    onclick="return confirm('¿Deseas asignarte este ticket?')">
                                                <i class="bi bi-hand-thumbs-up"></i> Asignarme
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mis Tickets Asignados -->
    <div class="card border-orange">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    <i class="bi bi-person-check"></i> Mis Tickets Asignados
                    <span class="badge bg-info ms-2"><?= count($misTickets) ?></span>
                </div>
                <form method="GET" action="/tickets" class="d-flex gap-2">
                    <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                        <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>
                            <i class="bi bi-funnel"></i> Todos los Estados
                        </option>
                        <?php foreach ($estados as $est): ?>
                            <?php if ($est->nombre !== 'No Asignado' && $est->nombre !== 'Cerrado'): ?>
                                <option value="<?= htmlspecialchars($est->nombre) ?>" 
                                        <?= $estado === $est->nombre ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($est->nombre) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($misTickets)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3; color: var(--primary-orange);"></i>
                    <h5 class="mt-3 mb-2">No tienes tickets asignados</h5>
                    <p class="mb-3">No hay tickets asignados con estos filtros.</p>
                    <p class="small text-warning">
                        <i class="bi bi-lightbulb"></i> 
                        Asígnate un ticket de la cola global para comenzar a trabajar.
                    </p>
                </div>
            <?php else: ?>
                <!-- Vista de Cards para Mis Tickets -->
                <div class="row">
                    <?php foreach ($misTickets as $ticket): ?>
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
                                        <span class="badge badge-<?= strtolower(str_replace(' ', '-', $ticket->estado)) ?>">
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

                                    <!-- Usuario Creador -->
                                    <div class="ticket-users-info mb-3">
                                        <div class="user-info-item">
                                            <i class="bi bi-person text-primary"></i>
                                            <span class="small"><?= htmlspecialchars($ticket->usuario_creador) ?></span>
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
                                            <i class="bi bi-eye"></i> Ver y Gestionar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>