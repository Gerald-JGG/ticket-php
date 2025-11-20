<?php
class Usuario {
    private $conn;
    private $table = 'usuarios';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre_completo, username, password, rol, activo) 
                  VALUES (:nombre_completo, :username, :password, :rol, :activo)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre_completo', $data['nombre_completo']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':rol', $data['rol']);
        $activo = $data['activo'] ?? true;
        $stmt->bindParam(':activo', $activo);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Buscar usuario por username
     */
    public function findByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE username = :username AND activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Obtener todos los usuarios (solo para Superadministrador)
     */
    public function getAll() {
        $query = "SELECT id, nombre_completo, username, rol, activo, 
                         fecha_creacion, fecha_actualizacion 
                  FROM " . $this->table . "
                  ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getByRol($rol) {
        $query = "SELECT id, nombre_completo, username, rol, activo 
                  FROM " . $this->table . " 
                  WHERE rol = :rol AND activo = 1
                  ORDER BY nombre_completo ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Actualizar información del usuario
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre_completo = :nombre_completo, 
                      username = :username, 
                      rol = :rol
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre_completo', $data['nombre_completo']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':rol', $data['rol']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar contraseña
     */
    public function updatePassword($id, $newPassword) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', $newPassword);
        return $stmt->execute();
    }
    
    /**
     * Desactivar usuario (no eliminar)
     */
    public function desactivar($id) {
        $query = "UPDATE " . $this->table . " SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Activar usuario
     */
    public function activar($id) {
        $query = "UPDATE " . $this->table . " SET activo = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Verificar si un username ya existe
     */
    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE username = :username";
        
        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($excludeId !== null) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}