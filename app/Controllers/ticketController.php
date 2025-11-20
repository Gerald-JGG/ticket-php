<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Models/Entrada.php';

class TicketController {
    private $db;
    private $ticketModel;
    private $entradaModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ticketModel = new Ticket($this->db);
        $this->entradaModel = new Entrada($this->db);
    }
    
    /**
     * Crear un nuevo ticket (solo Usuarios)
     */
    public function create($data) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        // Verificar que sea un Usuario
        if ($_SESSION['rol'] !== 'Usuario') {
            return ['success' => false, 'message' => 'Solo los usuarios pueden crear tickets'];
        }
        
        // Validar datos
        if (empty($data['titulo']) || empty($data['tipo']) || empty($data['descripcion'])) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }
        
        // Validar longitud del título
        if (strlen($data['titulo']) > 200) {
            return ['success' => false, 'message' => 'El título no puede exceder 200 caracteres'];
        }
        
        // Validar tipo
        if (!in_array($data['tipo'], ['Petición', 'Incidente'])) {
            return ['success' => false, 'message' => 'Tipo de solicitud no válido'];
        }
        
        // Crear ticket
        $data['usuario_creador_id'] = $_SESSION['user_id'];
        $ticketId = $this->ticketModel->create($data);
        
        if ($ticketId) {
            // Crear la primera entrada (descripción inicial)
            $entradaData = [
                'ticket_id' => $ticketId,
                'autor_id' => $_SESSION['user_id'],
                'texto' => $data['descripcion'],
                'estado_anterior' => null,
                'estado_nuevo' => 'No Asignado'
            ];
            
            $this->entradaModel->create($entradaData);
            
            return [
                'success' => true,
                'message' => 'Ticket creado exitosamente',
                'ticket_id' => $ticketId
            ];
        }
        
        return ['success' => false, 'message' => 'Error al crear ticket'];
    }
    
    /**
     * Obtener mis tickets (Usuario)
     */
    public function getMisTickets($estado = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Usuario') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $tickets = $this->ticketModel->getByUsuarioCreador($_SESSION['user_id'], $estado);
        return ['success' => true, 'tickets' => $tickets];
    }
    
    /**
     * Obtener tickets no asignados (Operador)
     */
    public function getTicketsNoAsignados() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Operador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $tickets = $this->ticketModel->getNoAsignados();
        return ['success' => true, 'tickets' => $tickets];
    }
    
    /**
     * Obtener mis tickets asignados (Operador)
     */
    public function getMisTicketsAsignados($estado = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Operador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $tickets = $this->ticketModel->getByOperadorAsignado($_SESSION['user_id'], $estado);
        return ['success' => true, 'tickets' => $tickets];
    }
    
    /**
     * Autoasignar ticket (Operador)
     */
    public function autoasignar($ticketId) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Operador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        // Verificar que el ticket existe y está no asignado
        $ticket = $this->ticketModel->findById($ticketId);
        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }
        
        if ($ticket['estado'] !== 'No Asignado') {
            return ['success' => false, 'message' => 'El ticket ya está asignado'];
        }
        
        // Asignar el ticket
        if ($this->ticketModel->asignarOperador($ticketId, $_SESSION['user_id'])) {
            // Crear entrada de asignación
            $entradaData = [
                'ticket_id' => $ticketId,
                'autor_id' => $_SESSION['user_id'],
                'texto' => 'Ticket asignado',
                'estado_anterior' => 'No Asignado',
                'estado_nuevo' => 'Asignado'
            ];
            $this->entradaModel->create($entradaData);
            
            return ['success' => true, 'message' => 'Ticket asignado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al asignar ticket'];
    }
    
    /**
     * Cambiar estado del ticket
     */
    public function cambiarEstado($ticketId, $nuevoEstado, $comentario = '') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        // Obtener ticket
        $ticket = $this->ticketModel->findById($ticketId);
        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }
        
        $estadoActual = $ticket['estado'];
        
        // Validar transiciones según reglas de negocio
        $transicionValida = $this->validarTransicion($estadoActual, $nuevoEstado, $ticket);
        
        if (!$transicionValida['valida']) {
            return ['success' => false, 'message' => $transicionValida['mensaje']];
        }
        
        // Cambiar estado
        if ($this->ticketModel->cambiarEstado($ticketId, $nuevoEstado)) {
            // Crear entrada con cambio de estado
            $textoEntrada = !empty($comentario) ? $comentario : "Estado cambiado de '$estadoActual' a '$nuevoEstado'";
            
            $entradaData = [
                'ticket_id' => $ticketId,
                'autor_id' => $_SESSION['user_id'],
                'texto' => $textoEntrada,
                'estado_anterior' => $estadoActual,
                'estado_nuevo' => $nuevoEstado
            ];
            $this->entradaModel->create($entradaData);
            
            return ['success' => true, 'message' => 'Estado actualizado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al cambiar estado'];
    }
    
    /**
     * Validar transiciones de estado según reglas de negocio
     */
    private function validarTransicion($estadoActual, $nuevoEstado, $ticket) {
        $rol = $_SESSION['rol'];
        $userId = $_SESSION['user_id'];
        
        // Reglas para Operador
        if ($rol === 'Operador') {
            // Verificar que el ticket esté asignado al operador
            if ($ticket['operador_asignado_id'] != $userId) {
                return ['valida' => false, 'mensaje' => 'No puede modificar un ticket que no le está asignado'];
            }
            
            // Transiciones válidas para Operador
            $transicionesValidas = [
                'Asignado' => ['En Proceso'],
                'En Proceso' => ['En Espera de Terceros', 'Solucionado'],
                'En Espera de Terceros' => ['En Proceso']
            ];
            
            if (!isset($transicionesValidas[$estadoActual]) || 
                !in_array($nuevoEstado, $transicionesValidas[$estadoActual])) {
                return ['valida' => false, 'mensaje' => 'Transición de estado no válida'];
            }
        }
        
        // Reglas para Usuario
        if ($rol === 'Usuario') {
            // Solo puede aceptar o rechazar cuando está Solucionado
            if ($estadoActual !== 'Solucionado') {
                return ['valida' => false, 'mensaje' => 'Solo puede responder cuando el ticket está solucionado'];
            }
            
            // Verificar que sea el creador del ticket
            if ($ticket['usuario_creador_id'] != $userId) {
                return ['valida' => false, 'mensaje' => 'No puede modificar un ticket que no creó'];
            }
            
            // Solo puede cambiar a Cerrado o Asignado
            if (!in_array($nuevoEstado, ['Cerrado', 'Asignado'])) {
                return ['valida' => false, 'mensaje' => 'Transición no válida'];
            }
        }
        
        return ['valida' => true];
    }
    
    /**
     * Obtener detalle del ticket con historial
     */
    public function getDetalle($ticketId) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $ticket = $this->ticketModel->findById($ticketId);
        
        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }
        
        // Verificar permisos según rol
        if ($_SESSION['rol'] === 'Usuario' && $ticket['usuario_creador_id'] != $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'No tiene permisos para ver este ticket'];
        }
        
        if ($_SESSION['rol'] === 'Operador' && 
            $ticket['operador_asignado_id'] != $_SESSION['user_id'] && 
            $ticket['estado'] !== 'No Asignado') {
            return ['success' => false, 'message' => 'No tiene permisos para ver este ticket'];
        }
        
        // Obtener historial
        $entradas = $this->entradaModel->getByTicketId($ticketId);
        
        return [
            'success' => true,
            'ticket' => $ticket,
            'entradas' => $entradas
        ];
    }
    
    /**
     * Obtener todos los tickets (Superadministrador)
     */
    public function getAll($filtros = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Superadministrador') {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $tickets = $this->ticketModel->getAll($filtros);
        return ['success' => true, 'tickets' => $tickets];
    }
    
    /**
     * Obtener estadísticas
     */
    public function getEstadisticas() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $usuarioId = $_SESSION['user_id'];
        $rol = $_SESSION['rol'];
        
        // Superadministrador ve todas las estadísticas
        if ($rol === 'Superadministrador') {
            $usuarioId = null;
            $rol = null;
        }
        
        $estadisticas = $this->ticketModel->getEstadisticas($usuarioId, $rol);
        return ['success' => true, 'estadisticas' => $estadisticas];
    }
}