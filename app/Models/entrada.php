<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Entrada extends Model
{
    public static function getByTicket($ticketId)
    {
        $statement = self::connection()->prepare("
            SELECT e.*, 
                   u.nombre_completo as autor_nombre,
                   u.rol as autor_rol
            FROM entradas e
            LEFT JOIN usuarios u ON e.autor_id = u.id
            WHERE e.ticket_id = :ticketId
            ORDER BY e.fecha_creacion ASC
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO entradas (ticket_id, autor_id, texto, es_interno, estado_anterior, estado_nuevo) 
            VALUES (:ticket_id, :autor_id, :texto, :es_interno, :estado_anterior, :estado_nuevo)
        ");
        
        $statement->bindValue(':ticket_id', $data['ticket_id']);
        $statement->bindValue(':autor_id', $data['autor_id']);
        $statement->bindValue(':texto', $data['texto']);
        $statement->bindValue(':es_interno', $data['es_interno'] ?? false);
        $statement->bindValue(':estado_anterior', $data['estado_anterior'] ?? null);
        $statement->bindValue(':estado_nuevo', $data['estado_nuevo'] ?? null);
        
        return $statement->execute();
    }

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

    public static function getLastByTicket($ticketId)
    {
        $statement = self::connection()->prepare("
            SELECT e.*, 
                   u.nombre_completo as autor_nombre,
                   u.rol as autor_rol
            FROM entradas e
            LEFT JOIN usuarios u ON e.autor_id = u.id
            WHERE e.ticket_id = :ticketId
            ORDER BY e.fecha_creacion DESC
            LIMIT 1
        ");
        $statement->bindValue(':ticketId', $ticketId);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }
}