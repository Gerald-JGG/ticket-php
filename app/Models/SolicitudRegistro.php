<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SolicitudRegistro extends Model
{
    /**
     * Obtener todas las solicitudes
     */
    public static function all()
    {
        $statement = self::connection()->prepare("
            SELECT s.*, 
                   u.username as respondido_por_nombre
            FROM solicitudes_registro s
            LEFT JOIN usuarios u ON s.respondido_por = u.id
            ORDER BY 
                CASE s.estado 
                    WHEN 'Pendiente' THEN 1 
                    WHEN 'Aprobada' THEN 2 
                    WHEN 'Rechazada' THEN 3 
                END,
                s.fecha_solicitud DESC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Obtener solicitudes pendientes
     */
    public static function getPendientes()
    {
        $statement = self::connection()->prepare("
            SELECT * FROM solicitudes_registro 
            WHERE estado = 'Pendiente'
            ORDER BY fecha_solicitud ASC
        ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar solicitud por ID
     */
    public static function find($id)
    {
        $statement = self::connection()->prepare("
            SELECT s.*, 
                   u.username as respondido_por_nombre
            FROM solicitudes_registro s
            LEFT JOIN usuarios u ON s.respondido_por = u.id
            WHERE s.id = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nueva solicitud
     */
    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO solicitudes_registro (
                nombre_completo, 
                email, 
                username_solicitado, 
                departamento_solicitado, 
                telefono, 
                motivo
            ) 
            VALUES (
                :nombre_completo, 
                :email, 
                :username_solicitado, 
                :departamento_solicitado, 
                :telefono, 
                :motivo
            )
        ");
        
        $statement->bindValue(':nombre_completo', $data['nombre_completo']);
        $statement->bindValue(':email', $data['email']);
        $statement->bindValue(':username_solicitado', $data['username_solicitado']);
        $statement->bindValue(':departamento_solicitado', $data['departamento_solicitado'] ?? null);
        $statement->bindValue(':telefono', $data['telefono'] ?? null);
        $statement->bindValue(':motivo', $data['motivo']);
        
        return $statement->execute();
    }

    /**
     * Aprobar solicitud
     */
    public static function aprobar($id, $adminId, $comentario = null)
    {
        $statement = self::connection()->prepare("
            UPDATE solicitudes_registro 
            SET estado = 'Aprobada',
                fecha_respuesta = NOW(),
                respondido_por = :admin_id,
                comentario_respuesta = :comentario
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':admin_id', $adminId);
        $statement->bindValue(':comentario', $comentario);
        
        return $statement->execute();
    }

    /**
     * Rechazar solicitud
     */
    public static function rechazar($id, $adminId, $comentario)
    {
        $statement = self::connection()->prepare("
            UPDATE solicitudes_registro 
            SET estado = 'Rechazada',
                fecha_respuesta = NOW(),
                respondido_por = :admin_id,
                comentario_respuesta = :comentario
            WHERE id = :id
        ");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':admin_id', $adminId);
        $statement->bindValue(':comentario', $comentario);
        
        return $statement->execute();
    }

    /**
     * Verificar si un email ya tiene solicitud pendiente
     */
    public static function hasPendingRequest($email)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM solicitudes_registro 
            WHERE email = :email 
            AND estado = 'Pendiente'
        ");
        $statement->bindValue(':email', $email);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total > 0;
    }

    /**
     * Verificar si un username ya está solicitado
     */
    public static function isUsernameTaken($username)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM solicitudes_registro 
            WHERE username_solicitado = :username 
            AND estado = 'Pendiente'
        ");
        $statement->bindValue(':username', $username);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total > 0;
    }

    /**
     * Contar solicitudes por estado
     */
    public static function countByStatus($estado)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM solicitudes_registro 
            WHERE estado = :estado
        ");
        $statement->bindValue(':estado', $estado);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
}