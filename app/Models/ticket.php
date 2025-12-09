<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Ticket extends Model
{
    /**
     * Obtener todos los tickets
     */
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   o.nombre_completo as operador_asignado,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN usuarios o ON t.operador_asignado_id = o.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            ORDER BY t.fecha_creacion DESC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar ticket por ID
     */
    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   u.email as usuario_email,
                   o.nombre_completo as operador_asignado,
                   o.email as operador_email,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN usuarios o ON t.operador_asignado_id = o.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            WHERE t.id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nuevo ticket
     */
    public static function create($data)
    {
        // Obtener el ID con estado "No Asignado"
        $estadoId = self::getEstadoIdByName('No Asignado');
        
        $statement = self::connection()->prepare("
            INSERT INTO tickets (titulo, tipo, usuario_creador_id, categoria_id, prioridad_id, estado_id) 
            VALUES (:titulo, :tipo, :usuario_creador_id, :categoria_id, :prioridad_id, :estado_id)
        ");
        
        $statement->bindValue(':titulo', $data['titulo']);
        $statement->bindValue(':tipo', $data['tipo']);
        $statement->bindValue(':usuario_creador_id', $data['usuario_creador_id']);
        $statement->bindValue(':categoria_id', $data['categoria_id'] ?? null);
        $statement->bindValue(':prioridad_id', $data['prioridad_id'] ?? null);
        $statement->bindValue(':estado_id', $estadoId);
        
        $statement->execute();
        return self::connection()->lastInsertId();
    }

    /**
     * Obtener tickets de un usuario específico
     */
    public static function getByUser($userId, $estado = 'todos')
    {
        $query = "
            SELECT t.*, 
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado
            FROM tickets t
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            WHERE t.usuario_creador_id = :userId
        ";
        
        if ($estado !== 'todos') {
            $query .= " AND e.nombre = :estado";
        }
        
        $query .= " ORDER BY t.fecha_creacion DESC";
        
        $statement = self::connection()->prepare($query);
        $statement->bindValue(':userId', $userId);
        
        if ($estado !== 'todos') {
            $statement->bindValue(':estado', $estado);
        }
        
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener tickets no asignados
     */
    public static function getUnassigned()
    {
        $statement = self::connection()->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            WHERE e.nombre = 'No Asignado'
            ORDER BY t.fecha_creacion ASC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener tickets asignados a un operador
     */
    public static function getByOperator($operatorId, $estado = 'todos')
    {
        $query = "
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            WHERE t.operador_asignado_id = :operatorId
        ";
        
        if ($estado !== 'todos') {
            $query .= " AND e.nombre = :estado";
        }
        
        $query .= " ORDER BY t.fecha_actualizacion DESC";
        
        $statement = self::connection()->prepare($query);
        $statement->bindValue(':operatorId', $operatorId);
        
        if ($estado !== 'todos') {
            $statement->bindValue(':estado', $estado);
        }
        
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todos los tickets con filtros
     */
    public static function getAllFiltered($estado, $tipo, $operador, $busqueda)
    {
        $query = "
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   o.nombre_completo as operador_asignado,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN usuarios o ON t.operador_asignado_id = o.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($estado !== 'todos') {
            $query .= " AND e.nombre = :estado";
            $params[':estado'] = $estado;
        }
        
        if ($tipo !== 'todos') {
            $query .= " AND t.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        if ($operador !== 'todos') {
            $query .= " AND t.operador_asignado_id = :operador";
            $params[':operador'] = $operador;
        }
        
        if (!empty($busqueda)) {
            $query .= " AND (t.titulo LIKE :busqueda OR t.id = :id)";
            $params[':busqueda'] = "%$busqueda%";
            $params[':id'] = $busqueda;
        }
        
        $query .= " ORDER BY t.fecha_creacion DESC";
        
        $statement = self::connection()->prepare($query);
        
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Asignar ticket a un operador
     */
    public static function assignToOperator($ticketId, $operatorId)
    {
        $estadoId = self::getEstadoIdByName('Asignado');
        
        $statement = self::connection()->prepare("
            UPDATE tickets 
            SET operador_asignado_id = :operatorId, 
                estado_id = :estadoId,
                fecha_asignacion = NOW()
            WHERE id = :ticketId
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->bindValue(':operatorId', $operatorId);
        $statement->bindValue(':estadoId', $estadoId);
        return $statement->execute();
    }

    /**
     * Actualizar estado del ticket
     */
    public static function updateStatus($ticketId, $nuevoEstado)
    {
        $estadoId = self::getEstadoIdByName($nuevoEstado);
        
        $query = "UPDATE tickets SET estado_id = :estado_id";
        
        if ($nuevoEstado === 'Cerrado') {
            $query .= ", fecha_cierre = NOW()";
        }
        
        $query .= " WHERE id = :ticketId";
        
        $statement = self::connection()->prepare($query);
        $statement->bindValue(':ticketId', $ticketId);
        $statement->bindValue(':estado_id', $estadoId);
        return $statement->execute();
    }

    /**
     * Validar si una transición de estado es válida
     */
    public static function isValidTransition($estadoActual, $nuevoEstado)
    {
        $transiciones = [
            'No Asignado' => ['Asignado'],
            'Asignado' => ['En Proceso'],
            'En Proceso' => ['En Espera de Terceros', 'Solucionado'],
            'En Espera de Terceros' => ['En Proceso'],
            'Solucionado' => ['Cerrado', 'Asignado']
        ];
        
        return isset($transiciones[$estadoActual]) && 
               in_array($nuevoEstado, $transiciones[$estadoActual]);
    }

    /**
     * Obtener ID del estado por nombre
     */
    private static function getEstadoIdByName($estadoName)
    {
        $statement = self::connection()->prepare("SELECT id FROM estados WHERE nombre = :nombre");
        $statement->bindValue(':nombre', $estadoName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result ? $result->id : null;
    }

    /**
     * Obtener todos los estados disponibles
     */
    public static function getAllEstados()
    {
        $statement = self::connection()->prepare("SELECT * FROM estados WHERE activo = TRUE ORDER BY orden");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}