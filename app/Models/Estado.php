<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Estado extends Model
{
    /**
     * Obtener todos los estados
     */
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM estados 
            ORDER BY orden
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todos los estados activos
     */
    public static function allActive()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM estados 
            WHERE activo = TRUE
            ORDER BY orden
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar estado por ID
     */
    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM estados 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Buscar estado por nombre
     */
    public static function findByName($nombre)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM estados 
            WHERE nombre = :nombre
        ");
        $statement->bindValue(':nombre', $nombre);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nuevo estado
     */
    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO estados (nombre, descripcion, orden) 
            VALUES (:nombre, :descripcion, :orden)
        ");
        
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        $statement->bindValue(':orden', $data['orden']);
        
        return $statement->execute();
    }

    /**
     * Actualizar estado
     */
    public static function update($id, $data)
    {
        $statement = self::connection()->prepare("
            UPDATE estados 
            SET nombre = :nombre, 
                descripcion = :descripcion, 
                orden = :orden
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        $statement->bindValue(':orden', $data['orden']);
        
        return $statement->execute();
    }

    /**
     * Desactivar estado
     */
    public static function deactivate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE estados 
            SET activo = FALSE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Activar estado
     */
    public static function activate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE estados 
            SET activo = TRUE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Contar tickets en este estado
     */
    public static function countTickets($estadoId)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM tickets 
            WHERE estado_id = :estado_id
        ");
        $statement->bindValue(':estado_id', $estadoId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Obtener estados disponibles para transición desde un estado específico
     */
    public static function getAvailableTransitions($estadoActual)
    {
        $transiciones = [
            'No Asignado' => ['Asignado'],
            'Asignado' => ['En Proceso'],
            'En Proceso' => ['En Espera de Terceros', 'Solucionado'],
            'En Espera de Terceros' => ['En Proceso'],
            'Solucionado' => ['Cerrado', 'Asignado']
        ];
        
        if (!isset($transiciones[$estadoActual])) {
            return [];
        }
        
        $nombresEstados = $transiciones[$estadoActual];
        
        $placeholders = str_repeat('?,', count($nombresEstados) - 1) . '?';
        $statement = self::connection()->prepare("
            SELECT * FROM estados 
            WHERE nombre IN ($placeholders) 
            AND activo = TRUE
            ORDER BY orden
        ");
        $statement->execute($nombresEstados);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener estadísticas de tickets por estado
     */
    public static function getTicketStats()
    {
        $statement = self::connection()->prepare("
            SELECT e.id, e.nombre, e.descripcion, COUNT(t.id) as total_tickets
            FROM estados e
            LEFT JOIN tickets t ON e.id = t.estado_id
            WHERE e.activo = TRUE
            GROUP BY e.id, e.nombre, e.descripcion
            ORDER BY e.orden
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}