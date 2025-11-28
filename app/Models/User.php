<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT id, nombre_completo, username, rol, email, telefono, 
                   departamento, activo, fecha_creacion, ultimo_acceso 
            FROM usuarios 
            ORDER BY nombre_completo
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT id, nombre_completo, username, password, rol, email, 
                   telefono, departamento, activo, fecha_creacion, ultimo_acceso 
            FROM usuarios 
            WHERE id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public static function findByUsername($username)
    {
        $statement = self::connection()->prepare("
            SELECT id, nombre_completo, username, password, rol, email, 
                   telefono, departamento, activo, fecha_creacion, ultimo_acceso 
            FROM usuarios 
            WHERE username = :username
        ");
        $statement->bindValue(':username', $username);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public static function create($data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $statement = self::connection()->prepare("
            INSERT INTO usuarios (nombre_completo, username, password, rol, email, telefono, departamento) 
            VALUES (:nombre_completo, :username, :password, :rol, :email, :telefono, :departamento)
        ");
        
        $statement->bindValue(':nombre_completo', $data['nombre_completo']);
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':rol', $data['rol']);
        $statement->bindValue(':email', $data['email']);
        $statement->bindValue(':telefono', $data['telefono']);
        $statement->bindValue(':departamento', $data['departamento']);
        
        return $statement->execute();
    }

    public static function updateUser($id, $data)
    {
        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $statement = self::connection()->prepare("
                UPDATE usuarios 
                SET nombre_completo = :nombre_completo, 
                    username = :username, 
                    password = :password,
                    rol = :rol, 
                    email = :email, 
                    telefono = :telefono, 
                    departamento = :departamento
                WHERE id = :id
            ");
            $statement->bindValue(':password', $password);
        } else {
            $statement = self::connection()->prepare("
                UPDATE usuarios 
                SET nombre_completo = :nombre_completo, 
                    username = :username, 
                    rol = :rol, 
                    email = :email, 
                    telefono = :telefono, 
                    departamento = :departamento
                WHERE id = :id
            ");
        }

        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre_completo', $data['nombre_completo']);
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':rol', $data['rol']);
        $statement->bindValue(':email', $data['email']);
        $statement->bindValue(':telefono', $data['telefono']);
        $statement->bindValue(':departamento', $data['departamento']);
        
        return $statement->execute();
    }

    public static function deactivate($id)
    {
        $statement = self::connection()->prepare("UPDATE usuarios SET activo = FALSE WHERE id = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    public static function activate($id)
    {
        $statement = self::connection()->prepare("UPDATE usuarios SET activo = TRUE WHERE id = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    public static function updateLastAccess($id)
    {
        $statement = self::connection()->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    public static function getOperators()
    {
        $statement = self::connection()->prepare("
            SELECT id, nombre_completo, username 
            FROM usuarios 
            WHERE rol = 'Operador' AND activo = TRUE
            ORDER BY nombre_completo
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}