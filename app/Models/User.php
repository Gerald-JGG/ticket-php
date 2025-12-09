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
            SELECT u.id, u.nombre_completo, u.username, u.email, u.telefono, 
                   u.activo, u.fecha_creacion, u.ultimo_acceso,
                   r.nombre as rol,
                   d.nombre as departamento
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            LEFT JOIN departamentos d ON u.departamento_id = d.id
            ORDER BY u.nombre_completo
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
            SELECT u.id, u.nombre_completo, u.username, u.password, u.email, 
                   u.telefono, u.activo, u.fecha_creacion, u.ultimo_acceso,
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
            SELECT u.id, u.nombre_completo, u.username, u.password, u.email, 
                   u.telefono, u.activo, u.fecha_creacion, u.ultimo_acceso,
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
        
        // Obtener rol_id
        $rolId = self::getRolIdByName($data['rol']);
        
        // Obtener departamento_id
        $departamentoId = null;
        if (!empty($data['departamento'])) {
            $departamentoId = self::getDepartamentoIdByName($data['departamento']);
        }
        
        $statement = self::connection()->prepare("
            INSERT INTO usuarios (nombre_completo, username, password, rol_id, email, telefono, departamento_id) 
            VALUES (:nombre_completo, :username, :password, :rol_id, :email, :telefono, :departamento_id)
        ");
        
        $statement->bindValue(':nombre_completo', $data['nombre_completo']);
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':rol_id', $rolId);
        $statement->bindValue(':email', $data['email'] ?? null);
        $statement->bindValue(':telefono', $data['telefono'] ?? null);
        $statement->bindValue(':departamento_id', $departamentoId);
        
        return $statement->execute();
    }

    /**
     * Actualizar usuario existente
     */
    public static function updateUser($id, $data)
    {
        $rolId = self::getRolIdByName($data['rol']);
        
        $departamentoId = null;
        if (!empty($data['departamento'])) {
            $departamentoId = self::getDepartamentoIdByName($data['departamento']);
        }
        
        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $statement = self::connection()->prepare("
                UPDATE usuarios 
                SET nombre_completo = :nombre_completo, 
                    username = :username, 
                    password = :password,
                    rol_id = :rol_id, 
                    email = :email, 
                    telefono = :telefono, 
                    departamento_id = :departamento_id
                WHERE id = :id
            ");
            $statement->bindValue(':password', $password);
        } else {
            $statement = self::connection()->prepare("
                UPDATE usuarios 
                SET nombre_completo = :nombre_completo, 
                    username = :username, 
                    rol_id = :rol_id, 
                    email = :email, 
                    telefono = :telefono, 
                    departamento_id = :departamento_id
                WHERE id = :id
            ");
        }

        $statement->bindValue(':id', $id);
        $statement->bindValue(':nombre_completo', $data['nombre_completo']);
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':rol_id', $rolId);
        $statement->bindValue(':email', $data['email'] ?? null);
        $statement->bindValue(':telefono', $data['telefono'] ?? null);
        $statement->bindValue(':departamento_id', $departamentoId);
        
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
            SELECT u.id, u.nombre_completo, u.username 
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE r.nombre = 'Operador' AND u.activo = TRUE
            ORDER BY u.nombre_completo
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener ID del rol por nombre
     */
    private static function getRolIdByName($rolName)
    {
        $statement = self::connection()->prepare("SELECT id FROM roles WHERE nombre = :nombre");
        $statement->bindValue(':nombre', $rolName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result ? $result->id : null;
    }

    /**
     * Obtener ID del departamento por nombre
     */
    private static function getDepartamentoIdByName($departamentoName)
    {
        $statement = self::connection()->prepare("SELECT id FROM departamentos WHERE nombre = :nombre");
        $statement->bindValue(':nombre', $departamentoName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result ? $result->id : null;
    }

    /**
     * Obtener todos los roles disponibles
     */
    public static function getAllRoles()
    {
        $statement = self::connection()->prepare("SELECT * FROM roles WHERE activo = TRUE ORDER BY nivel_acceso DESC");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener todos los departamentos disponibles
     */
    public static function getAllDepartamentos()
    {
        $statement = self::connection()->prepare("SELECT * FROM departamentos WHERE activo = TRUE ORDER BY nombre");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}