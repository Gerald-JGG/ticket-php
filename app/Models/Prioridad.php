<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Prioridad extends Model
{
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM prioridades 
            ORDER BY nivel ASC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function allActive()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM prioridades 
            WHERE activo = TRUE
            ORDER BY nivel ASC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM prioridades 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO prioridades (nombre, nivel, tiempo_respuesta_horas, color, descripcion) 
            VALUES (:nombre, :nivel, :tiempo_respuesta_horas, :color, :descripcion)
        ");
        
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':nivel', $data['nivel']);
        $statement->bindValue(':tiempo_respuesta_horas', $data['tiempo_respuesta_horas'] ?? null);
        $statement->bindValue(':color', $data['color'] ?? null);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        
        return $statement->execute();
    }

    public static function update($id, $data)
    {
        $statement = self::connection()->prepare("
            UPDATE prioridades 
            SET nombre = :nombre, 
                nivel = :nivel, 
                tiempo_respuesta_horas = :tiempo_respuesta_horas, 
                color = :color, 
                descripcion = :descripcion
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':nivel', $data['nivel']);
        $statement->bindValue(':tiempo_respuesta_horas', $data['tiempo_respuesta_horas'] ?? null);
        $statement->bindValue(':color', $data['color'] ?? null);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        
        return $statement->execute();
    }

    public static function delete($id)
    {
        $statement = self::connection()->prepare("
            UPDATE prioridades 
            SET activo = FALSE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    public static function activate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE prioridades 
            SET activo = TRUE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }
}