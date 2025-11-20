<?php
class Entrada {
    private $conn;
    private $table = 'entradas';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crear una nueva entrada/comentario
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (ticket_id, autor_id, texto, estado_anterior, estado_nuevo) 
                  VALUES (:ticket_id, :autor_id, :texto, :estado_anterior, :estado_nuevo)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':ticket_id', $data['ticket_id']);
        $stmt->bindParam(':autor_id', $data['autor_id']);
        $stmt->bindParam(':texto', $data['texto']);
        
        // Estados pueden ser NULL si no hay cambio de estado
        $estadoAnterior = $data['estado_anterior'] ?? null;
        $estadoNuevo = $data['estado_nuevo'] ?? null;
        
        $stmt->bindParam(':estado_anterior', $estadoAnterior);
        $stmt->bindParam(':estado_nuevo', $estadoNuevo);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener todas las entradas de un ticket (historial completo)
     */
    public function getByTicketId($ticketId) {
        $query = "SELECT e.*, 
                         u.nombre_completo as autor_nombre, 
                         u.username as autor_username,
                         u.rol as autor_rol
                  FROM " . $this->table . " e
                  INNER JOIN usuarios u ON e.autor_id = u.id
                  WHERE e.ticket_id = :ticket_id
                  ORDER BY e.fecha_creacion ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener entrada por ID
     */
    public function findById($id) {
        $query = "SELECT e.*, 
                         u.nombre_completo as autor_nombre, u.username as autor_username
                  FROM " . $this->table . " e
                  INNER JOIN usuarios u ON e.autor_id = u.id
                  WHERE e.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Contar entradas de un ticket
     */
    public function countByTicketId($ticketId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE ticket_id = :ticket_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Obtener última entrada de un ticket
     */
    public function getUltimaEntrada($ticketId) {
        $query = "SELECT e.*, 
                         u.nombre_completo as autor_nombre
                  FROM " . $this->table . " e
                  INNER JOIN usuarios u ON e.autor_id = u.id
                  WHERE e.ticket_id = :ticket_id
                  ORDER BY e.fecha_creacion DESC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticketId);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>