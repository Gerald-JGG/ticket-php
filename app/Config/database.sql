-- ============================================
-- Notas sobre el diseño:
-- ============================================
-- 1. Las contraseñas deben hashearse con password_hash() de PHP
-- 2. El campo 'activo' permite desactivar usuarios sin eliminarlos

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('Superadministrador', 'Operador', 'Usuario') NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tipo ENUM('Petición', 'Incidente') NOT NULL,
    estado ENUM('No Asignado', 'Asignado', 'En Proceso', 'En Espera de Terceros', 'Solucionado', 'Cerrado') DEFAULT 'No Asignado',
    usuario_creador_id INT NOT NULL,
    operador_asignado_id INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_asignacion TIMESTAMP NULL,
    fecha_cierre TIMESTAMP NULL,
    FOREIGN KEY (usuario_creador_id) REFERENCES usuarios(id),
    FOREIGN KEY (operador_asignado_id) REFERENCES usuarios(id),
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Entradas (Historial/Bitácora)
CREATE TABLE entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    autor_id INT NOT NULL,
    texto TEXT NOT NULL,
    estado_anterior ENUM('No Asignado', 'Asignado', 'En Proceso', 'En Espera de Terceros', 'Solucionado', 'Cerrado') NULL,
    estado_nuevo ENUM('No Asignado', 'Asignado', 'En Proceso', 'En Espera de Terceros', 'Solucionado', 'Cerrado') NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id),
);ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;