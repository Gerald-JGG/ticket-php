<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function login()
    {
        // Si ya está autenticado, redirigir
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
        
        return $this->view('auth/login');
    }

    /**
     * Procesar autenticación
     */
    public function authenticate()
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar que los campos no estén vacíos
        if (empty($username) || empty($password)) {
            return $this->view('auth/login', [
                'error' => 'Por favor ingrese usuario y contraseña'
            ]);
        }

        // Buscar usuario
        $user = User::findByUsername($username);

        // Validar usuario y contraseña
        if (!$user || !password_verify($password, $user->password)) {
            return $this->view('auth/login', [
                'error' => 'Credenciales inválidas'
            ]);
        }

        // Verificar si el usuario está activo
        if (!$user->activo) {
            return $this->view('auth/login', [
                'error' => 'Usuario desactivado. Contacte al administrador.'
            ]);
        }

        // Crear sesión
        $_SESSION['user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'rol' => $user->rol, // Ahora viene del JOIN con la tabla roles
            'nombre_completo' => $user->nombre_completo
        ];

        // Actualizar último acceso
        User::updateLastAccess($user->id);

        // Redirigir al dashboard
        header('Location: /');
        exit;
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        // Destruir todas las variables de sesión
        $_SESSION = [];

        // Destruir la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        // Redirigir al login
        header('Location: /login');
        exit;
    }
}