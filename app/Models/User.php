<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    /**
     * Obtener todos los usuarios con información completa
     */
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT u.id, u.username, u.email, u.departamento_id,
                   u.activo, u.fecha_creacion, u.ultimo_acceso,
                   r.nombre as rol, r.id as rol_id,
                   d.nombre as departamento
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            LEFT JOIN departamentos d ON u.departamento_id = d.id
            ORDER BY u.username
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar usuario por ID
     */
    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT u.id, u.username, u.password, u.email,
                   u.activo, u.fecha_creacion, u.ultimo_acceso,
                   u.rol_id, u.departamento_id,
                   r.nombre as rol,
                   d.nombre as departamento
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            LEFT JOIN departamentos d ON u.departamento_id = d.id
            WHERE u.id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Buscar usuario por username
     */
    public static function findByUsername($username)
    {
        $statement = self::connection()->prepare("
            SELECT u.id, u.username, u.password, u.email,
                   u.activo, u.fecha_creacion, u.ultimo_acceso,
                   u.rol_id, u.departamento_id,
                   r.nombre as rol,
                   d.nombre as departamento
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            LEFT JOIN departamentos d ON u.departamento_id = d.id
            WHERE u.username = :username
        ");
        $statement->bindValue(':username', $username);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nuevo usuario
     */
    public static function create($data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        $statement = self::connection()->prepare("
            INSERT INTO usuarios (username, password, rol_id, email, departamento_id) 
            VALUES (:username, :password, :rol_id, :email, :departamento_id)
        ");

        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':rol_id', $data['rol_id']);
        $statement->bindValue(':email', $data['email'] ?? null);
        $statement->bindValue(':departamento_id', $data['departamento_id'] ?? null);

        return $statement->execute();
    }

    /**
     * Actualizar usuario existente
     */
    public static function updateUser($id, $data)
    {
        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $statement = self::connection()->prepare("
                UPDATE usuarios 
                SET username = :username, 
                    password = :password,
                    rol_id = :rol_id, 
                    email = :email, 
                    departamento_id = :departamento_id
                WHERE id = :id
            ");
            $statement->bindValue(':password', $password);
        } else {
            $statement = self::connection()->prepare("
                UPDATE usuarios 
                SET username = :username, 
                    rol_id = :rol_id, 
                    email = :email, 
                    departamento_id = :departamento_id
                WHERE id = :id
            ");
        }

        $statement->bindValue(':id', $id);
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':rol_id', $data['rol_id']);
        $statement->bindValue(':email', $data['email'] ?? null);
        $statement->bindValue(':departamento_id', $data['departamento_id'] ?? null);

        return $statement->execute();
    }

    /**
     * Desactivar usuario
     */
    public static function deactivate($id)
    {
        $statement = self::connection()->prepare("UPDATE usuarios SET activo = FALSE WHERE id = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Activar usuario
     */
    public static function activate($id)
    {
        $statement = self::connection()->prepare("UPDATE usuarios SET activo = TRUE WHERE id = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Actualizar ultimo acceso
     */
    public static function updateLastAccess($id)
    {
        $statement = self::connection()->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
        $statement->bindValue(':id', $id);
        return $statement->execute();
    }

    /**
     * Obtener todos los operadores activos
     */
    public static function getOperators()
    {
        $statement = self::connection()->prepare("
            SELECT u.id, u.username 
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE r.nombre = 'Operador' AND u.activo = TRUE
            ORDER BY u.username
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}