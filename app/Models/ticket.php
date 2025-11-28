<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Ticket extends Model
{
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   o.nombre_completo as operador_asignado,
                   c.nombre as categoria_nombre,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN usuarios o ON t.operador_asignado_id = o.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            ORDER BY t.fecha_creacion DESC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   u.email as usuario_email,
                   o.nombre_completo as operador_asignado,
                   o.email as operador_email,
                   c.nombre as categoria_nombre,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN usuarios o ON t.operador_asignado_id = o.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            WHERE t.id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO tickets (titulo, tipo, usuario_creador_id, categoria_id, prioridad_id) 
            VALUES (:titulo, :tipo, :usuario_creador_id, :categoria_id, :prioridad_id)
        ");
        
        $statement->bindValue(':titulo', $data['titulo']);
        $statement->bindValue(':tipo', $data['tipo']);
        $statement->bindValue(':usuario_creador_id', $data['usuario_creador_id']);
        $statement->bindValue(':categoria_id', $data['categoria_id']);
        $statement->bindValue(':prioridad_id', $data['prioridad_id']);
        
        $statement->execute();
        return self::connection()->lastInsertId();
    }

    public static function getByUser($userId, $estado = 'todos')
    {
        $query = "
            SELECT t.*, 
                   c.nombre as categoria_nombre,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color
            FROM tickets t
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            WHERE t.usuario_creador_id = :userId
        ";
        
        if ($estado !== 'todos') {
            $query .= " AND t.estado = :estado";
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

    public static function getUnassigned()
    {
        $statement = self::connection()->prepare("
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   c.nombre as categoria_nombre,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            WHERE t.estado = 'No Asignado'
            ORDER BY t.fecha_creacion ASC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getByOperator($operatorId, $estado = 'todos')
    {
        $query = "
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   c.nombre as categoria_nombre,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            WHERE t.operador_asignado_id = :operatorId
        ";
        
        if ($estado !== 'todos') {
            $query .= " AND t.estado = :estado";
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

    public static function getAllFiltered($estado, $tipo, $operador, $busqueda)
    {
        $query = "
            SELECT t.*, 
                   u.nombre_completo as usuario_creador,
                   o.nombre_completo as operador_asignado,
                   c.nombre as categoria_nombre,
                   p.nombre as prioridad_nombre,
                   p.color as prioridad_color
            FROM tickets t
            LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
            LEFT JOIN usuarios o ON t.operador_asignado_id = o.id
            LEFT JOIN categorias c ON t.categoria_id = c.id
            LEFT JOIN prioridades p ON t.prioridad_id = p.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($estado !== 'todos') {
            $query .= " AND t.estado = :estado";
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

    public static function assignToOperator($ticketId, $operatorId)
    {
        $statement = self::connection()->prepare("
            UPDATE tickets 
            SET operador_asignado_id = :operatorId, 
                estado = 'Asignado',
                fecha_asignacion = NOW()
            WHERE id = :ticketId
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->bindValue(':operatorId', $operatorId);
        return $statement->execute();
    }

    public static function updateStatus($ticketId, $nuevoEstado)
    {
        $query = "UPDATE tickets SET estado = :estado";
        
        if ($nuevoEstado === 'Cerrado') {
            $query .= ", fecha_cierre = NOW()";
        }
        
        $query .= " WHERE id = :ticketId";
        
        $statement = self::connection()->prepare($query);
        $statement->bindValue(':ticketId', $ticketId);
        $statement->bindValue(':estado', $nuevoEstado);
        return $statement->execute();
    }

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
}