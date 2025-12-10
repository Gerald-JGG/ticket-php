<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-ticket-perforated"></i> Mis Tickets</h2>
                <a href="/tickets/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Crear Nuevo Ticket
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <form method="GET" action="/tickets" class="row g-3">
            <div class="col-md-4">
                <label for="estado" class="form-label">
                    <i class="bi bi-funnel"></i> Filtrar por Estado
                </label>
                <select name="estado" id="estado" class="form-select" onchange="this.form.submit()">
                    <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos los Estados</option>
                    <?php foreach ($estados as $est): ?>
                        <option value="<?= htmlspecialchars($est->nombre) ?>" 
                                <?= $estado === $est->nombre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($est->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Listado de Tickets -->
    <?php if (empty($tickets)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>No hay tickets</h3>
            <p>Aún no has creado ningún ticket de soporte.</p>
            <a href="/tickets/create" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle"></i> Crear Mi Primer Ticket
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($tickets as $ticket): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card ticket-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">
                                    <span class="ticket-id">#<?= $ticket->id ?></span>
                                </h5>
                                <span class="badge badge-<?= strtolower(str_replace(' ', '-', $ticket->estado)) ?>">
                                    <?= htmlspecialchars($ticket->estado) ?>
                                </span>
                            </div>
                            
                            <h6 class="ticket-title"><?= htmlspecialchars($ticket->titulo) ?></h6>
                            
                            <div class="ticket-meta">
                                <span class="badge badge-type-<?= strtolower($ticket->tipo) ?>">
                                    <i class="bi bi-<?= $ticket->tipo === 'Incidente' ? 'exclamation-triangle' : 'chat-dots' ?>"></i>
                                    <?= htmlspecialchars($ticket->tipo) ?>
                                </span>
                                
                                <?php if ($ticket->categoria_nombre): ?>
                                    <span class="badge" style="background-color: <?= htmlspecialchars($ticket->categoria_color ?? '#6c757d') ?>">
                                        <?= htmlspecialchars($ticket->categoria_nombre) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($ticket->prioridad_nombre): ?>
                                    <span class="badge" style="background-color: <?= htmlspecialchars($ticket->prioridad_color ?? '#6c757d') ?>">
                                        <?= htmlspecialchars($ticket->prioridad_nombre) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-muted small mb-3">
                                <i class="bi bi-calendar"></i> 
                                <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                            </p>
                            
                            <a href="/tickets/<?= $ticket->id ?>" class="btn btn-outline-orange btn-sm w-100">
                                <i class="bi bi-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>