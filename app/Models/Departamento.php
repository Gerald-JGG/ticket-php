<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Departamento extends Model
{
    /**
     * Obtener todos los departamentos
     */
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM departamentos 
            ORDER BY nombre
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todos los departamentos activos
     */
    public static function allActive()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM departamentos 
            WHERE activo = TRUE
            ORDER BY nombre
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar departamento por ID
     */
    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM departamentos 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Buscar departamento por nombre
     */
    public static function findByName($nombre)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM departamentos 
            WHERE nombre = :nombre
        ");
        $statement->bindValue(':nombre', $nombre);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nuevo departamento
     */
    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO departamentos (nombre, descripcion) 
            VALUES (:nombre, :descripcion)
        ");
        
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        
        return $statement->execute();
    }

    /**
     * Actualizar departamento
     */
    public static function update($id, $data)
    {
        $statement = self::connection()->prepare("
            UPDATE departamentos 
            SET nombre = :nombre, 
                descripcion = :descripcion
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        
        return $statement->execute();
    }

    /**
     * Desactivar departamento
     */
    public static function deactivate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE departamentos 
            SET activo = FALSE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Activar departamento
     */
    public static function activate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE departamentos 
            SET activo = TRUE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Contar usuarios en este departamento
     */
    public static function countUsers($departamentoId)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM usuarios 
            WHERE departamento_id = :departamento_id
        ");
        $statement->bindValue(':departamento_id', $departamentoId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Obtener usuarios de un departamento
     */
    public static function getUsers($departamentoId)
    {
        $statement = self::connection()->prepare("
            SELECT u.id, u.nombre_completo, u.username, u.email, r.nombre as rol
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.departamento_id = :departamento_id
            AND u.activo = TRUE
            ORDER BY u.nombre_completo
        ");
        $statement->bindValue(':departamento_id', $departamentoId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}