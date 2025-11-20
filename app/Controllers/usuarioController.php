<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Usuario.php';

class UsuarioController {
    private $db;
    private $usuarioModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }
    
    /**
     * Crear un nuevo usuario (solo Superadministrador)
     */
    public function create($data) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar permisos
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No tiene permisos para realizar esta acción'];
        }
        
        // Validar datos requeridos
        if (empty($data['nombre_completo']) || empty($data['username']) || 
            empty($data['password']) || empty($data['rol'])) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }
        
        // Verificar que el username no exista
        if ($this->usuarioModel->usernameExists($data['username'])) {
            return ['success' => false, 'message' => 'El nombre de usuario ya existe'];
        }
        
        // Validar rol
        $rolesValidos = ['Superadministrador', 'Operador', 'Usuario'];
        if (!in_array($data['rol'], $rolesValidos)) {
            return ['success' => false, 'message' => 'Rol no válido'];
        }
        
        // Hash de la contraseña
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Crear usuario
        $userId = $this->usuarioModel->create($data);
        
        if ($userId) {
            return [
                'success' => true, 
                'message' => 'Usuario creado exitosamente',
                'user_id' => $userId
            ];
        }
        
        return ['success' => false, 'message' => 'Error al crear usuario'];
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function getAll() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $usuarios = $this->usuarioModel->getAll();
        return ['success' => true, 'usuarios' => $usuarios];
    }
    
    /**
     * Obtener usuario por ID
     */
    public function getById($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $usuario = $this->usuarioModel->findById($id);
        
        if ($usuario) {
            // No enviar la contraseña
            unset($usuario['password']);
            return ['success' => true, 'usuario' => $usuario];
        }
        
        return ['success' => false, 'message' => 'Usuario no encontrado'];
    }
    
    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        // Verificar que el usuario existe
        $usuario = $this->usuarioModel->findById($id);
        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        // Verificar username único (excluyendo el usuario actual)
        if (isset($data['username']) && 
            $this->usuarioModel->usernameExists($data['username'], $id)) {
            return ['success' => false, 'message' => 'El nombre de usuario ya existe'];
        }
        
        // Actualizar usuario
        if ($this->usuarioModel->update($id, $data)) {
            // Si se proporcionó nueva contraseña, actualizarla
            if (!empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $this->usuarioModel->updatePassword($id, $hashedPassword);
            }
            
            return ['success' => true, 'message' => 'Usuario actualizado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al actualizar usuario'];
    }
    
    /**
     * Desactivar usuario
     */
    public function desactivar($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        // No permitir desactivar al mismo usuario
        if ($id == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'No puede desactivar su propia cuenta'];
        }
        
        if ($this->usuarioModel->desactivar($id)) {
            return ['success' => true, 'message' => 'Usuario desactivado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al desactivar usuario'];
    }
    
    /**
     * Activar usuario
     */
    public function activar($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        if ($this->usuarioModel->activar($id)) {
            return ['success' => true, 'message' => 'Usuario activado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al activar usuario'];
    }
    
    /**
     * Obtener operadores activos (para asignar tickets)
     */
    public function getOperadores() {
        $operadores = $this->usuarioModel->getByRol('Operador');
        return ['success' => true, 'operadores' => $operadores];
    }
}