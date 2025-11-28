<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

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
        if ($user->rol !== 'Superadministrador') {
            header('Location: /');
            exit;
        }
    }

    public function index()
    {
        $users = User::all();
        return $this->view('users/index', ['users' => $users]);
    }

    public function create()
    {
        return $this->view('users/create');
    }

    public function store()
    {
        $data = [
            'nombre_completo' => $_POST['nombre_completo'],
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'rol' => $_POST['rol'],
            'email' => $_POST['email'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'departamento' => $_POST['departamento'] ?? null
        ];

        if (User::findByUsername($data['username'])) {
            return $this->view('users/create', ['error' => 'El usuario ya existe']);
        }

        User::create($data);
        header('Location: /users');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return $this->view('users/edit', ['user' => $user]);
    }

    public function update($id)
    {
        $data = [
            'nombre_completo' => $_POST['nombre_completo'],
            'username' => $_POST['username'],
            'rol' => $_POST['rol'],
            'email' => $_POST['email'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'departamento' => $_POST['departamento'] ?? null
        ];

        // Si se proporciona nueva contraseña
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        // Verificar si el username está siendo usado por otro usuario
        $existingUser = User::findByUsername($data['username']);
        if ($existingUser && $existingUser->id != $id) {
            $user = User::find($id);
            return $this->view('users/edit', ['user' => $user, 'error' => 'El username ya está en uso']);
        }

        User::updateUser($id, $data);
        header('Location: /users');
    }

    public function deactivate($id)
    {
        User::deactivate($id);
        header('Location: /users');
    }

    public function activate($id)
    {
        User::activate($id);
        header('Location: /users');
    }
}