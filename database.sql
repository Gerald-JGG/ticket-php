-- Tabla 1: Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    nivel_acceso INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 2: Departamentos
CREATE TABLE departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 3: Estados
CREATE TABLE estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 4: Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    email VARCHAR(100) NULL,
    departamento_id INT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 5: Categorías de Tickets
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    color VARCHAR(7) NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 6: Prioridades
CREATE TABLE prioridades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    nivel INT NOT NULL,
    tiempo_respuesta_horas INT NULL,
    color VARCHAR(7) NULL,
    descripcion TEXT NULL,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 7: Tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tipo ENUM('Petición', 'Incidente') NOT NULL,
    estado_id INT NOT NULL,
    prioridad_id INT NULL,
    categoria_id INT NULL,
    usuario_creador_id INT NOT NULL,
    operador_asignado_id INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_asignacion TIMESTAMP NULL,
    fecha_cierre TIMESTAMP NULL,
    FOREIGN KEY (estado_id) REFERENCES estados(id),
    FOREIGN KEY (usuario_creador_id) REFERENCES usuarios(id),
    FOREIGN KEY (operador_asignado_id) REFERENCES usuarios(id),
    FOREIGN KEY (prioridad_id) REFERENCES prioridades(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 8: Entradas (Historial/Bitácora)
CREATE TABLE entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    autor_id INT NOT NULL,
    texto TEXT NOT NULL,
    estado_anterior_id INT NULL,
    estado_nuevo_id INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id),
    FOREIGN KEY (estado_anterior_id) REFERENCES estados(id),
    FOREIGN KEY (estado_nuevo_id) REFERENCES estados(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 9: Para Imagenes de Perfil de Usuarios
CREATE TABLE imagenes_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_guardado VARCHAR(255) NOT NULL,
    ruta VARCHAR(500) NOT NULL,
    tipo_mime VARCHAR(100) NOT NULL,
    tamano_bytes INT NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla 10: Para Solicitudes de Registro de Usuarios
CREATE TABLE solicitudes_registro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    email VARCHAR(100) NOT NULL,
    username_solicitado VARCHAR(50) NOT NULL,
    departamento_solicitado VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    motivo TEXT NOT NULL,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada') DEFAULT 'Pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta TIMESTAMP NULL,
    respondido_por INT NULL,
    comentario_respuesta TEXT NULL,
    FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1. INSERTAR ROLES
INSERT INTO roles (nombre, descripcion, nivel_acceso) VALUES
('Superadministrador', 'Acceso completo al sistema, gestión de usuarios y configuración', 100),
('Operador', 'Personal de soporte técnico que atiende tickets', 50),
('Usuario', 'Usuario final que crea tickets de soporte', 10);

-- 2. INSERTAR DEPARTAMENTOS
INSERT INTO departamentos (nombre, descripcion) VALUES
('Tecnologías de Información', 'Departamento de TI y soporte técnico'),
('Recursos Humanos', 'Gestión de personal y nómina'),
('Contabilidad', 'Departamento financiero y contable'),
('Ventas', 'Equipo comercial y ventas'),
('Operaciones', 'Operaciones y logística');

-- 3. INSERTAR ESTADOS
INSERT INTO estados (nombre, descripcion) VALUES
('No Asignado', 'Ticket recién creado, esperando asignación'),
('Asignado', 'Ticket asignado a un operador'),
('En Proceso', 'Operador trabajando en la solución'),
('En Espera de Terceros', 'Esperando información adicional'),
('Solucionado', 'Solución propuesta por el operador'),
('Cerrado', 'Ticket cerrado y aceptado por el usuario');

INSERT INTO usuarios (username, password, rol_id, email, departamento_id) VALUES
('admin', '$2y$12$NPht9L5H8gtGxQZwIwZot.S2XACFngICHbkk6NEGBuocPqC5PoDFq', 1, 'admin@lospatitos.com', 1);

INSERT INTO categorias (nombre, descripcion, color) VALUES
('Soporte Técnico', 'Problemas relacionados con computadoras, rendimiento y fallos generales.', '#007bff'),
('Redes', 'Conectividad, WiFi, routers, VPN y problemas de red.', '#28a745'),
('Hardware', 'Fallas en equipos físicos: monitores, teclados, impresoras.', '#ffc107'),
('Software', 'Errores, instalación o actualización de programas.', '#17a2b8'),
('Accesos y Permisos', 'Solicitudes de creacion de usuarios y permisos.', '#6610f2'),
('Administrativo', 'Solicitudes internas relacionadas con procesos administrativos.', '#fd7e14'),
('Seguridad', 'Incidentes de seguridad, antivirus, accesos no autorizados.', '#dc3545');

INSERT INTO prioridades (nombre, nivel, tiempo_respuesta_horas, color, descripcion) VALUES
('Crítica', 1, 1, '#dc3545', 'Interrupción total del servicio o incidente grave que requiere atención inmediata.'),
('Alta', 2, 4, '#fd7e14', 'Problema serio que afecta el trabajo pero no detiene todo el servicio.'),
('Media', 3, 12, '#ffc107', 'Problema manejable o consulta técnica que afecta parcialmente al usuario.'),
('Baja', 4, 24, '#28a745', 'Solicitudes menores o preguntas que no requieren atención urgente.');

ALTER TABLE estados ADD COLUMN color VARCHAR(7) NULL AFTER descripcion;

-- Actualizar los colores
UPDATE estados SET color = '#6c757d' WHERE nombre = 'No Asignado';
UPDATE estados SET color = '#3b82f6' WHERE nombre = 'Asignado';
UPDATE estados SET color = '#f59e0b' WHERE nombre = 'En Proceso';
UPDATE estados SET color = '#9333ea' WHERE nombre = 'En Espera de Terceros';
UPDATE estados SET color = '#10b981' WHERE nombre = 'Solucionado';
UPDATE estados SET color = '#374151' WHERE nombre = 'Cerrado';

ALTER TABLE estados 
CHANGE estado_color color VARCHAR(7) NULL;

UPDATE prioridades 
SET color = '#8b2e31'
WHERE nombre = 'Crítica';

UPDATE prioridades 
SET color = '#a65f2a'
WHERE nombre = 'Alta';

UPDATE prioridades 
SET color = '#b59b32'
WHERE nombre = 'Media';

UPDATE prioridades 
SET color = '#2d6a3a'
WHERE nombre = 'Baja';


UPDATE categorias SET color = '#0056b3' WHERE nombre = 'Soporte Técnico';
UPDATE categorias SET color = '#1f7a33' WHERE nombre = 'Redes';
UPDATE categorias SET color = '#b59b32' WHERE nombre = 'Hardware';
UPDATE categorias SET color = '#117a88' WHERE nombre = 'Software';
UPDATE categorias SET color = '#4b2e8c' WHERE nombre = 'Accesos y Permisos';
UPDATE categorias SET color = '#a66319' WHERE nombre = 'Administrativo';
UPDATE categorias SET color = '#8b2e31' WHERE nombre = 'Seguridad';
