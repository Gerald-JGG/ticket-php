<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\ImagenPerfil;
use App\Models\Departamento;

class PerfilController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Mostrar formulario de edición de perfil
     */
    public function edit()
    {
        $user = User::find($_SESSION['user']['id']);
        $departamentos = Departamento::allActive();
        $imagenPerfil = ImagenPerfil::findByUserId($user->id);
        
        return $this->view('perfil/edit', [
            'user' => $user,
            'departamentos' => $departamentos,
            'imagenPerfil' => $imagenPerfil
        ]);
    }

    /**
     * Actualizar perfil del usuario
     */
    public function update()
    {
        $userId = $_SESSION['user']['id'];
        $user = User::find($userId);
        
        // Validar datos
        $errors = [];
        
        if (empty($_POST['username']) || strlen($_POST['username']) < 3) {
            $errors[] = 'El nombre de usuario es obligatorio y debe tener al menos 3 caracteres';
        }
        
        // Verificar si el username está siendo usado por otro usuario
        if (!empty($_POST['username'])) {
            $existingUser = User::findByUsername($_POST['username']);
            if ($existingUser && $existingUser->id != $userId) {
                $errors[] = 'El nombre de usuario ya está en uso por otro usuario';
            }
        }
        
        // Validar contraseña si se proporciona
        if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar imagen si se sube
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['profile_image']['type'], $allowedTypes)) {
                $errors[] = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP';
            }
            
            if ($_FILES['profile_image']['size'] > $maxSize) {
                $errors[] = 'La imagen no debe superar los 5MB';
            }
        }
        
        if (!empty($errors)) {
            $departamentos = Departamento::allActive();
            $imagenPerfil = ImagenPerfil::findByUserId($userId);
            return $this->view('perfil/edit', [
                'user' => $user,
                'departamentos' => $departamentos,
                'imagenPerfil' => $imagenPerfil,
                'errors' => $errors
            ]);
        }
        
        // Actualizar datos del usuario
        $data = [
            'username' => trim($_POST['username']),
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'departamento_id' => !empty($_POST['departamento']) ? $_POST['departamento'] : null,
            'rol_id' => $user->rol_id // Mantener el rol actual
        ];
        
        // Si se proporciona nueva contraseña
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        User::updateUser($userId, $data);
        
        // Actualizar sesión
        $_SESSION['user']['username'] = $data['username'];
        $_SESSION['user']['email'] = $data['email'];
        
        // Procesar imagen de perfil si se subió
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $this->uploadProfileImage($userId, $_FILES['profile_image']);
        }
        
        header('Location: /perfil/edit?success=1');
        exit;
    }

    /**
     * Subir imagen de perfil
     */
    private function uploadProfileImage($userId, $file)
    {
        // Crear directorio si no existe
        $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreGuardado = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $ruta = $uploadDir . $nombreGuardado;
        $rutaRelativa = 'uploads/profiles/' . $nombreGuardado;
        
        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $ruta)) {
            // Datos de la imagen
            $imageData = [
                'usuario_id' => $userId,
                'nombre_original' => $file['name'],
                'nombre_guardado' => $nombreGuardado,
                'ruta' => $rutaRelativa,
                'tipo_mime' => $file['type'],
                'tamano_bytes' => $file['size']
            ];
            
            // Verificar si ya existe una imagen y actualizar o crear nueva
            if (ImagenPerfil::hasProfileImage($userId)) {
                // Eliminar imagen anterior
                ImagenPerfil::delete($userId);
            }
            
            ImagenPerfil::create($imageData);
        }
    }

    /**
     * Eliminar imagen de perfil
     */
    public function deleteImage()
    {
        $userId = $_SESSION['user']['id'];
        ImagenPerfil::delete($userId);
        
        header('Location: /perfil/edit?deleted=1');
        exit;
    }
}