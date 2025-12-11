<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class ImagenPerfil extends Model
{
    /**
     * Obtener imagen de perfil de un usuario
     */
    public static function findByUserId($userId)
    {
        $statement = self::connection()->prepare("
            SELECT * FROM imagenes_perfil 
            WHERE usuario_id = :usuario_id
        ");
        $statement->bindValue(':usuario_id', $userId);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Crear nueva imagen de perfil
     */
    public static function create($data)
    {
        $statement = self::connection()->prepare("
            INSERT INTO imagenes_perfil (
                usuario_id, 
                nombre_original, 
                nombre_guardado, 
                ruta, 
                tipo_mime, 
                tamano_bytes
            ) 
            VALUES (
                :usuario_id, 
                :nombre_original, 
                :nombre_guardado, 
                :ruta, 
                :tipo_mime, 
                :tamano_bytes
            )
        ");
        
        $statement->bindValue(':usuario_id', $data['usuario_id']);
        $statement->bindValue(':nombre_original', $data['nombre_original']);
        $statement->bindValue(':nombre_guardado', $data['nombre_guardado']);
        $statement->bindValue(':ruta', $data['ruta']);
        $statement->bindValue(':tipo_mime', $data['tipo_mime']);
        $statement->bindValue(':tamano_bytes', $data['tamano_bytes']);
        
        return $statement->execute();
    }

    /**
     * Actualizar imagen de perfil existente
     */
    public static function update($userId, $data)
    {
        $statement = self::connection()->prepare("
            UPDATE imagenes_perfil 
            SET nombre_original = :nombre_original, 
                nombre_guardado = :nombre_guardado, 
                ruta = :ruta, 
                tipo_mime = :tipo_mime, 
                tamano_bytes = :tamano_bytes
            WHERE usuario_id = :usuario_id
        ");
        
        $statement->bindValue(':usuario_id', $userId);
        $statement->bindValue(':nombre_original', $data['nombre_original']);
        $statement->bindValue(':nombre_guardado', $data['nombre_guardado']);
        $statement->bindValue(':ruta', $data['ruta']);
        $statement->bindValue(':tipo_mime', $data['tipo_mime']);
        $statement->bindValue(':tamano_bytes', $data['tamano_bytes']);
        
        return $statement->execute();
    }

    /**
     * Eliminar imagen de perfil
     */
    public static function delete($userId)
    {
        // Primero obtenemos la ruta del archivo para eliminarlo físicamente
        $imagen = self::findByUserId($userId);
        
        if ($imagen && file_exists($imagen->ruta)) {
            unlink($imagen->ruta);
        }
        
        $statement = self::connection()->prepare("
            DELETE FROM imagenes_perfil 
            WHERE usuario_id = :usuario_id
        ");
        $statement->bindValue(':usuario_id', $userId);
        return $statement->execute();
    }

    /**
     * Verificar si un usuario tiene imagen de perfil
     */
    public static function hasProfileImage($userId)
    {
        $statement = self::connection()->prepare("
            SELECT COUNT(*) as total 
            FROM imagenes_perfil 
            WHERE usuario_id = :usuario_id
        ");
        $statement->bindValue(':usuario_id', $userId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result->total > 0;
    }

    /**
     * Obtener URL de imagen de perfil o imagen por defecto
     */
    public static function getProfileImageUrl($userId)
    {
        $imagen = self::findByUserId($userId);
        
        if ($imagen) {
            return '/' . $imagen->ruta;
        }
        
        // Retornar imagen por defecto si no existe
        return '/img/default-avatar.png';
    }
}