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
                   uc.username as usuario_creador,
                   oa.username as operador_asignado,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado,
                   e.color as estado_color
            FROM tickets t
            LEFT JOIN usuarios uc ON t.usuario_creador_id = uc.id
            LEFT JOIN usuarios oa ON t.operador_asignado_id = oa.id
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
                   uc.username as usuario_creador,
                   uc.email as usuario_email,
                   oa.username as operador_asignado,
                   oa.email as operador_email,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado,
                   e.color as estado_color
            FROM tickets t
            LEFT JOIN usuarios uc ON t.usuario_creador_id = uc.id
            LEFT JOIN usuarios oa ON t.operador_asignado_id = oa.id
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
        // Obtener el ID del estado "No Asignado"
        $estadoNoAsignado = Estado::findByName('No Asignado');
        
        if (!$estadoNoAsignado) {
            throw new \Exception('Estado "No Asignado" no encontrado en la base de datos');
        }
        
        $statement = self::connection()->prepare("
            INSERT INTO tickets (titulo, tipo, usuario_creador_id, categoria_id, prioridad_id, estado_id) 
            VALUES (:titulo, :tipo, :usuario_creador_id, :categoria_id, :prioridad_id, :estado_id)
        ");
        
        $statement->bindValue(':titulo', $data['titulo']);
        $statement->bindValue(':tipo', $data['tipo']);
        $statement->bindValue(':usuario_creador_id', $data['usuario_creador_id']);
        $statement->bindValue(':categoria_id', $data['categoria_id'] ?? null);
        $statement->bindValue(':prioridad_id', $data['prioridad_id'] ?? null);
        $statement->bindValue(':estado_id', $estadoNoAsignado->id);
        
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
                   e.nombre as estado,
                   e.color as estado_color
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
                   uc.username as usuario_creador,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado,
                   e.color as estado_color
            FROM tickets t
            LEFT JOIN usuarios uc ON t.usuario_creador_id = uc.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            LEFT JOIN estados e ON t.estado_id = e.id
            WHERE t.operador_asignado_id IS NULL
            AND e.nombre = 'No Asignado'
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
                   uc.username as usuario_creador,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado,
                   e.color as estado_color
            FROM tickets t
            LEFT JOIN usuarios uc ON t.usuario_creador_id = uc.id
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
                   uc.username as usuario_creador,
                   oa.username as operador_asignado,
                   c.nombre as categoria_nombre,
                   c.color as categoria_color,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color,
                   e.nombre as estado,
                   e.color as estado_color
            FROM tickets t
            LEFT JOIN usuarios uc ON t.usuario_creador_id = uc.id
            LEFT JOIN usuarios oa ON t.operador_asignado_id = oa.id
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
        $estadoAsignado = Estado::findByName('Asignado');
        
        if (!$estadoAsignado) {
            throw new \Exception('Estado "Asignado" no encontrado en la base de datos');
        }
        
        $statement = self::connection()->prepare("
            UPDATE tickets 
            SET operador_asignado_id = :operatorId, 
                estado_id = :estadoId,
                fecha_asignacion = NOW()
            WHERE id = :ticketId
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->bindValue(':operatorId', $operatorId);
        $statement->bindValue(':estadoId', $estadoAsignado->id);
        return $statement->execute();
    }

    /**
     * Actualizar estado del ticket
     */
    public static function updateStatus($ticketId, $nuevoEstado)
    {
        $estado = Estado::findByName($nuevoEstado);
        
        if (!$estado) {
            throw new \Exception("Estado '$nuevoEstado' no encontrado en la base de datos");
        }
        
        $query = "UPDATE tickets SET estado_id = :estado_id";
        
        if ($nuevoEstado === 'Cerrado') {
            $query .= ", fecha_cierre = NOW()";
        }
        
        $query .= " WHERE id = :ticketId";
        
        $statement = self::connection()->prepare($query);
        $statement->bindValue(':ticketId', $ticketId);
        $statement->bindValue(':estado_id', $estado->id);
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
     * Obtener todos los estados disponibles
     */
    public static function getAllEstados()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM estados 
            WHERE activo = TRUE 
            ORDER BY id
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Contar tickets por estado
     */
    public static function countByStatus($estadoNombre)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total
            FROM tickets t
            INNER JOIN estados e ON t.estado_id = e.id
            WHERE e.nombre = :estado
        ");
        $statement->bindValue(':estado', $estadoNombre);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Obtener estadísticas generales de tickets
     */
    public static function getStats()
    {
        $statement = self::connection()->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN e.nombre = 'No Asignado' THEN 1 ELSE 0 END) as no_asignados,
                SUM(CASE WHEN e.nombre = 'Asignado' THEN 1 ELSE 0 END) as asignados,
                SUM(CASE WHEN e.nombre = 'En Proceso' THEN 1 ELSE 0 END) as en_proceso,
                SUM(CASE WHEN e.nombre = 'Solucionado' THEN 1 ELSE 0 END) as solucionados,
                SUM(CASE WHEN e.nombre = 'Cerrado' THEN 1 ELSE 0 END) as cerrados
            FROM tickets t
            INNER JOIN estados e ON t.estado_id = e.id
        ");
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }
}