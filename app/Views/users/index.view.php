<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2">
                        <i class="bi bi-people text-warning"></i> 
                        Gestión de Usuarios
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle"></i> 
                        Administración completa de usuarios del sistema
                    </p>
                </div>
                <a href="/users/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Crear Nuevo Usuario
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['error']) && $_GET['error'] === 'no_puede_desactivarse'): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Error:</strong> No puedes desactivar tu propia cuenta.
        </div>
    <?php endif; ?>

    <!-- Empty State -->
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h3>No hay usuarios</h3>
            <p>Aún no se han creado usuarios en el sistema.</p>
            <a href="/users/create" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle"></i> Crear Primer Usuario
            </a>
        </div>
    <?php else: ?>
        <!-- Vista de Cards Responsiva -->
        <div class="row">
            <?php foreach ($users as $user): ?>
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card user-card-admin">
                        <div class="card-body d-flex flex-column">
                            <!-- Avatar -->
                            <div class="user-avatar">
                                <?= strtoupper(substr($user->username, 0, 2)) ?>
                            </div>

                            <!-- Nombre de Usuario -->
                            <h6 class="user-card-name">
                                <?= htmlspecialchars($user->username) ?>
                            </h6>

                            <!-- Email -->
                            <p class="user-card-email">
                                <?= htmlspecialchars($user->email ?? 'Sin email') ?>
                            </p>

                            <!-- Grid de Información -->
                            <div class="user-info-grid">
                                <!-- Rol -->
                                <div class="user-info-row">
                                    <span class="user-info-label">
                                        <i class="bi bi-shield-check"></i> Rol:
                                    </span>
                                    <span class="badge badge-orange">
                                        <?= htmlspecialchars($user->rol) ?>
                                    </span>
                                </div>

                                <!-- Departamento -->
                                <div class="user-info-row">
                                    <span class="user-info-label">
                                        <i class="bi bi-building"></i> Departamento:
                                    </span>
                                    <span class="user-info-value">
                                        <?= htmlspecialchars($user->departamento ?? 'N/A') ?>
                                    </span>
                                </div>

                                <!-- Estado -->
                                <div class="user-info-row">
                                    <span class="user-info-label">
                                        <i class="bi bi-circle-fill"></i> Estado:
                                    </span>
                                    <?php if ($user->activo): ?>
                                        <span class="badge bg-success user-status-badge">
                                            <i class="bi bi-check-circle"></i> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary user-status-badge">
                                            <i class="bi bi-x-circle"></i> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Fecha Creación -->
                                <div class="user-info-row">
                                    <span class="user-info-label">
                                        <i class="bi bi-calendar-plus"></i> Creado:
                                    </span>
                                    <span class="user-info-value">
                                        <?= date('d/m/Y', strtotime($user->fecha_creacion)) ?>
                                    </span>
                                </div>

                                <!-- Último Acceso -->
                                <div class="user-info-row">
                                    <span class="user-info-label">
                                        <i class="bi bi-clock-history"></i> Último acceso:
                                    </span>
                                    <span class="user-info-value">
                                        <?php if ($user->ultimo_acceso): ?>
                                            <?= date('d/m/Y H:i', strtotime($user->ultimo_acceso)) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Nunca</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="user-actions mt-auto">
                                <a href="/users/edit/<?= $user->id ?>" 
                                   class="btn btn-warning btn-sm flex-fill" 
                                   title="Editar usuario">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                
                                <?php if ($user->activo): ?>
                                    <a href="/users/deactivate/<?= $user->id ?>" 
                                       class="btn btn-danger btn-sm flex-fill" 
                                       title="Desactivar usuario"
                                       onclick="return confirm('¿Está seguro de desactivar este usuario?')">
                                        <i class="bi bi-x-circle"></i> Desactivar
                                    </a>
                                <?php else: ?>
                                    <a href="/users/activate/<?= $user->id ?>" 
                                       class="btn btn-success btn-sm flex-fill" 
                                       title="Activar usuario"
                                       onclick="return confirm('¿Está seguro de activar este usuario?')">
                                        <i class="bi bi-check-circle"></i> Activar
                                    </a>
                                <?php endif; ?>
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
                        <strong>Total de usuarios:</strong>
                        <span class="badge bg-primary ms-2"><?= count($users) ?></span>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-check-circle text-success"></i> Activos: 
                        <span class="text-success">
                            <?= count(array_filter($users, fn($u) => $u->activo)) ?>
                        </span>
                        <span class="mx-2">|</span>
                        <i class="bi bi-x-circle text-secondary"></i> Inactivos: 
                        <span class="text-secondary">
                            <?= count(array_filter($users, fn($u) => !$u->activo)) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>