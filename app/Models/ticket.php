<?php
class Ticket {
    private $conn;
    private $table = 'tickets';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crear un nuevo ticket
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (titulo, tipo, usuario_creador_id, estado) 
                  VALUES (:titulo, :tipo, :usuario_creador_id, 'No Asignado')";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':usuario_creador_id', $data['usuario_creador_id']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener ticket por ID con información del creador y operador
     */
    public function findById($id) {
        $query = "SELECT t.*, 
                         uc.nombre_completo as creador_nombre, uc.username as creador_username,
                         op.nombre_completo as operador_nombre, op.username as operador_username
                  FROM " . $this->table . " t
                  INNER JOIN usuarios uc ON t.usuario_creador_id = uc.id
                  LEFT JOIN usuarios op ON t.operador_asignado_id = op.id
                  WHERE t.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Obtener tickets creados por un usuario específico
     */
    public function getByUsuarioCreador($usuarioId, $estado = null) {
        $query = "SELECT t.*, 
                         op.nombre_completo as operador_nombre
                  FROM " . $this->table . " t
                  LEFT JOIN usuarios op ON t.operador_asignado_id = op.id
                  WHERE t.usuario_creador_id = :usuario_id";
        
        if ($estado !== null) {
            $query .= " AND t.estado = :estado";
        }
        
        $query .= " ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuarioId);
        
        if ($estado !== null) {
            $stmt->bindParam(':estado', $estado);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener tickets no asignados (cola global para operadores)
     */
    public function getNoAsignados() {
        $query = "SELECT t.*, 
                         u.nombre_completo as creador_nombre, u.username as creador_username
                  FROM " . $this->table . " t
                  INNER JOIN usuarios u ON t.usuario_creador_id = u.id
                  WHERE t.estado = 'No Asignado'
                  ORDER BY t.fecha_creacion ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener tickets asignados a un operador específico
     */
    public function getByOperadorAsignado($operadorId, $estado = null) {
        $query = "SELECT t.*, 
                         u.nombre_completo as creador_nombre, u.username as creador_username
                  FROM " . $this->table . " t
                  INNER JOIN usuarios u ON t.usuario_creador_id = u.id
                  WHERE t.operador_asignado_id = :operador_id";
        
        if ($estado !== null) {
            $query .= " AND t.estado = :estado";
        }
        
        $query .= " ORDER BY t.fecha_actualizacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':operador_id', $operadorId);
        
        if ($estado !== null) {
            $stmt->bindParam(':estado', $estado);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todos los tickets (para Superadministrador)
     */
    public function getAll($filtros = []) {
        $query = "SELECT t.*, 
                         uc.nombre_completo as creador_nombre,
                         op.nombre_completo as operador_nombre
                  FROM " . $this->table . " t
                  INNER JOIN usuarios uc ON t.usuario_creador_id = uc.id
                  LEFT JOIN usuarios op ON t.operador_asignado_id = op.id
                  WHERE 1=1";
        
        // Aplicar filtros
        if (isset($filtros['estado'])) {
            $query .= " AND t.estado = :estado";
        }
        if (isset($filtros['tipo'])) {
            $query .= " AND t.tipo = :tipo";
        }
        if (isset($filtros['operador_id'])) {
            $query .= " AND t.operador_asignado_id = :operador_id";
        }
        if (isset($filtros['busqueda'])) {
            $query .= " AND (t.id = :busqueda_id OR t.titulo LIKE :busqueda_texto)";
        }
        
        $query .= " ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind de parámetros de filtro
        if (isset($filtros['estado'])) {
            $stmt->bindParam(':estado', $filtros['estado']);
        }
        if (isset($filtros['tipo'])) {
            $stmt->bindParam(':tipo', $filtros['tipo']);
        }
        if (isset($filtros['operador_id'])) {
            $stmt->bindParam(':operador_id', $filtros['operador_id']);
        }
        if (isset($filtros['busqueda'])) {
            $stmt->bindParam(':busqueda_id', $filtros['busqueda']);
            $busquedaTexto = '%' . $filtros['busqueda'] . '%';
            $stmt->bindParam(':busqueda_texto', $busquedaTexto);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Asignar ticket a un operador
     */
    public function asignarOperador($ticketId, $operadorId) {
        $query = "UPDATE " . $this->table . " 
                  SET operador_asignado_id = :operador_id, 
                      estado = 'Asignado',
                      fecha_asignacion = NOW()
                  WHERE id = :ticket_id AND estado = 'No Asignado'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->bindParam(':operador_id', $operadorId);
        
        return $stmt->execute();
    }
    
    /**
     * Cambiar estado del ticket
     */
    public function cambiarEstado($ticketId, $nuevoEstado) {
        $query = "UPDATE " . $this->table . " 
                  SET estado = :nuevo_estado";
        
        // Si el estado es Cerrado, registrar fecha de cierre
        if ($nuevoEstado === 'Cerrado') {
            $query .= ", fecha_cierre = NOW()";
        }
        
        $query .= " WHERE id = :ticket_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->bindParam(':nuevo_estado', $nuevoEstado);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener estadísticas de tickets por estado
     */
    public function getEstadisticas($usuarioId = null, $rol = null) {
        $query = "SELECT estado, COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE 1=1";
        
        if ($rol === 'Usuario' && $usuarioId !== null) {
            $query .= " AND usuario_creador_id = :usuario_id";
        } elseif ($rol === 'Operador' && $usuarioId !== null) {
            $query .= " AND operador_asignado_id = :usuario_id";
        }
        
        $query .= " GROUP BY estado";
        
        $stmt = $this->conn->prepare($query);
        
        if ($usuarioId !== null && in_array($rol, ['Usuario', 'Operador'])) {
            $stmt->bindParam(':usuario_id', $usuarioId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}