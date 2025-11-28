<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
        return $this->view('auth/login');
    }

    public function authenticate()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = User::findByUsername($username);

        if ($user && password_verify($password, $user->password)) {
            // Verificar si el usuario está activo
            if (!$user->activo) {
                return $this->view('auth/login', ['error' => 'Usuario desactivado']);
            }

            $_SESSION['user'] = [
                'id' => $user->id,
                'username' => $user->username,
                'rol' => $user->rol,
                'nombre_completo' => $user->nombre_completo
            ];

            // Actualizar último acceso
            User::updateLastAccess($user->id);

            header('Location: /');
        } else {
            return $this->view('auth/login', ['error' => 'Credenciales inválidas']);
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
    }
}