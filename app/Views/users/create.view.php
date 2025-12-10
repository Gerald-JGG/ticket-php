<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-person-plus"></i> Crear Nuevo Usuario</h2>
                <a href="/users" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge"></i> Información del Usuario
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

                    <form action="/users/store" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person"></i> Nombre de Usuario *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                                       placeholder="usuario123"
                                       required>
                                <small class="text-muted">Mínimo 3 caracteres, sin espacios</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Contraseña *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="••••••••"
                                       required>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rol" class="form-label">
                                <i class="bi bi-shield-check"></i> Rol del Sistema *
                            </label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccione un rol</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol->id ?>" 
                                            <?= (isset($old['rol']) && $old['rol'] == $rol->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rol->nombre) ?>
                                        <?php if ($rol->descripcion): ?>
                                            - <?= htmlspecialchars($rol->descripcion) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Correo Electrónico
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label for="departamento" class="form-label">
                                <i class="bi bi-building"></i> Departamento
                            </label>
                            <select class="form-select" id="departamento" name="departamento">
                                <option value="">Sin departamento</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?= $depto->id ?>"
                                            <?= (isset($old['departamento']) && $old['departamento'] == $depto->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($depto->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/users" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>