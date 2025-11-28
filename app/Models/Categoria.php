<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Categoria extends Model
{
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM categorias 
            ORDER BY nombre
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function allActive()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM categorias 
            WHERE activo = TRUE
            ORDER BY nombre
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM categorias 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO categorias (nombre, descripcion, icono, color) 
            VALUES (:nombre, :descripcion, :icono, :color)
        ");
        
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        $statement->bindValue(':icono', $data['icono'] ?? null);
        $statement->bindValue(':color', $data['color'] ?? null);
        
        return $statement->execute();
    }

    public static function update($id, $data)
    {
        $statement = self::connection()->prepare("
            UPDATE categorias 
            SET nombre = :nombre, 
                descripcion = :descripcion, 
                icono = :icono, 
                color = :color
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre', $data['nombre']);
        $statement->bindValue(':descripcion', $data['descripcion'] ?? null);
        $statement->bindValue(':icono', $data['icono'] ?? null);
        $statement->bindValue(':color', $data['color'] ?? null);
        
        return $statement->execute();
    }

    public static function delete($id)
    {
        $statement = self::connection()->prepare("
            UPDATE categorias 
            SET activo = FALSE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    public static function activate($id)
    {
        $statement = self::connection()->prepare("
            UPDATE categorias 
            SET activo = TRUE 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }
}