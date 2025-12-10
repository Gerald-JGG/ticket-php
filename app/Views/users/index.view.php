<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-people"></i> Gestión de Usuarios</h2>
                <a href="/users/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Crear Nuevo Usuario
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'no_puede_desactivarse'): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            No puedes desactivar tu propia cuenta.
        </div>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h3>No hay usuarios</h3>
            <p>Aún no se han creado usuarios en el sistema.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Departamento</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user->id ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($user->username) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($user->email ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge badge-orange">
                                            <?= htmlspecialchars($user->rol) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($user->departamento ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($user->activo): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($user->fecha_creacion)) ?></td>
                                    <td>
                                        <?= $user->ultimo_acceso 
                                            ? date('d/m/Y H:i', strtotime($user->ultimo_acceso)) 
                                            : 'Nunca' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/users/edit/<?= $user->id ?>" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <?php if ($user->activo): ?>
                                                <a href="/users/deactivate/<?= $user->id ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   title="Desactivar"
                                                   onclick="return confirm('¿Está seguro de desactivar este usuario?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="/users/activate/<?= $user->id ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Activar"
                                                   onclick="return confirm('¿Está seguro de activar este usuario?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>