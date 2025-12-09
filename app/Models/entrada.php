<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Entrada extends Model
{
    /**
     * Obtener todas las entradas de un ticket
     */
    public static function getByTicket($ticketId)
    {
        $statement = self::connection()->prepare("
            SELECT e.*, 
                   u.nombre_completo as autor_nombre,
                   r.nombre as autor_rol,
                   ea.nombre as estado_anterior,
                   en.nombre as estado_nuevo
            FROM entradas e
            LEFT JOIN usuarios u ON e.autor_id = u.id
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN estados ea ON e.estado_anterior_id = ea.id
            LEFT JOIN estados en ON e.estado_nuevo_id = en.id
            WHERE e.ticket_id = :ticketId
            ORDER BY e.fecha_creacion ASC
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Crear nueva entrada
     */
    public static function create($data)
    {
        $estadoAnteriorId = null;
        $estadoNuevoId = null;
        
        // Obtener IDs de estados si se proporcionan nombres
        if (isset($data['estado_anterior']) && !empty($data['estado_anterior'])) {
            $estadoAnteriorId = self::getEstadoIdByName($data['estado_anterior']);
        }
        
        if (isset($data['estado_nuevo']) && !empty($data['estado_nuevo'])) {
            $estadoNuevoId = self::getEstadoIdByName($data['estado_nuevo']);
        }
        
        $statement = self::connection()->prepare("
            INSERT INTO entradas (ticket_id, autor_id, texto, es_interno, estado_anterior_id, estado_nuevo_id) 
            VALUES (:ticket_id, :autor_id, :texto, :es_interno, :estado_anterior_id, :estado_nuevo_id)
        ");
        
        $statement->bindValue(':ticket_id', $data['ticket_id']);
        $statement->bindValue(':autor_id', $data['autor_id']);
        $statement->bindValue(':texto', $data['texto']);
        $statement->bindValue(':es_interno', $data['es_interno'] ?? false);
        $statement->bindValue(':estado_anterior_id', $estadoAnteriorId);
        $statement->bindValue(':estado_nuevo_id', $estadoNuevoId);
        
        return $statement->execute();
    }

    /**
     * Contar entradas de un ticket
     */
    public static function countByTicket($ticketId)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM entradas 
            WHERE ticket_id = :ticketId
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Obtener la última entrada de un ticket
     */
    public static function getLastByTicket($ticketId)
    {
        $statement = self::connection()->prepare("
            SELECT e.*, 
                   u.nombre_completo as autor_nombre,
                   r.nombre as autor_rol,
                   ea.nombre as estado_anterior,
                   en.nombre as estado_nuevo
            FROM entradas e
            LEFT JOIN usuarios u ON e.autor_id = u.id
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN estados ea ON e.estado_anterior_id = ea.id
            LEFT JOIN estados en ON e.estado_nuevo_id = en.id
            WHERE e.ticket_id = :ticketId
            ORDER BY e.fecha_creacion DESC
            LIMIT 1
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Obtener entradas visibles para un usuario (no internas o si es operador)
     */
    public static function getVisibleByTicket($ticketId, $isOperator = false)
    {
        $query = "
            SELECT e.*, 
                   u.nombre_completo as autor_nombre,
                   r.nombre as autor_rol,
                   ea.nombre as estado_anterior,
                   en.nombre as estado_nuevo
            FROM entradas e
            LEFT JOIN usuarios u ON e.autor_id = u.id
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN estados ea ON e.estado_anterior_id = ea.id
            LEFT JOIN estados en ON e.estado_nuevo_id = en.id
            WHERE e.ticket_id = :ticketId
        ";
        
        // Si no es operador, solo mostrar entradas no internas
        if (!$isOperator) {
            $query .= " AND e.es_interno = FALSE";
        }
        
        $query .= " ORDER BY e.fecha_creacion ASC";
        
        $statement = self::connection()->prepare($query);
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
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
     * Obtener todas las entradas con cambios de estado
     */
    public static function getStatusChanges($ticketId)
    {
        $statement = self::connection()->prepare("
            SELECT e.*, 
                   u.nombre_completo as autor_nombre,
                   ea.nombre as estado_anterior,
                   en.nombre as estado_nuevo
            FROM entradas e
            LEFT JOIN usuarios u ON e.autor_id = u.id
            LEFT JOIN estados ea ON e.estado_anterior_id = ea.id
            LEFT JOIN estados en ON e.estado_nuevo_id = en.id
            WHERE e.ticket_id = :ticketId 
            AND e.estado_nuevo_id IS NOT NULL
            ORDER BY e.fecha_creacion ASC
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}