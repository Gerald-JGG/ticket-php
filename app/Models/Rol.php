<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Rol extends Model
{
    /**
     * Obtener todos los roles
     */
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM roles 
            ORDER BY nivel_acceso DESC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todos los roles activos
     */
    public static function allActive()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM roles 
            WHERE activo = TRUE
            ORDER BY nivel_acceso DESC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar rol por ID
     */
    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM roles 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Buscar rol por nombre
     */
    public static function findByName($nombre)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM roles 
            WHERE nombre = :nombre
        ");
        $statement->bindValue(':nombre', $nombre);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nuevo rol
     */
    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO roles (nombre, descripcion, nivel_acceso) 
            VALUES (:nombre, :descripcion, :nivel_acceso)
        ");
        
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        $statement->bindValue(':nivel_acceso', $data['nivel_acceso']);
        
        return $statement->execute();
    }

    /**
     * Actualizar rol
     */
    public static function update($id, $data)
    {
        $statement = self::connection()->prepare("
            UPDATE roles 
            SET nombre = :nombre, 
                descripcion = :descripcion, 
                nivel_acceso = :nivel_acceso
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        $statement->bindValue(':nivel_acceso', $data['nivel_acceso']);
        
        return $statement->execute();
    }

    /**
     * Desactivar rol
     */
    public static function deactivate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE roles 
            SET activo = FALSE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Activar rol
     */
    public static function activate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE roles 
            SET activo = TRUE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Contar usuarios con este rol
     */
    public static function countUsers($rolId)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM usuarios 
            WHERE rol_id = :rol_id
        ");
        $statement->bindValue(':rol_id', $rolId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
}