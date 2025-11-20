<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/Entrada.php';
require_once __DIR__ . '/../Models/Ticket.php';

class EntradaController {
    private $db;
    private $entradaModel;
    private $ticketModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->entradaModel = new Entrada($this->db);
        $this->ticketModel = new Ticket($this->db);
    }
    
    /**
     * Agregar comentario/entrada a un ticket
     */
    public function create($data) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        // Validar datos
        if (empty($data['ticket_id']) || empty($data['texto'])) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }
        
        // Verificar que el ticket existe
        $ticket = $this->ticketModel->findById($data['ticket_id']);
        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket no encontrado'];
        }
        
        // Verificar permisos
        $rol = $_SESSION['rol'];
        $userId = $_SESSION['user_id'];
        
        if ($rol === 'Usuario' && $ticket['usuario_creador_id'] != $userId) {
            return ['success' => false, 'message' => 'No tiene permisos para comentar en este ticket'];
        }
        
        if ($rol === 'Operador' && $ticket['operador_asignado_id'] != $userId) {
            return ['success' => false, 'message' => 'No tiene permisos para comentar en este ticket'];
        }
        
        // Crear entrada
        $data['autor_id'] = $userId;
        $entradaId = $this->entradaModel->create($data);
        
        if ($entradaId) {
            return [
                'success' => true,
                'message' => 'Comentario agregado exitosamente',
                'entrada_id' => $entradaId
            ];
        }
        
        return ['success' => false, 'message' => 'Error al agregar comentario'];
    }
    
    /**
     * Obtener historial de un ticket
     */
    public function getHistorial($ticketId) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'No autorizado'];
        }
        
        $entradas = $this->entradaModel->getByTicketId($ticketId);
        return ['success' => true, 'entradas' => $entradas];
    }
}
?>