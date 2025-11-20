<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Usuario.php';

class AuthController {
    private $db;
    private $usuarioModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }
    
    /**
     * Iniciar sesión
     */
    public function login($username, $password) {
        $usuario = $this->usuarioModel->findByUsername($username);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Verificar que el usuario esté activo
            if (!$usuario['activo']) {
                return ['success' => false, 'message' => 'Usuario desactivado'];
            }
            
            // Iniciar sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['rol'];
            
            return [
                'success' => true, 
                'message' => 'Inicio de sesión exitoso',
                'rol' => $usuario['rol'],
                'user_id' => $usuario['id']
            ];
        }
        
        return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada exitosamente'];
    }
    
    /**
     * Verificar si hay una sesión activa
     */
    public function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($rol) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['rol'])) {
            return false;
        }
        return $_SESSION['rol'] === $rol;
    }
    
    /**
     * Obtener información del usuario autenticado
     */
    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'nombre_completo' => $_SESSION['nombre_completo'],
            'rol' => $_SESSION['rol']
        ];
    }
    
    /**
     * Redirigir según el rol del usuario
     */
    public function redirectByRole() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol'])) {
            return 'login.php';
        }
        
        switch ($_SESSION['rol']) {
            case 'Superadministrador':
                return 'dashboard/admin.php';
            case 'Operador':
                return 'dashboard/operador.php';
            case 'Usuario':
                return 'dashboard/usuario.php';
            default:
                return 'login.php';
        }
    }
}
?>