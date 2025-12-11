<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2">
                        <i class="bi bi-person-lines-fill text-warning"></i> 
                        Solicitudes de Registro
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle"></i> 
                        Gestiona las solicitudes de acceso al sistema
                    </p>
                </div>
                <?php if ($pendientes > 0): ?>
                    <div class="alert alert-warning mb-0" style="padding: 0.75rem 1.5rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong><?= $pendientes ?></strong> solicitud<?= $pendientes != 1 ? 'es' : '' ?> pendiente<?= $pendientes != 1 ? 's' : '' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito -->
    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'aprobada'): ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <strong>Solicitud aprobada exitosamente.</strong> El usuario ha sido creado.
            </div>
        <?php elseif ($_GET['success'] === 'rechazada'): ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Solicitud rechazada.</strong>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Listado de Solicitudes -->
    <?php if (empty($solicitudes)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>No hay solicitudes</h3>
            <p>No se han recibido solicitudes de registro aún.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($solicitudes as $solicitud): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card solicitud-card solicitud-<?= strtolower($solicitud->estado) ?>">
                        <div class="card-body">
                            <!-- Header con estado -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="solicitud-id-badge">
                                        <i class="bi bi-hash"></i><?= $solicitud->id ?>
                                    </span>
                                </div>
                                <span class="badge badge-<?= strtolower($solicitud->estado) ?>">
                                    <?php if ($solicitud->estado === 'Pendiente'): ?>
                                        <i class="bi bi-hourglass-split"></i>
                                    <?php elseif ($solicitud->estado === 'Aprobada'): ?>
                                        <i class="bi bi-check-circle"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle"></i>
                                    <?php endif; ?>
                                    <?= $solicitud->estado ?>
                                </span>
                            </div>

                            <!-- Información del solicitante -->
                            <h5 class="mb-2"><?= htmlspecialchars($solicitud->nombre_completo) ?></h5>
                            
                            <div class="solicitud-info mb-3">
                                <div class="info-item">
                                    <i class="bi bi-at text-primary"></i>
                                    <span><?= htmlspecialchars($solicitud->username_solicitado) ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-envelope text-info"></i>
                                    <span><?= htmlspecialchars($solicitud->email) ?></span>
                                </div>
                                <?php if ($solicitud->departamento_solicitado): ?>
                                    <div class="info-item">
                                        <i class="bi bi-building text-warning"></i>
                                        <span><?= htmlspecialchars($solicitud->departamento_solicitado) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($solicitud->telefono): ?>
                                    <div class="info-item">
                                        <i class="bi bi-telephone text-success"></i>
                                        <span><?= htmlspecialchars($solicitud->telefono) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Motivo (extracto) -->
                            <div class="solicitud-motivo mb-3">
                                <strong>Motivo:</strong>
                                <p class="mb-0 small">
                                    <?= htmlspecialchars(substr($solicitud->motivo, 0, 100)) ?>
                                    <?= strlen($solicitud->motivo) > 100 ? '...' : '' ?>
                                </p>
                            </div>

                            <!-- Fecha -->
                            <div class="solicitud-fecha mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    Solicitado: <?= date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)) ?>
                                </small>
                            </div>

                            <!-- Información de respuesta si existe -->
                            <?php if ($solicitud->estado !== 'Pendiente' && $solicitud->respondido_por_nombre): ?>
                                <div class="solicitud-respuesta">
                                    <small class="text-muted">
                                        <i class="bi bi-person-check"></i>
                                        Respondido por: <strong><?= htmlspecialchars($solicitud->respondido_por_nombre) ?></strong>
                                        <br>
                                        <i class="bi bi-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($solicitud->fecha_respuesta)) ?>
                                    </small>
                                </div>
                            <?php endif; ?>

                            <!-- Botón de acción -->
                            <div class="mt-3">
                                <a href="/solicitudes/<?= $solicitud->id ?>" 
                                   class="btn btn-outline-orange btn-sm w-100">
                                    <i class="bi bi-eye"></i> 
                                    <?= $solicitud->estado === 'Pendiente' ? 'Revisar y Responder' : 'Ver Detalles' ?>
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
                        <strong>Total de solicitudes:</strong>
                        <span class="badge bg-primary ms-2"><?= count($solicitudes) ?></span>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-hourglass text-warning"></i> Pendientes: 
                        <span class="text-warning"><?= \App\Models\SolicitudRegistro::countByStatus('Pendiente') ?></span>
                        <span class="mx-2">|</span>
                        <i class="bi bi-check-circle text-success"></i> Aprobadas: 
                        <span class="text-success"><?= \App\Models\SolicitudRegistro::countByStatus('Aprobada') ?></span>
                        <span class="mx-2">|</span>
                        <i class="bi bi-x-circle text-danger"></i> Rechazadas: 
                        <span class="text-danger"><?= \App\Models\SolicitudRegistro::countByStatus('Rechazada') ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>