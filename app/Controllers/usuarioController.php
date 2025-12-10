<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Rol;
use App\Models\Departamento;

class UsuarioController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Solo Superadministrador puede acceder
        $user = User::find($_SESSION['user']['id']);
        if (!$user || $user->rol !== 'Superadministrador') {
            header('Location: /');
            exit;
        }
    }

    /**
     * Listar todos los usuarios
     */
    public function index()
    {
        $users = User::all();
        return $this->view('users/index', ['users' => $users]);
    }

    /**
     * Formulario de creacion de usuario
     */
    public function create()
    {
        $roles = Rol::allActive();
        $departamentos = Departamento::allActive();
        
        return $this->view('users/create', [
            'roles' => $roles,
            'departamentos' => $departamentos
        ]);
    }

    /**
     * Guardar nuevo usuario
     */
    public function store()
    {
        // Validar datos
        $errors = [];
        
        if (empty($_POST['username']) || strlen($_POST['username']) < 3) {
            $errors[] = 'El nombre de usuario es obligatorio y debe tener al menos 3 caracteres';
        }
        
        if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
            $errors[] = 'La contraseña es obligatoria y debe tener al menos 6 caracteres';
        }
        
        if (empty($_POST['rol'])) {
            $errors[] = 'Debe seleccionar un rol';
        }

        // Verificar si el username ya existe
        if (!empty($_POST['username']) && User::findByUsername($_POST['username'])) {
            $errors[] = 'El nombre de usuario ya está en uso';
        }

        if (!empty($errors)) {
            $roles = Rol::allActive();
            $departamentos = Departamento::allActive();
            return $this->view('users/create', [
                'roles' => $roles,
                'departamentos' => $departamentos,
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        $data = [
            'username' => trim($_POST['username']),
            'password' => $_POST['password'],
            'rol_id' => $_POST['rol'],
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'departamento_id' => !empty($_POST['departamento']) ? $_POST['departamento'] : null
        ];

        User::create($data);
        header('Location: /users');
    }

    /**
     * Formulario de edicion de usuario
     */
    public function edit($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            header('Location: /users');
            exit;
        }
        
        $roles = Rol::allActive();
        $departamentos = Departamento::allActive();
        
        return $this->view('users/edit', [
            'user' => $user,
            'roles' => $roles,
            'departamentos' => $departamentos
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            header('Location: /users');
            exit;
        }

        // Validar datos
        $errors = [];
        
        if (empty($_POST['username']) || strlen($_POST['username']) < 3) {
            $errors[] = 'El nombre de usuario es obligatorio y debe tener al menos 3 caracteres';
        }
        
        if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if (empty($_POST['rol'])) {
            $errors[] = 'Debe seleccionar un rol';
        }

        // Verificar si el username está siendo usado por otro usuario
        if (!empty($_POST['username'])) {
            $existingUser = User::findByUsername($_POST['username']);
            if ($existingUser && $existingUser->id != $id) {
                $errors[] = 'El nombre de usuario ya está en uso por otro usuario';
            }
        }

        if (!empty($errors)) {
            $roles = Rol::allActive();
            $departamentos = Departamento::allActive();
            return $this->view('users/edit', [
                'user' => $user,
                'roles' => $roles,
                'departamentos' => $departamentos,
                'errors' => $errors
            ]);
        }

        $data = [
            'username' => trim($_POST['username']),
            'rol_id' => $_POST['rol'],
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'departamento_id' => !empty($_POST['departamento']) ? $_POST['departamento'] : null
        ];

        // Si se proporciona nueva contraseña
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        User::updateUser($id, $data);
        header('Location: /users');
    }

    /**
     * Desactivar usuario
     */
    public function deactivate($id)
    {
        // No permitir desactivar al propio superadministrador
        if ($id == $_SESSION['user']['id']) {
            header('Location: /users?error=no_puede_desactivarse');
            exit;
        }

        User::deactivate($id);
        header('Location: /users');
    }

    /**
     * Activar usuario
     */
    public function activate($id)
    {
        User::activate($id);
        header('Location: /users');
    }
}