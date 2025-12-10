<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="bi bi-speedometer2"></i> Panel de Operador</h2>
        </div>
    </div>

    <!-- Cola Global de Tickets No Asignados -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-inbox"></i> Cola Global - Tickets No Asignados
            <span class="badge bg-warning text-dark ms-2"><?= count($ticketsNoAsignados) ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($ticketsNoAsignados)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
                    <p class="mb-0 mt-2">¡Excelente! No hay tickets sin asignar en este momento.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Prioridad</th>
                                <th>Categoría</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ticketsNoAsignados as $ticket): ?>
                                <tr>
                                    <td>
                                        <strong class="text-warning">#<?= $ticket->id ?></strong>
                                    </td>
                                    <td>
                                        <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                                        <?php if ($ticket->prioridad_nombre): ?>
                                            <span class="badge" style="background-color: <?= htmlspecialchars($ticket->prioridad_color ?? '#6c757d') ?>">
                                                <?= htmlspecialchars($ticket->prioridad_nombre) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ticket->categoria_nombre): ?>
                                            <span class="badge" style="background-color: <?= htmlspecialchars($ticket->categoria_color ?? '#6c757d') ?>">
                                                <?= htmlspecialchars($ticket->categoria_nombre) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($ticket->usuario_creador) ?></small>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/tickets/<?= $ticket->id ?>" 
                                               class="btn btn-sm btn-outline-orange" 
                                               title="Ver Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="/tickets/<?= $ticket->id ?>/assign" method="POST" style="display: inline;">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success" 
                                                        title="Asignarme este ticket"
                                                        onclick="return confirm('¿Deseas asignarte este ticket?')">
                                                    <i class="bi bi-hand-thumbs-up"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mis Tickets Asignados -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-person-check"></i> Mis Tickets Asignados
                <span class="badge bg-info ms-2"><?= count($misTickets) ?></span>
            </div>
            <form method="GET" action="/tickets" class="d-flex gap-2">
                <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                    <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos los Estados</option>
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
        <div class="card-body">
            <?php if (empty($misTickets)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mb-0 mt-2">No tienes tickets asignados con estos filtros.</p>
                    <p class="small">Asígnate un ticket de la cola global para comenzar a trabajar.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($misTickets as $ticket): ?>
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
                                    
                                    <div class="ticket-meta mb-3">
                                        <span class="badge <?= $ticket->tipo === 'Incidente' ? 'bg-danger' : 'bg-info' ?>">
                                            <i class="bi bi-<?= $ticket->tipo === 'Incidente' ? 'exclamation-triangle' : 'chat-dots' ?>"></i>
                                            <?= htmlspecialchars($ticket->tipo) ?>
                                        </span>
                                        
                                        <?php if ($ticket->prioridad_nombre): ?>
                                            <span class="badge" style="background-color: <?= htmlspecialchars($ticket->prioridad_color ?? '#6c757d') ?>">
                                                <?= htmlspecialchars($ticket->prioridad_nombre) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-person"></i> 
                                        <?= htmlspecialchars($ticket->usuario_creador) ?>
                                    </p>
                                    
                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-calendar"></i> 
                                        <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                                    </p>
                                    
                                    <a href="/tickets/<?= $ticket->id ?>" class="btn btn-outline-orange btn-sm w-100">
                                        <i class="bi bi-eye"></i> Ver y Gestionar
                                    </a>
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