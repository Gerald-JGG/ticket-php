<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-person-gear text-warning"></i> Editar Mi Perfil</h2>
                <a href="/tickets" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <strong>¡Perfil actualizado exitosamente!</strong>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Imagen de perfil eliminada.</strong>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge"></i> Información Personal
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

                    <form action="/perfil/update" method="POST" enctype="multipart/form-data">
                        <!-- Imagen de Perfil -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">
                                    <i class="bi bi-image"></i> Imagen de Perfil
                                </label>
                                
                                <div class="profile-image-section">
                                    <div class="current-profile-image">
                                        <?php 
                                        $imagenUrl = \App\Models\ImagenPerfil::getProfileImageUrl($user->id);
                                        ?>
                                        <img src="<?= $imagenUrl ?>" 
                                             alt="Imagen de perfil actual" 
                                             id="preview-image"
                                             class="profile-preview">
                                    </div>
                                    
                                    <div class="profile-image-actions">
                                        <div class="mb-2">
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="profile_image" 
                                                   name="profile_image" 
                                                   accept="image/jpeg,image/png,image/gif,image/webp"
                                                   onchange="previewImage(event)">
                                            <small class="text-muted">
                                                Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB
                                            </small>
                                        </div>
                                        
                                        <?php if ($imagenPerfil): ?>
                                            <a href="/perfil/delete-image" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Está seguro de eliminar su imagen de perfil?')">
                                                <i class="bi bi-trash"></i> Eliminar Imagen Actual
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Información de Usuario -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person"></i> Nombre de Usuario *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       value="<?= htmlspecialchars($user->username) ?>"
                                       required>
                                <small class="text-muted">Mínimo 3 caracteres</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i> Correo Electrónico
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($user->email ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Dejar en blanco para mantener la actual">
                                <small class="text-muted">Solo completar si desea cambiarla (mín. 6 caracteres)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="departamento" class="form-label">
                                    <i class="bi bi-building"></i> Departamento
                                </label>
                                <select class="form-select" id="departamento" name="departamento">
                                    <option value="">Sin departamento</option>
                                    <?php foreach ($departamentos as $depto): ?>
                                        <option value="<?= $depto->id ?>"
                                                <?= ($user->departamento_id == $depto->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($depto->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Información de Solo Lectura -->
                        <div class="card bg-dark mb-3">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bi bi-info-circle"></i> Información de la Cuenta
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong>Rol:</strong>
                                            <span class="badge badge-orange ms-2">
                                                <?= htmlspecialchars($user->rol) ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong>Estado:</strong>
                                            <?php if ($user->activo): ?>
                                                <span class="badge bg-success ms-2">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary ms-2">Inactivo</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong>Fecha de Creación:</strong><br>
                                            <small><?= date('d/m/Y H:i', strtotime($user->fecha_creacion)) ?></small>
                                        </p>
                                    </div>
                                </div>
                                <?php if ($user->ultimo_acceso): ?>
                                    <p class="mb-0 mt-2">
                                        <strong>Último Acceso:</strong>
                                        <small><?= date('d/m/Y H:i', strtotime($user->ultimo_acceso)) ?></small>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/tickets" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>